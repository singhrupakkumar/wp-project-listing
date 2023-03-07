//import { chevronLeft, chevronRight, chevronUp, chevronDown } from '@wordpress/icons';

import ToolbarLayout from '../../components/toolbar-layout';
import ToolbarAlignment from '../../components/toolbar-alignment';

import FeatureDisabled from '../../components/feature-disabled-placeholder';

import CitadelaRangeControl  from '../../components/range-control';
import CustomColorControl from '../../components/custom-color-control';

//const { apiFetch } = wp;
const { __ } = wp.i18n;
const { Component } = wp.element;
const { InspectorControls, BlockControls, RichText } = wp.blockEditor;
const { PanelBody, ToolbarGroup, ToolbarItem, Icon, ToggleControl, SelectControl, BaseControl, RadioControl, Button, ButtonGroup } = wp.components;

export class Edit extends Component {
    constructor() {
        super( ...arguments );
        
        this.updateSelectedInputs = this.updateSelectedInputs.bind(this);
        this.onMove = this.onMove.bind(this);
        this.onMoveBackward = this.onMoveBackward.bind(this);
        this.onMoveForward = this.onMoveForward.bind(this);
        this.getCurrentOrder = this.getCurrentOrder.bind(this);
        this.checkAvailableInput = this.checkAvailableInput.bind(this);
        this.onClickGroup = this.onClickGroup.bind(this);
        this.updateFiltersOperator = this.updateFiltersOperator.bind(this);
        
        this.item_extension_options = CitadelaDirectorySettings.options.item_extension;
        this.filtered_data = [];
        this.available_inputs = [];
        this.state = {
            selected_group: '',
        };
    }
    componentDidMount() {
        const { attributes, setAttributes } = this.props;
        if( ! _.isEqual( attributes.active_filter_groups, Object.keys( this.filtered_data ) ) ){
            setAttributes( { active_filter_groups: Object.keys( this.filtered_data ) } );
        }

        //disable submit button if filters are inside search form 
        if( attributes.show_submit_button && attributes.in_search_form ){
            setAttributes( { show_submit_button: false } );
        }

    }
    componentDidUpdate( prevProps, prevState ) {
        // refer to condition https://reactjs.org/docs/react-component.html#componentdidupdate
        if( prevProps.isSelected != this.props.isSelected && ! this.props.isSelected ){
            this.setState( { selected_group: '' } );
        }
        
        
        /*
        // update already visible filter groups
        if(     prevProps.attributes.show_data != this.props.attributes.show_data 
            ||  prevProps.attributes.selected_inputs != this.props.attributes.selected_inputs 
        ) {
            this.props.setAttributes( { active_filter_groups: Object.keys( this.filtered_data ) } );
        }*/


        // update already visible filter groups
        if( !_.isEqual( prevProps.attributes.active_filter_groups, Object.keys( this.filtered_data ) ) ) {
           this.props.setAttributes( { active_filter_groups: Object.keys( this.filtered_data ) } );
        }
    }
    
    onClickGroup( group_key ){
        this.setState( { selected_group: group_key} );
    }


    checkAvailableInput( input_name ){
        const { selected_inputs } = this.props.attributes;
        return selected_inputs.includes( input_name ) && this.available_inputs.includes( input_name );
    }

    updateFiltersOperator( group_name, new_operator ){
        const { attributes, setAttributes } = this.props;
        const { filter_operators } = attributes;
        let new_filter_operators = {...filter_operators};

        new_filter_operators[group_name] = new_operator;
        setAttributes( { filter_operators: new_filter_operators } );
    }

    updateSelectedInputs( input_name, checked, related_filter_group ) {
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
        setAttributes( { 
            selected_inputs: selected_inputs 
        } );
    }

    getCurrentOrder(){
        let filters_order = [];
        Object.keys( this.filtered_data ).map( ( order_key, i ) => {
            filters_order[i] = order_key;
        });
        return Object.values( filters_order );
    }
    
    onMove( oldIndex, newIndex ) {
        const { setAttributes } = this.props;
        const current_order = this.getCurrentOrder();
        let new_order = [...current_order];
		new_order.splice( newIndex, 1, current_order[ oldIndex ] );
        new_order.splice( oldIndex, 1, current_order[ newIndex ] );
		setAttributes( { filters_order: Object.values( new_order ) } );
	}

	onMoveForward( oldIndex ) {
        if ( oldIndex === Object.keys( this.getCurrentOrder() ).length - 1 ) {
            return;
        }
        this.onMove( oldIndex, oldIndex + 1 );
	}

	onMoveBackward( oldIndex ) {
        if ( oldIndex === 0 ) {
            return;
        }
        this.onMove( oldIndex, oldIndex - 1 );
    }
    
    render() {
        const activeProPlugin = typeof CitadelaProSettings === "undefined" ? false : true;
        const { item_extension_options } = this;
        const { attributes, setAttributes, name, isSelected } = this.props;
        const {
            in_search_form,
            advanced_header,
            advanced_header_opened,
            advanced_header_closed_on_mobile,
            header_text_color,
            header_bg_color,
            header_border_color,
            header_border_type,
            show_group_title,
            filter_operators,
            filters_order,
            show_data,
            title,
            layout,
            align,
            box_width,
            title_color,
            lines_color,
            lines_type,
            labels_color,
            data_row_bg_color,
            show_submit_button,
            button_text,
            button_style,
            button_text_color,
            button_bg_color,
            button_border_radius,
        } = attributes;

        const { selected_group } = this.state;
        
        const block = wp.blocks.getBlockType(name);
        
        const loaded_options = Object.keys( item_extension_options ).length == 0 ? false : true;

        const enabled = loaded_options ? item_extension_options.enable : false;

        let item_extension_inputs = loaded_options ? item_extension_options.inputs_group.inputs : {};

        this.available_inputs = Object.keys( item_extension_inputs ).map( ( input_name, index ) => {
            return input_name;
        } );

        let checkbox_filter_group = [];
        let selection_filter_group = [];
        
        //get only Filters inputs
        Object.keys( item_extension_inputs ).map( ( input_name, index ) => {
            if( item_extension_inputs[input_name]['use_as_filter'] ){
                let input_data = item_extension_inputs[input_name];
                
                if( input_data['type'] == 'checkbox' ){
                    // merge checkboxes into groups
                    const checkbox_filters_group_name = ( typeof input_data['checkbox_filters_group_name'] == "undefined" || input_data['checkbox_filters_group_name'] == '' ) ? __( 'Filters', 'citadela-directory' ) : input_data['checkbox_filters_group_name'];
                    if( typeof checkbox_filter_group[checkbox_filters_group_name] == "undefined" ) checkbox_filter_group[checkbox_filters_group_name] = [];
                    checkbox_filter_group[checkbox_filters_group_name][input_name] = input_data;
                }else if( input_data['type'] == 'select' || input_data['type'] == 'citadela_multiselect' ){
                    // all other filters
                    selection_filter_group[input_name] = input_data;
                }
            }
        } );
        
        
        this.filtered_data = [];
        // grouped checkboxes
        Object.keys( checkbox_filter_group ).map( ( group_name, index ) => { 
            let data = [];
            data['filters_group_name'] = group_name;
            data['filters_group_type'] = 'checkbox';
            data['filters'] = [];
            Object.keys( checkbox_filter_group[group_name] ).map( ( input_name, index ) => { 
                let use_input = false;
                if( show_data == 'all' ){
                    use_input = true;
                }else if( show_data == 'selected' ){
                    if( this.checkAvailableInput( input_name ) ){
                        use_input = true;
                    }
                }else if( show_data == 'hide_selected' ){
                    if( ! this.checkAvailableInput( input_name ) ){
                        use_input = true;
                    }
                }

                if( use_input ){
                    data['filters'][input_name] = [];
                    data['filters'][input_name]['key'] = input_name;
                    data['filters'][input_name]['label'] = checkbox_filter_group[group_name][input_name]['label'];
                }
            });

            const filter_key = data['filters_group_name'] + "_" + data['filters_group_type'];

            if( Object.keys( data['filters'] ).length ) {
                this.filtered_data[ filter_key ] = data;
            }
        });

        // other inputs
        Object.keys( selection_filter_group ).map( ( input_name, index ) => { 
            let data = [];
            data['filters_group_name'] = selection_filter_group[input_name]['label'];
            data['filters_group_type'] = selection_filter_group[input_name]['type'];
            // even there is always just one record, we store it as array to follow data structure of grouped checkboxes
            data['filters'] = [];

            let use_input = false;
            if( show_data == 'all' ){
                use_input = true;
            }else if( show_data == 'selected' ){
                if( this.checkAvailableInput( input_name ) ){
                    use_input = true;
                }
            }else if( show_data == 'hide_selected' ){
                if( ! this.checkAvailableInput( input_name ) ){
                    use_input = true;
                }
            }
            if( use_input ){
                Object.keys( selection_filter_group[input_name]['choices'] ).map( ( choice_key, index ) => { 
                    data['filters'][choice_key] = [];
                    data['filters'][choice_key]['key'] = choice_key;
                    data['filters'][choice_key]['label'] = selection_filter_group[input_name]['choices'][choice_key];
                });
            }
            
            const filter_key = input_name + "_" + data['filters_group_type'];
            
            if( Object.keys( data['filters'] ).length ) {
                this.filtered_data[ filter_key ] = data;
            }
        });

        if( Object.keys( filters_order ).length ){
            
            // if is defined order, reorder filtered data
            let original_data = this.filtered_data;
            let filtered_data = [];
            Object.keys( filters_order ).map( ( i ) => {
                //check if we are going to use this filter, may be disabled from side options, thus is undefined
                if( typeof original_data[ filters_order[i] ] != 'undefined' ){
                    filtered_data[filters_order[i]] = original_data[ filters_order[i] ];
                    // remove input group from original data, so we know if there are some new goups which would be displayed after ordered groups
                    delete original_data[ filters_order[i] ];
                }
            });
            this.filtered_data = filtered_data;
            //if there are still some original data (were not included in stored order), show them after ordered goups
            if( Object.keys( original_data ).length > 0 ){
                this.filtered_data = {...this.filtered_data, ...original_data };
            }

        }
            
        

        let title_styles = {
            ...( activeProPlugin && title_color ? { color: `${title_color}` } : false ),
        };
        
        let header_styles = {};
        if( activeProPlugin && advanced_header ) {
            // make sure to use only header_text_color picker instead of title picker for simple header type
            delete title_styles['color'];

            header_styles = {
                ...( header_text_color ? { color: `${header_text_color}` } : false ),
                ...( header_bg_color ? { backgroundColor: `${header_bg_color}` } : false ),
                ...( header_border_color ? { borderColor: `${header_border_color}` } : false ),
            }
        }

        const heading_styles = {
            ...( activeProPlugin && title_color ? { color: `${title_color}` } : false ),
            ...( activeProPlugin && lines_type == "filter-heading" && lines_color ? { borderColor: `${lines_color}` } : false ),
        };

        const label_styles = {
            ...( activeProPlugin && labels_color ? { color: `${labels_color}` } : false ),
        };


        const data_row_styles = {
            ...( layout == 'box' && box_width ? { flexBasis: `${box_width}px` } : false ),
            ...( activeProPlugin && lines_type == "filter-group" && lines_color ? { borderColor: `${lines_color}` } : false ),
            ...( activeProPlugin && data_row_bg_color ? { backgroundColor: `${data_row_bg_color}` } : false ),
        };

        const contentTemplate = Object.keys( this.filtered_data ).map( ( order_key, i ) => { 
                let heading = this.filtered_data[order_key]['filters_group_name'];
                let input_template = Object.keys( this.filtered_data[order_key]['filters'] ).map( ( input_key ) => { 
                    return <div class="filter-container" >
                        <div class="filter-label" style={ label_styles ? label_styles : false }>{this.filtered_data[order_key]['filters'][input_key]['label']}</div>
                    </div>
                });
                const isFirstItem = i == 0 ? true : false;
                const isLastItem = Object.keys( this.filtered_data ).length - 1 == i ? true : false;

                return input_template.length == 0 
                    ?   "" 
                    :   <div 
                            className={ classNames(
                                "data-row",
                                isSelected && selected_group == order_key ? "selected" : null,
                            )}
                            style={ data_row_styles ? data_row_styles : false }
                            onClick={ () => this.onClickGroup( order_key ) }
                        >
                            { isSelected && selected_group == order_key &&
                                <ButtonGroup className="avanced-filters-navigation">
                                    <Button
                                        className="backward"
                                        icon={ layout == "box" ? 'arrow-left-alt2' : 'arrow-up-alt2' }
                                        onClick={ isFirstItem ? undefined : () => { this.onMoveBackward(i) } }
                                        label={ __( 'Move backward', 'citadela-directory' ) }
                                        aria-disabled={ isFirstItem }
                                    />
                                    <Button
                                        className="forward"
                                        icon={ layout == "box" ? 'arrow-right-alt2' : 'arrow-down-alt2' }
                                        onClick={ isLastItem ? undefined : () => { this.onMoveForward(i) } }
                                        label={ __( 'Move forward', 'citadela-directory' ) }
                                        aria-disabled={ isLastItem }
                                    />
                                </ButtonGroup>
                            }
                            {show_group_title &&
                                <div class="filters-heading" style={ heading_styles ? heading_styles : false }>{heading}</div>
                            }
                            <div class="filters-wrapper">{input_template}</div>
                            
                            { isSelected && selected_group == order_key
                                ? 
                                <div class="logical-operator-component">
                                    <SelectControl
                                        label={__('Logical operator', 'citadela-directory')}
                                        help={ this.filtered_data[order_key]['filters_group_type'] == 'select' 
                                                ?   __( 'Select type input cannot use AND operator.', 'citadela-directory')
                                                :   filter_operators[order_key] == 'AND' 
                                                    ? __( 'Results must include all selected filters.', 'citadela-directory') 
                                                    : __( 'Results include at least one of selected filters.', 'citadela-directory') }
                                        value={ filter_operators[order_key] ? filter_operators[order_key] : 'OR' }
                                        options={ [
                                            { label: 'OR', value: 'OR' },
                                            { label: 'AND', value: 'AND' },
                                        ] }
                                        onChange={ ( value ) => { this.updateFiltersOperator( order_key, value  ) } }
                                        disabled={ this.filtered_data[order_key]['filters_group_type'] == 'select' }
                                    />
                                </div>
                                :<div class="logical-operator-component">
                                    <div class="info-label">
                                        {__('Logical operator', 'citadela-directory')}
                                        <span class="operator"> {filter_operators[order_key] ? filter_operators[order_key] : 'OR'}</span>
                                    </div>
                                </div>
                            }
                        </div>;
        });

        const button_text_styles = button_text_color ? { color: button_text_color } : {};
        const button_styles = {
            ...( button_bg_color ? {backgroundColor: button_bg_color } : {} ),
            ...( button_border_radius >= 0 ? { borderRadius: `${button_border_radius}px` } : {} ),
        } 

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
                    { ! in_search_form &&
                        <ToolbarItem>
                            { ( toggleProps ) => (
                                <ToolbarLayout 
                                    allowedLayouts={ [ 'list', 'box'] } 
                                    value={ layout } 
                                    onChange={ ( value ) => setAttributes( { layout: value } ) } 
                                    toggleProps={ toggleProps } 
                                />
                            )}
                        </ToolbarItem>
                    }
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
                            { label: __( 'All filters', 'citadela-directory' ), value: 'all' },
                            { label: __( 'Show selected filters', 'citadela-directory' ), value: 'selected' },
                            { label: __( 'Hide selected filters', 'citadela-directory' ), value: 'hide_selected' },
                        ] }
                        onChange={ ( value ) => { setAttributes( { show_data: value } ) } }
                    />
                    
                    { show_data !== 'all' && 
                        <>

                        { /* SINGLE CHECKBOXES */ }
                        { Object.keys(checkbox_filter_group).length > 0 && <div class="citadela-label-separator">{ __( 'Single checkbox filters') }</div> }
                        { 
                            Object.keys( checkbox_filter_group ).map( ( group_name ) => {
                                const group_key = `${group_name}_checkbox`;
                                const toggles = Object.keys( checkbox_filter_group[group_name] ).map( ( input_name ) => { 
                                    const selected = this.checkAvailableInput( input_name );
                                    return <ToggleControl
                                        label={ checkbox_filter_group[group_name][input_name]['label'] }
                                        checked={ selected }
                                        onChange={ ( checked ) => { this.updateSelectedInputs( input_name, checked, group_key ) } }
                                    />
                                })
                                return <>{toggles}</>;
                            })
                        }

                        { /* SELECT, MULTISELECT */ }
                        { Object.keys(selection_filter_group).length > 0 && <div class="citadela-label-separator">{ __( 'Selection filters') }</div> }
                        {
                            Object.keys( selection_filter_group ).map( ( input_name ) => {
                                const input_type =selection_filter_group[input_name]['type'];
                                const group_key = `${input_name}_${input_type}`;
                                const selected = this.checkAvailableInput( input_name );
                                return <ToggleControl
                                    label={ selection_filter_group[input_name]['label'] }
                                    checked={ selected }
                                    onChange={ ( checked ) => { this.updateSelectedInputs( input_name, checked, group_key ) } }
                                />
                            })
                        }
                        </>
                    }

                </PanelBody>

                <PanelBody 
                    title={ __('Options', 'citadela-directory') }
                    initialOpen={false}
                    className="citadela-panel"
                >
                    { ! in_search_form &&
                        <ToggleControl
                            label={ __('Advanced header', 'citadela-directory') }
                            help={ __('Collapsible filters section.', 'citadela-directory') }
                            checked={ advanced_header }
                            onChange={ ( checked ) => setAttributes( { advanced_header: checked } ) }
                        />
                    }
                    { advanced_header && 
                        <>
                        <ToggleControl
                            label={ __('Opened by default', 'citadela-directory') }
                            help={ __('Header opened or closed by default.', 'citadela-directory') }
                            checked={ advanced_header_opened }
                            onChange={ ( checked ) => setAttributes( { advanced_header_opened: checked } ) }
                        />

                        { advanced_header_opened &&
                            <ToggleControl
                                label={ __('Closed on mobile', 'citadela-directory') }
                                help={ __('Keep header closed by default on mobile.', 'citadela-directory') }
                                checked={ advanced_header_closed_on_mobile }
                                onChange={ ( checked ) => setAttributes( { advanced_header_closed_on_mobile: checked } ) }
                            />
                        }
                        </>
                    }
                    
                    <ToggleControl
                        label={ __('Show filters group title', 'citadela-directory') }
                        checked={ show_group_title }
                        onChange={ ( checked ) => setAttributes( { show_group_title: checked } ) }
                    />

                    { layout == 'box' && 
                        
                        <CitadelaRangeControl
                            label={ __('Box width', 'citadela-directory') }
                            rangeValue={ box_width }
                            onChange={ ( value ) => { setAttributes( { box_width: value } ); } }
                            min={ 150 }
                            max={ 500 }
                            initial={ 200 }
                            allowReset
                            allowNoValue
                        />
                        
                    }

                    { ! in_search_form &&
                    <>
                        <ToggleControl
                            label={__('Show submit filter button', 'citadela-directory')}
                            help={ show_submit_button ? '' : __('Make sure the submit button is enabled at least in one of Listing Advanced Filters blocks on this page.') }
                            checked={ show_submit_button }
                            onChange={ ( checked ) => setAttributes( { show_submit_button: checked } ) }
                        />
                        { show_submit_button &&
                            <BaseControl 
                                label={ __('Button style', 'citadela-directory') }
                            >
                                <RadioControl
                                    selected={ button_style }
                                    options={ [
                                        { label:  __('Small button', 'citadela-directory'), value: 'small-button' },
                                        { label:  __('Large button', 'citadela-directory'), value: 'large-button' },
                                    ] }
                                    onChange={ ( value ) => { setAttributes( { button_style: value } ) } }
                                />
                            </BaseControl>  
                        }
                    </>
                    }
                    
                </PanelBody>
                

                { activeProPlugin &&
                    <>

                    { advanced_header &&
                        <PanelBody 
                            title={ __('Advanced Header Design', 'citadela-directory') }
                            initialOpen={false}
                            className="citadela-panel"
                        >
                            <CustomColorControl 
                                label={ __('Text color', 'citadela-directory') }
                                color={ header_text_color }
                                onChange={ ( value ) => { setAttributes( { header_text_color: value } ) } }
                                allowReset
                            />

                             <CustomColorControl 
                                label={ __('Background color', 'citadela-directory') }
                                color={ header_bg_color }
                                onChange={ ( value ) => { setAttributes( { header_bg_color: value } ) } }
                                allowReset
                            />
                            
                            <CustomColorControl 
                                label={ __('Border color', 'citadela-directory') }
                                color={ header_border_color }
                                onChange={ ( value ) => { setAttributes( { header_border_color: value } ) } }
                                allowReset
                            />

                            <SelectControl
                                label={__('Border style', 'citadela-directory')}
                                value={ header_border_type }
                                options={ [
                                    { label: __( 'No lines', 'citadela-directory' ), value: 'none' },
                                    { label: __( 'Line under heading', 'citadela-directory' ), value: 'bottom' },
                                    { label: __( 'Border around heading', 'citadela-directory' ), value: 'full' },
                                ] }
                                onChange={ ( value ) => { setAttributes( { header_border_type: value } ) } }
                            />
                            

                        </PanelBody>
                    }

                    <PanelBody
                        title={__('Filters Design', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >

                        <CustomColorControl 
                            label={ __('Filters background color', 'citadela-directory') }
                            color={ data_row_bg_color }
                            onChange={ (value) => { setAttributes( { data_row_bg_color: value } ); } }
                        />

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

                        <SelectControl
                            label={__('Lines decoration', 'citadela-directory')}
                            value={ lines_type }
                            options={ [
                                { label: __( 'No lines', 'citadela-directory' ), value: 'none' },
                                { label: __( 'Line under heading', 'citadela-directory' ), value: 'filter-heading' },
                                { label: __( 'Border around filter group', 'citadela-directory' ), value: 'filter-group' },
                            ] }
                            onChange={ ( value ) => { setAttributes( { lines_type: value } ) } }
                        />

                        { lines_type != 'none' &&
                            <CustomColorControl 
                                label={ __('Lines color', 'citadela-directory') }
                                color={ lines_color }
                                onChange={ (value) => { setAttributes( { lines_color: value } ); } }
                            />
                        }

                        

                        { show_submit_button && ! in_search_form &&
                            <>
                            <CustomColorControl 
                                label={ __('Button text color', 'citadela-directory') }
                                color={ button_text_color }
                                onChange={ ( value ) => { setAttributes( { button_text_color: value } ) } }
                                allowReset
                                disableAlpha
                            />
                            
                            <CustomColorControl 
                                label={ __('Button background color', 'citadela-directory') }
                                color={ button_bg_color }
                                onChange={ ( value ) => { setAttributes( { button_bg_color: value } ) } }
                                allowReset
                            />

                            <CitadelaRangeControl
                                label={ __('Button border radius', 'citadela-directory') }
                                rangeValue={ button_border_radius }
                                onChange={ ( value ) => { setAttributes( { button_border_radius: value } ) } }
                                min={ 0 }
                                max={ 50 }
                                initial={ 0 }
                                allowReset
                                allowNoValue
                            />
                            </>
                        }

                    </PanelBody>
                    </>
                }
            </InspectorControls>
            
            <div className={ classNames(
                "wp-block-citadela-blocks",
                "ctdl-directory-advanced-filters",
                attributes.className,
                `${layout}-layout`,
                `align-${align}`,
                title == '' ? 'no-header-text' : null,
                show_group_title ? null : 'hidden-filter-group-title',
                advanced_header ? 'advanced-header' : 'simple-header',
                advanced_header && advanced_header_opened ? 'opened' : null,
                advanced_header && advanced_header_opened && advanced_header_closed_on_mobile ? 'closed-on-mobile' : null,
                activeProPlugin && advanced_header && header_text_color ? 'custom-header-text-color' : null,
                activeProPlugin && advanced_header && header_bg_color ? 'custom-header-background-color' : null,
                activeProPlugin && advanced_header && header_border_color ? 'custom-header-border-color' : null,
                activeProPlugin && advanced_header ? `header-border-type-${header_border_type}` : null,
                activeProPlugin ? `lines-type-${lines_type}` : null,
                activeProPlugin && lines_color ? 'custom-lines-color' : null,
                activeProPlugin && title_color ? 'custom-title-color' : null,
                activeProPlugin && labels_color ? 'custom-label-color' : null,
                activeProPlugin && data_row_bg_color ? 'custom-data-background-color' : null,
                layout == 'box' && box_width ? 'custom-box-width' : null,
                show_submit_button ? 'has-submit-button' : null,
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
                        <div class="citadela-block-header" style={ header_styles ? header_styles : false }>
                            <RichText
                                tagName='h3'
                                value={ title }
                                onChange={ (title) => setAttributes( { title } ) }
                                placeholder={ block.title }
                                keepPlaceholderOnFocus={true}
                                allowedFormats={ [] }
                                style={ title_styles ? title_styles : false }
                            />
                            { advanced_header &&
                                <div class="header-toggle"><span class="toggle-arrow"></span></div>
                            }
                        </div>

                        <div class="citadela-block-articles">
                            <div class="citadela-block-articles-wrap">

                                { Object.keys( this.filtered_data ).length == 0
                                    ? __('There are no filters to show.', 'citadela-directory')
                                    : <>{ contentTemplate }</>
                                }

                            </div>
                        </div>
                        
                        { show_submit_button && ! in_search_form &&
                            <div 
                                className={ classNames(
                                    "submit-button-wrapper",
                                    `${button_style}-style`,
                                    activeProPlugin && button_text_color ? 'custom-text-color' : null,
                                    activeProPlugin && button_bg_color ? 'custom-background-color' : null,
                                    activeProPlugin && button_border_radius ? 'custom-border-radius' : null
                                ) }
                                >
                                    <div class="submit-button" style={ button_styles }>
                                        <RichText
                                            tagName='span'
                                            className="button-text"
                                            placeholder={ __('Filter', 'citadela-directory') }
                                            value={ button_text }
                                            onChange={ ( value ) => setAttributes( { button_text: value } ) }
                                            keepPlaceholderOnFocus={ true }
                                            allowedFormats={ [ 'core/bold', 'core/italic' ] }
                                            style={ button_text_styles }
                                        />
                                    </div>
                            </div>
                        }
                    </>
                    :
                    <FeatureDisabled
                        description={ __('Enable and insert inputs available for filters via menu', 'citadela-directory') }
                        pathText={ __('Citadela Listing > Item Extension', 'citadela-directory') }
                    />
                }

            </div>
            </>
        );
    }
}

export default Edit;