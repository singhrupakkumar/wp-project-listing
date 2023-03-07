import ToolbarLayout from '../../components/toolbar-layout';
import ToolbarAlignment from '../../components/toolbar-alignment';

import FeatureDisabled from '../../components/feature-disabled-placeholder';

import CitadelaRangeControl  from '../../components/range-control';
import CustomColorControl from '../../components/custom-color-control';

//const { apiFetch } = wp;
const { __ } = wp.i18n;
const { Component } = wp.element;
const { InspectorControls, BlockControls, RichText } = wp.blockEditor;
const { PanelBody, ToolbarGroup, ToolbarItem, Icon, ToggleControl, SelectControl } = wp.components;

export class Edit extends Component {
    constructor() {
        super( ...arguments );
        
        this.updateSelectedInputs = this.updateSelectedInputs.bind(this);
        this.cleanSelectedInputs = this.cleanSelectedInputs.bind(this);
        
        this.item_extension_options = CitadelaDirectorySettings.options.item_extension;

    }

    componentDidMount() {
       this.cleanSelectedInputs();
    }

    // clean selected inputs and make sure the selection include only inputs which are really available in Item Extension settings
    // there may be saved selected inputs which were deleted in Item Extension settings
    cleanSelectedInputs() {
        const { item_extension_options } = this;
        const { attributes, setAttributes } = this.props;
        
        if( _.isEmpty( item_extension_options ) ) return;

        // inputs which are already available in Item Extension settings
        const available_inputs = Object.keys( item_extension_options.inputs_group.inputs ).map( ( input_name, index ) => {
            return input_name;
        } );

        // loopo through selected inputs and remove these which are not avialable in Item Extension settings.
        let selected_inputs = [];
        attributes.selected_inputs.map( ( node ) => {
            const input_name = node;
            if( available_inputs.includes( input_name ) ) {
                selected_inputs.push( input_name );
            }
        } );
        setAttributes( { selected_inputs: selected_inputs } );

    }
    updateSelectedInputs( input_name, checked ) {
        const { attributes, setAttributes } = this.props;
        // grab already selected inputs into array
        let selected_inputs = attributes.selected_inputs.map( ( node ) => {
            return node;
        } );

        // update array with already clicked input
        if( checked ){
            selected_inputs.push( input_name );
        }else{
            const index = selected_inputs.indexOf( input_name );
            if ( index > -1) {
              selected_inputs.splice(index, 1);
            }
        }

        setAttributes( { selected_inputs: selected_inputs } );
    }

    render() {
        const activeProPlugin = typeof CitadelaProSettings === "undefined" ? false : true;
        const { item_extension_options } = this;
        const { attributes, setAttributes, name } = this.props;
        const {
            title,
            layout,
            align,
            list_table_style,
            label_width,
            box_width,
            fix_box_width,
            hide_empty,
            hide_empty_checkbox,
            show_data,
            selected_inputs,

            title_color,
            lines_color,
            labels_color,
            values_color,
        } = attributes;

        const block = wp.blocks.getBlockType(name);
        const loaded_options = _.isEmpty( item_extension_options ) ? false : true;

        const enabled = loaded_options ? item_extension_options.enable : false;
        const inputs = loaded_options ? item_extension_options.inputs_group.inputs : {};
        
        const title_color_style = {
            ...( activeProPlugin && title_color ? { color: `${title_color}` } : false ),
        };

        const label_styles = {
            ...( layout == 'list' && label_width ? { width: `${label_width}px` } : false ),
            ...( activeProPlugin && labels_color ? { color: `${labels_color}` } : false ),
        };

        const value_styles = {
            ...( activeProPlugin && values_color ? { color: `${values_color}` } : false ),
        };

        const wrapper_styles = {
            ...( layout == 'box' && box_width ? { flexBasis: `${box_width}px` } : false ),
            ...( activeProPlugin && layout != 'text' && lines_color ? { borderColor: `${lines_color}` } : false ),
        };

        const contentTemplate = <div class="data-row" style={ wrapper_styles ? wrapper_styles : false }>
                                    <div class="label" style={ label_styles ? label_styles : false }></div>
                                    <div class="data" style={ value_styles ? value_styles : false }></div>
                                    { layout == 'text' && <span class="sep" style={ title_color_style ? title_color_style : false }></span> }
                                </div>;
        return (
            <>
            
            <BlockControls key='controls'>
                <ToolbarGroup>
                    <ToolbarItem>
                        { ( toggleProps ) => (
                            <ToolbarAlignment 
                                value={ align } 
                                onChange={ ( value ) => ( setAttributes( { align: value } ) ) }
                                leftLabel={ __( 'Align Left', 'citadela-directory' ) }    
                                centerLabel={ __( 'Align Center', 'citadela-directory' ) }    
                                rightLabel={ __( 'Align Right', 'citadela-directory' ) }    
                                toggleProps={ toggleProps }
                            />
                        )}
                    </ToolbarItem>
                    <ToolbarItem>
                        { ( toggleProps ) => (
                            <ToolbarLayout 
                                allowedLayouts={ ['text', 'list', 'box'] } 
                                value={ layout } 
                                onChange={ ( value ) => setAttributes( { layout: value } ) } 
                                toggleProps={ toggleProps } 
                            />
                        )}
                    </ToolbarItem>
                </ToolbarGroup>
               
                
            </BlockControls>

            <InspectorControls>
                
                <PanelBody 
                    title={ __('Show data', 'citadela-directory') }
                    initialOpen={true}
                    className="citadela-panel"
                >
                    <SelectControl
                        label={__('Show data', 'citadela-directory')}
                        value={ show_data }
                        options={ [
                            { label: __( 'All inputs', 'citadela-directory' ), value: 'all' },
                            { label: __( 'Show selected inputs', 'citadela-directory' ), value: 'selected' },
                            { label: __( 'Hide selected inputs', 'citadela-directory' ), value: 'hide_selected' },
                        ] }
                        onChange={ ( value ) => { setAttributes( { show_data: value } ) } }
                    />
                    
                    { show_data !== 'all' && 
                        Object.keys( inputs ).map( ( input_name, index ) => {
                            const selected = selected_inputs.includes( input_name );
                            return <ToggleControl
                                label={ inputs[input_name]['label'] }
                                checked={ selected }
                                onChange={ ( checked ) => { this.updateSelectedInputs( input_name, checked ) } }
                            />
                        } )
                    }

                </PanelBody>

                <PanelBody 
                    title={ __('Options', 'citadela-directory') }
                    initialOpen={false}
                    className="citadela-panel"
                >
                   <ToggleControl
                        label={__('Hide empty values', 'citadela-directory')}
                        help={__('Data of inputs with no value will not be displayed.', 'citadela-directory')}
                        checked={ hide_empty }
                        onChange={ ( checked ) => setAttributes( { hide_empty: checked } ) }
                    />
                    
                    <ToggleControl
                        label={__('Hide unchecked checkbox', 'citadela-directory')}
                        help={__('Unchecked single checkbox will not be displayed.', 'citadela-directory')}
                        checked={ hide_empty_checkbox }
                        onChange={ ( checked ) => setAttributes( { hide_empty_checkbox: checked } ) }
                    />
                    
                    { layout == 'list' && 
                        <>
                        <CitadelaRangeControl
                            label={ __('Label width', 'citadela-directory') }
                            rangeValue={ label_width }
                            onChange={ ( value ) => { setAttributes( { label_width: value } ); } }
                            min={ 150 }
                            max={ 500 }
                            allowReset
                            allowNoValue
                        />
                        <ToggleControl
                            label={__('Table style', 'citadela-directory')}
                            help={__('Force table style for displayed data, longer values are not moved under label.', 'citadela-directory')}
                            checked={ list_table_style }
                            onChange={ ( checked ) => setAttributes( { list_table_style: checked } ) }
                        />
                        </>
                    }

                    { layout == 'box' && 
                        <>
                        <CitadelaRangeControl
                            label={ __('Box width', 'citadela-directory') }
                            rangeValue={ box_width }
                            onChange={ ( value ) => { setAttributes( { box_width: value } ); } }
                            min={ 150 }
                            max={ 500 }
                            initial={ 200 }
                            allowReset
                        />
                        <ToggleControl
                            label={__('Fix width', 'citadela-directory')}
                            help={__('Force box width to selected value.', 'citadela-directory')}
                            checked={ fix_box_width }
                            onChange={ ( checked ) => setAttributes( { fix_box_width: checked } ) }
                        />
                        </>
                    }
                    
                </PanelBody>
                
                { activeProPlugin &&
                    <PanelBody
                        title={__('Design Options', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >

                        <CustomColorControl 
                            label={ __('Title color', 'citadela-directory') }
                            color={ title_color }
                            onChange={ (value) => { setAttributes( { title_color: value } ); } }
                        />

                        <CustomColorControl 
                            label={ __('Label color', 'citadela-directory') }
                            color={ labels_color }
                            onChange={ (value) => { setAttributes( { labels_color: value } ); } }
                        />

                        <CustomColorControl 
                            label={ __('Value color', 'citadela-directory') }
                            color={ values_color }
                            onChange={ (value) => { setAttributes( { values_color: value } ); } }
                        />

                        { layout != 'text' &&
                            <CustomColorControl 
                                label={ __('Lines color', 'citadela-directory') }
                                color={ lines_color }
                                onChange={ (value) => { setAttributes( { lines_color: value } ); } }
                            />
                        }

                    </PanelBody>
                }
            </InspectorControls>
            
            <div className={ classNames(
                "wp-block-citadela-blocks",
                "ctdl-item-extension",
                attributes.className,
                `${layout}-layout`,
                `align-${align}`,
                layout == 'list' && label_width ? 'custom-label-width' : null,
                layout == 'list' && list_table_style ? 'table-style' : null,
                activeProPlugin && layout != 'text' && lines_color ? 'custom-lines-color' : null,
                activeProPlugin && title_color ? 'custom-title-color' : null,
                activeProPlugin && labels_color ? 'custom-label-color' : null,
                activeProPlugin && values_color ? 'custom-value-color' : null,
                fix_box_width ? 'fix-width' : null,
                ! enabled ? 'feature-disabled' : null,
            )}>
                <div class="ctdl-blockcard-title">
                    <div class="ctdl-blockcard-icon">
                        <Icon icon={block.icon.src} />
                    </div>
                    <div class="ctdl-blockcard-text">
                        <div class="ctdl-blockcard-name">{ block.title }</div>
                        <div class="ctdl-blockcard-desc">{ block.description }</div>
                    </div>
                </div>

                { enabled ?
                    <>
                        <div class="citadela-block-header">
                            <RichText
                                tagName='h3'
                                value={ title }
                                onChange={ (title) => setAttributes( { title } ) }
                                placeholder={ block.title }
                                keepPlaceholderOnFocus={true}
                                allowedFormats={ [] }
                                style={ title_color_style ? title_color_style : false }
                            />
                        </div>

                        <div class="citadela-block-articles">
                            <div class="citadela-block-articles-wrap">
                                
                                { show_data == 'all' &&
                                    <>
                                    { _.size( inputs ) == 0 
                                        ? __('There are no inputs to show.', 'citadela-directory')
                                        : Array.from(Array( _.size( inputs ) ), (e, i) => {
                                            return contentTemplate;
                                        })
                                    }
                                    </>
                                }

                                { show_data == 'selected' &&
                                    <>
                                    { _.size( selected_inputs ) == 0
                                        ? __('There are no selected inputs to show.', 'citadela-directory')
                                        : Array.from(Array( _.size( selected_inputs ) ), (e, i) => {
                                            return contentTemplate;
                                        })
                                    }
                                    </>
                                }

                                { show_data == 'hide_selected' &&
                                    <>
                                    { _.size( selected_inputs ) == _.size( inputs )
                                        ? __('There are no selected inputs to show.', 'citadela-directory')
                                        : Array.from(Array( _.size( inputs ) - _.size( selected_inputs ) ), (e, i) => {
                                            return contentTemplate;
                                        })
                                    }
                                    </>
                                }
                            </div>
                        </div>
                    </>
                    :
                    <FeatureDisabled
                        description={ __('Enable and insert inputs via menu', 'citadela-directory') }
                        pathText={ __('Citadela Listing > Item Extension', 'citadela-directory') }

                    />
                }

            </div>
            </>
        );
    }
}

export default Edit;