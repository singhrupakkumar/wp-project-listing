import CategorySelect from '../../components/category-select';
import CustomColorControl from '../../components/custom-color-control';
import CitadelaRangeControl from '../../components/range-control';

const { Component, createRef, useState } = wp.element;
const { __, sprintf } = wp.i18n;
const { InspectorControls, InnerBlocks } = wp.blockEditor;
const { PanelBody, ToggleControl, Icon, RadioControl, TextareaControl, SelectControl, TextControl, BaseControl, RangeControl } = wp.components;
const { apiFetch } = wp;
const { getBlockName, getBlockParents } = wp.data.select('core/block-editor');

import mapStyles from '../../components/map-styles.js';

let styleOptions = mapStyles.map((styleObject) => {
    return { label: styleObject.name, value: styleObject.codeName }
});
styleOptions.push( {label: __('Custom', 'citadela-directory'), value: 'custom'} );

export default class Edit extends Component {
    constructor() {
        super( ...arguments );
        this.isParentColumn = this.isParentColumn.bind(this);
		this.updateMainBlockDivWrapper = this.updateMainBlockDivWrapper.bind(this);
		this.blockRef = createRef();
        this.state = {
            categoriesList: [],
            locationsList: [],
        };
    }

    componentDidMount() {
        const { attributes, setAttributes } = this.props;
		const isParentColumn = this.isParentColumn();
		
		if( attributes.inColumn !== isParentColumn ){
			setAttributes( { inColumn: isParentColumn } );
		}
		this.updateMainBlockDivWrapper();

        this.fetchRequest = apiFetch( {
			path: `/wp/v2/citadela-item-category/?per_page=-1`,
		} ).then(
			( categoriesList ) => {
				this.setState( { categoriesList } );
			}
		).catch(
			() => {
				this.setState( { categoriesList: [] } );
			}
        );

        this.fetchRequest = apiFetch( {
			path: `/wp/v2/citadela-item-location/?per_page=-1`,
		} ).then(
			( locationsList ) => {
				this.setState( { locationsList } );
			}
		).catch(
			() => {
				this.setState( { locationsList: [] } );
			}
		);
    }

    componentDidUpdate(){
		this.updateMainBlockDivWrapper();
	}

	isParentColumn(){
		const parents = getBlockParents( this.props.clientId );
		return parents.length > 0 && getBlockName( parents[ parents.length - 1] ) === 'core/column';
	}

	updateMainBlockDivWrapper(){
		const { withSearchForm, coverHeight, inColumn } = this.props.attributes;
		let blockWrapper = this.blockRef.current.parentNode;
		if( inColumn && coverHeight ){
			blockWrapper.dataset.coverHeight = 'true';
		}else{
			delete blockWrapper.dataset.coverHeight;
		}

        if( withSearchForm ){
            blockWrapper.dataset.withSearchForm = 'true';
		}else{
			delete blockWrapper.dataset.withSearchForm;
		}

	}

    render() {
        const { categoriesList, locationsList} = this.state;
        const { attributes, setAttributes, name, isSelected } = this.props;
        const { 
            limitPosts,
            maxPosts,
            withSearchForm, 
            category, 
            location, 
            onlyFeatured, 
            theme,
            themeOSM,
            customTheme, 
            provider, 
            dataType,
            dynamicTrack,
            trackColor, 
            trackEndpointsColor,
            clusterGridSize,
            coverHeight,
			unit,
			height,
			inColumn,
        } = attributes;
        
        const block = wp.blocks.getBlockType(name);
        const featuredImage = `${CitadelaDirectorySettings.images}/blocks/map.png`;

		const mainStyle = {
			...( height !== undefined && ! ( inColumn && coverHeight ) ? { height: height + unit } : {} ),
		}

        return (
            <>
                <InspectorControls key='inspector'>
                    <PanelBody
                        title={__('Options', 'citadela-directory')}
                        initialOpen={true}
                        className="citadela-panel"
                    >
                        <SelectControl
                            label={__('Maps provider', 'citadela-directory')}
                            value={ provider }
                            options={ [
                                { label: 'Google Maps', value: 'google-map' },
                                { label: 'OpenStreetMap', value: 'openstreetmap' },
                            ] }
                            onChange={ ( value ) => { setAttributes( { provider: value } ) } }
                        />
                        <ToggleControl
							label={__('With search form', 'citadela-directory')}
							checked={ withSearchForm }
							onChange={ ( checked ) => setAttributes( { withSearchForm: checked } ) }
						/>
                        { withSearchForm &&
                            <OutsideSearchFormBreakpoint attributes={ attributes } setAttributes={ setAttributes } />
                        }
                        { provider == 'openstreetmap' &&
                            <>
                            <RadioControl
                                label={__('Show on map', 'citadela-directory')}
                                selected={ dataType }
                                options={ [
                                    { label:  __('Markers', 'citadela-directory'), value: 'markers' },
                                    { label:  __('GPX Tracks', 'citadela-directory'), value: 'tracks' },
                                    { label:  __('Markers & GPX Tracks', 'citadela-directory'), value: 'all' },
                                ] }
                                onChange={ ( value ) => { setAttributes( { dataType: value } ) } }
                            />
                            
                            { dataType != "markers" &&
                                <>
                                <ToggleControl
                                    label={ __('Dynamic track visibility', 'citadela-directory') }
                                    help={ __('Tracks are hidden and replaced with map marker according to zoom level.', 'citadela-directory') }
                                    checked={ dynamicTrack }
                                    onChange={ ( checked ) => setAttributes( { dynamicTrack: checked } ) }
                                />

                                <CustomColorControl 
                                    label={ __('Track color', 'citadela-directory') }
                                    color={ trackColor }
                                    onChange={ ( value ) => { setAttributes( { trackColor: value } ); } }
                                    allowReset
                                />

                                <CustomColorControl 
                                    label={ __('Track endpoints color', 'citadela-directory') }
                                    color={ trackEndpointsColor }
                                    onChange={ ( value ) => { setAttributes( { trackEndpointsColor: value } ); } }
                                    allowReset
                                    disableAlpha
                                />
                                </>
                            }

                            </>
                        }
                    </PanelBody>
                    <PanelBody
                        title={__('Filters', 'citadela-directory')}
                        initialOpen={true}
                        className="citadela-panel"
                    >
                        <CategorySelect
                            categoriesList={ categoriesList }
                            label={ __( 'Category', 'citadela-directory' ) }
                            noOptionLabel={ __( 'All', 'citadela-directory' ) }
                            selectedCategoryId={ category }
                            onChange={ ( value ) => { setAttributes( { category: value } ) } }
                        />
                        <CategorySelect
                            categoriesList={ locationsList }
                            label={ __( 'Location', 'citadela-directory' ) }
                            noOptionLabel={ __( 'All', 'citadela-directory' ) }
                            selectedCategoryId={ location }
                            onChange={ ( value ) => { setAttributes( { location: value } ) } }
                        />
                        <ToggleControl
							label={__('Only featured items', 'citadela-directory')}
							checked={ onlyFeatured }
							onChange={ ( checked ) => setAttributes( { onlyFeatured: checked } ) }
                        />
                    </PanelBody>

                    <PanelBody 
                        title={ __('Custom Height Settings', 'citadela-directory') }
                        initialOpen={false}
                        className="citadela-panel"
                    >	
                        { inColumn &&
                            <ToggleControl 
                                label={ __( 'Cover column height', 'citadela-directory' ) }
                                help={ __( 'Works when block is the only block in a column.', 'citadela-directory' ) }
                                checked={ coverHeight }
                                onChange={ (value) => { setAttributes( { coverHeight: value } ) }}
                            />
                        }
                        { ( ! inColumn || ( inColumn && ! coverHeight ) ) &&
                            <HeightOptions attributes={ attributes } setAttributes={ setAttributes } />
                        }
                    </PanelBody>

                    <PanelBody
                            title={__('Marker Cluster Settings', 'citadela-directory')}
                            initialOpen={false}
                            className="citadela-panel"
                        >
                        <CitadelaRangeControl
                            label={__('Cluster radius in px', 'citadela-directory')}
                            help={ clusterGridSize == 0 ? __("Clusters are disabled.", 'citadela-directory') : sprintf( __( "Markers in distance less than %spx radius are grouped into clusters. Set 0 to disable clusters.", 'citadela-directory' ), clusterGridSize ) }
                            rangeValue={clusterGridSize}
                            onChange={(value) => { setAttributes({ clusterGridSize: value }) }}
                            min={0}
                            max={200}
                            initial={80}
                            allowReset
                        />
                    </PanelBody>

                    {provider == 'google-map' && 
                        <PanelBody
                            title={__('Appearance', 'citadela-directory')}
                            initialOpen={false}
                            className="citadela-panel"
                        >
                            <RadioControl
                                label={__('Theme', 'citadela-directory')}
                                selected={ theme }
                                options={ styleOptions }
                                onChange={ ( value ) => { setAttributes( { theme: value } ) } }
                            />
                            {theme == 'custom' && <TextareaControl
                                label={ __('Style JSON', 'citadela-directory') }
                                help={ <>{__('Copy and paste the JSON from this website:', 'citadela-directory')} <a target="_blank" href={'https://mapstyle.withgoogle.com/'}>https://mapstyle.withgoogle.com/</a></> }
                                value={ customTheme }
                                onChange={ (value) => { setAttributes({ customTheme: value }) } }
                            />}
                        </PanelBody>
                    }
                    {provider == 'openstreetmap' && 
                        <PanelBody
                            title={__('Appearance', 'citadela-directory')}
                            initialOpen={false}
                            className="citadela-panel"
                        >
                            <RadioControl
                                label={__('Theme', 'citadela-directory')}
                                selected={ themeOSM }
                                options={ [
                                    { label:  __('Default', 'citadela-directory'), value: 'default' },
                                    { label:  __('Silver', 'citadela-directory'), value: 'silver' },
                                    { label:  __('Retro', 'citadela-directory'), value: 'retro' },
                                    { label:  __('Dark', 'citadela-directory'), value: 'dark' },
                                    { label:  __('Night', 'citadela-directory'), value: 'night' },
                                    
                                ] }
                                onChange={ ( value ) => { setAttributes( { themeOSM: value } ) } }
                            />
                            
                        </PanelBody>
                    }
                    <PanelBody
                        title={__('Performance Settings', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <ToggleControl
							label={__('Limit displayed posts', 'citadela-directory')}
							checked={ limitPosts }
							onChange={ ( checked ) => setAttributes( { limitPosts: checked } ) }
						/>
                        { limitPosts && 
                            <TextControl 
                                label={__('Maximum displayed posts', 'citadela-directory')}
                                type='number'
                                onChange={ ( value ) => { setAttributes( { maxPosts: parseInt( value ) } ) } }
                                min={1}
                                value={ maxPosts }
                            />
                        }
                        
                    </PanelBody>                    
                </InspectorControls>

                <div className={ classNames(
                        "wp-block-citadela-blocks ctdl-directory-google-map",
                        attributes.className,
                        { "custom-height" : height !== undefined && ! ( inColumn && coverHeight ) ? true : false },
                        { "cover-height" : inColumn && coverHeight ? true : false },                       
                    ) }
                    ref={ this.blockRef }
                    >

                    <div class="ctdl-blockcard-title">
                        <div class="ctdl-blockcard-icon">
                            <Icon icon={block.icon.src} />
                        </div>
                        <div class="ctdl-blockcard-text">
                            <div class="ctdl-blockcard-name">{ block.title }</div>
                            <div class="ctdl-blockcard-desc">{ block.description }</div>
                        </div>
                    </div>

                    <div class="citadela-google-map" style={ mainStyle }>
                    
                        <div class="citadela-map-image" style={ {backgroundImage: 'url(' + featuredImage + ')'} }></div>
                    </div>
                </div>

                {withSearchForm && <InnerBlocks
                    template={[
                        ['citadela-directory/directory-search-form', {}],
                    ]}
                    templateLock="all"
                />}
            </>
        );
    }
}

const HeightOptions = ({
	attributes,
	setAttributes,
}) => {
	const { 
		unit,
		height,
	} = attributes;

	const [ heightState, setHeight ] = useState( height );
		
    let unitRange = {
        px: {
            min: 300,
            max: 800,
        },
        vh: {
            min: 30,
            max: 100,
        },
        vw: {
            min: 30,
            max: 100,
        },
    }

	let unitStep = 1;
	if( unit != "px" ){
		unitStep = 0.1;
	}

	return (
		<>
			<BaseControl 
				label={ __('Height unit', 'citadela-directory') } 
				>
				<SelectControl
					value={ unit }
					options={ [
						{ label: 'px', value: 'px' },
						{ label: 'vh', value: 'vh' },
						{ label: 'vw', value: 'vw' },
					] }
					onChange={ ( value ) => {
						setHeight( unitRange[value]['min'] );
						setAttributes( { 
							unit: value,
							height: unitRange[value]['min'],
						} );
					} 
					}
				/>
			</BaseControl>
			<BaseControl 
				label={ __( 'Recommended map height', 'citadela-directory' ) + `: ${height}${unit}` } 
                help={ __( "Minimum map height may differ of defined value due to needs of map's inside content.", 'citadela-directory' ) } 
				id="spacer-height"
			>
                <RangeControl
                    value={ heightState }
                    onChange={ (value) => {
                        let newValue = value;
						setHeight(newValue);
						if ( value == '' ) {
							setHeight( '' );
							newValue = unitRange[unit]['min'];
						}
						if(unit != "px"){
							setAttributes( { height: newValue ? parseFloat(newValue) : newValue } );
						}else{
							setAttributes( { height: newValue ? parseInt(newValue) : newValue } );
						}
                    } }
                    min={ unitRange[unit]['min'] }
                    max={ unitRange[unit]['max'] }
                    step={ unitStep }
                />
			</BaseControl>

		</>
	)
}


const OutsideSearchFormBreakpoint = ( { attributes, setAttributes } ) => {
	const { 
		outsideFormBreakpoint,
	} = attributes;

	const [ inputValue, setValue ] = useState( outsideFormBreakpoint );

	return (
		<BaseControl 
			label={ __( 'Form inside map breakpoint', 'citadela-directory' ) }
			help={ __( 'Search form is displayed inside map on screen width larger than', 'citadela-directory' ) + ` ${outsideFormBreakpoint}px.` }
			id="mobile-width"
		>
			<TextControl
				type="number"
				value={ inputValue }
				onChange={ ( value ) => {
					let newValue = value;
					setValue(newValue);
					if ( value == '' ) {
						newValue = 600;
					}
					setAttributes( { outsideFormBreakpoint: newValue ? parseInt( newValue ) : newValue } );
				} }
				step={ 1 }
			/>
		</BaseControl>
	)
}