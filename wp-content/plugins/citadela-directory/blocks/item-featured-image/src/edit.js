/**
 * Internal dependencies
 */
import ImageSizes from '../../components/image-sizes';
import ToolbarAlignment from '../../components/toolbar-alignment';
import ResponsiveOptionsTabs from '../../components/responsive-options-tabs';
import StateIcons from '../../components/state-icons';
/**
 * WordPress dependencies
 */
const { __, setLocaleData } = wp.i18n;
const { Component, createRef, useState } = wp.element;
const { Icon, PanelBody, ToggleControl, ToolbarGroup, ToolbarItem, BaseControl, SelectControl, TextControl } = wp.components;
const { InspectorControls, BlockControls } = wp.blockEditor;
const { getBlockName, getBlockParents } = wp.data.select('core/block-editor');

export default class Edit extends Component {

	constructor() {
		super( ...arguments );
		this.isParentColumn = this.isParentColumn.bind(this);
		this.updateMainBlockDivWrapper = this.updateMainBlockDivWrapper.bind(this);
		this.blockRef = createRef();
		this.state = {
			responsiveTab: "desktop",
		}
	}

	componentDidMount(){
		const { attributes, setAttributes } = this.props;
		const isParentColumn = this.isParentColumn();
		
		if( attributes.inColumn !== isParentColumn ){
			setAttributes( { inColumn: isParentColumn } );
		}
		this.updateMainBlockDivWrapper();		
	}

	componentDidUpdate(){
		this.updateMainBlockDivWrapper();
	}

	isParentColumn(){
		const parents = getBlockParents( this.props.clientId );
		return parents.length > 0 && getBlockName( parents[ parents.length - 1] ) === 'core/column';
	}

	updateMainBlockDivWrapper(){
		const { coverHeight, coverHeightMobile, inColumn } = this.props.attributes;
		let blockWrapper = this.blockRef.current.parentNode;
		const currentCoverHeight = this.state.responsiveTab == 'desktop' ? coverHeight : coverHeightMobile;
		if( inColumn && currentCoverHeight ){
			blockWrapper.dataset.coverHeight = 'true';
		}else{
			delete blockWrapper.dataset.coverHeight;
		}
	}

	render() {
		const { attributes, setAttributes, name, isSelected } = this.props;
		
		const { 
			size,
			inPopup,
			align,
			coverHeight,
			coverHeightMobile,
			unit,
			unitMobile,
			minUnit,
			minUnitMobile,
			height,
			heightMobile,
			minHeight,
			minHeightMobile,
			useResponsiveOptions,
			inColumn,
			objectPosition,
		} = attributes;
		
        const block = wp.blocks.getBlockType(name);

		let responsiveSettings = "";
		
		// default and desktop responsive options
		if ( ! useResponsiveOptions || ( useResponsiveOptions && this.state.responsiveTab == "desktop" ) ) {

			responsiveSettings = 
				<>
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
					{ ( inColumn && coverHeight ) &&
						<MinHeightOptions attributes={ attributes } setAttributes={ setAttributes } />
					}
				</>

		}

		// mobile responsive options
		if ( useResponsiveOptions && this.state.responsiveTab == "mobile" ) {

			responsiveSettings = 
				<>
					<MobileWidthBreakpoint attributes={ attributes } setAttributes={ setAttributes } />
					{ inColumn &&
						<ToggleControl 
							label={ __( 'Cover column height', 'citadela-directory' ) }
							help={ __( 'Works when block is the only block in a column.', 'citadela-directory' ) }
							checked={ coverHeightMobile }
							onChange={ (value) => { setAttributes( { coverHeightMobile: value } ) }}
						/>
					}
					{ ( ! inColumn || ( inColumn && ! coverHeightMobile ) ) &&
						<HeightOptionsMobile attributes={ attributes } setAttributes={ setAttributes } />
					}
					{ ( inColumn && coverHeightMobile ) &&
						<MinHeightOptionsMobile attributes={ attributes } setAttributes={ setAttributes } />
					}
				</>
		}
		

		const currentAttr = ( useResponsiveOptions && this.state.responsiveTab == 'mobile' )
		? {
			height: heightMobile,
			minHeight: minHeightMobile,
			unit: unitMobile,
			minUnit: minUnitMobile,
			coverHeight: coverHeightMobile,
		}
		: {
			height: height,
			minHeight: minHeight,
			unit: unit,
			minUnit: minUnit,
			coverHeight: coverHeight,
		}

		const imgStyle = {
			...( currentAttr.height !== undefined && ! ( inColumn && currentAttr.coverHeight ) ? { height: currentAttr.height + currentAttr.unit } : {} ),
		}
		
		const mainStyle = {
			//min height style for main div
			...( inColumn && currentAttr.coverHeight ? { minHeight: currentAttr.minHeight + currentAttr.minUnit } : {} ),
		}

		// constant decide if height of image was customized, using cover height option or custom height value
		const customizedHeight = ( currentAttr.height !== undefined && ! ( inColumn && currentAttr.coverHeight ) ) || ( inColumn && currentAttr.coverHeight );
        return (
        <>
            <BlockControls>
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
                </ToolbarGroup>
            </BlockControls>
            <InspectorControls key='inspector'>
                <PanelBody
                    title={__('Options', 'citadela-directory')}
                    initialOpen={true}
                    className="citadela-panel"
                >

                    <ImageSizes
                        value={ size }
                        onChange={ ( value ) => setAttributes( { size: value } ) }
                    />

                    <ToggleControl
                        label={__('Open image in popup', 'citadela-directory')}
                        checked={ inPopup }
                        onChange={ ( checked ) => setAttributes( { inPopup: checked } ) }
                    />

                </PanelBody>
				
				<PanelBody 
					title={ __('Custom Height Settings', 'citadela-directory') }
					initialOpen={false}
					className="citadela-panel"
				>	
					<ToggleControl 
						label={ __( 'Use responsive options', 'citadela-directory' ) }
						checked={ useResponsiveOptions }
						onChange={ (value) => { setAttributes( { useResponsiveOptions: value } ) }}
					/>
					{ useResponsiveOptions 
						? 
							<div class="citadela-responsive-settings-holder">
								<ResponsiveOptionsTabs activeTab={ this.state.responsiveTab } onChange={ (value) => { this.setState( { responsiveTab: value } ) } } />
								{ responsiveSettings }
							</div>
						:
							<>
								{ responsiveSettings }
							</>
					}
					{ //customizedHeight &&
						<SelectControl
							value={ objectPosition }
							label={ __('Image focus', 'citadela-directory') } 
							options={ [
								{ label: __( 'Top left', 'citadela-directory' ), value: 'top left' },
								{ label: __( 'Top', 'citadela-directory' ), value: 'top center' },
								{ label: __( 'Top right', 'citadela-directory' ), value: 'top right' },
								{ label: __( 'Left', 'citadela-directory' ), value: 'center left' },
								{ label: __( 'Center', 'citadela-directory' ), value: 'center center' },
								{ label: __( 'Right', 'citadela-directory' ), value: 'center right' },
								{ label: __( 'Bottom left', 'citadela-directory' ), value: 'bottom left' },
								{ label: __( 'Bottom', 'citadela-directory' ), value: 'bottom center' },
								{ label: __( 'Bottom right', 'citadela-directory' ), value: 'bottom right' },
							] }
							onChange={ ( value ) => setAttributes( { objectPosition: value } ) }
						/>
					}

				</PanelBody>

            </InspectorControls>
            <div className={ classNames(
					"wp-block-citadela-blocks",
					"ctdl-block-item-featured-image",
                    attributes.className,
					`align-${align}`,
					`size-${size}`,
					{ "custom-height" : currentAttr.height !== undefined && ! ( inColumn && currentAttr.coverHeight ) ? true : false },
					{ "cover-height" : inColumn && currentAttr.coverHeight ? true : false },
					{ [ `position-${objectPosition.replace(" ", "-" )}` ] : customizedHeight ? true : false },
				) }
				style={ mainStyle }
				ref={ this.blockRef }
			>	

				<StateIcons 
					useResponsiveOptions= { useResponsiveOptions } 
					isSelected={ isSelected } 
					currentView={ this.state.responsiveTab }  
				/>

                <div class="ctdl-blockcard-title">
                    <div class="ctdl-blockcard-icon">
                        <Icon icon={block.icon.src} />
                    </div>
                    <div class="ctdl-blockcard-text">
                        <div class="ctdl-blockcard-name">{ block.title }</div>
                        <div class="ctdl-blockcard-desc">{ block.description }</div>
                    </div>
                </div>

                <div class="ft-image-thumb">
                    <div class="ft-image" style={ imgStyle }></div>
                </div>
            </div>
        </>
        );
	}
	

}

const MobileWidthBreakpoint = ( { attributes, setAttributes } ) => {
	const { 
		breakpointMobile,
	} = attributes;

	const [ inputBreakpointMobileValue, setBreakpointMobileInputValue ] = useState( breakpointMobile );

	return (
		<BaseControl 
			label={ __( 'Mobile width breakpoint', 'citadela-directory' ) }
			help={ __( 'Responsive options applied under screen width', 'citadela-directory' ) + ` ${breakpointMobile}px` }
			id="mobile-width"
		>
			<TextControl
				type="number"
				value={ inputBreakpointMobileValue }
				onChange={ ( value ) => {
					let newValue = value;
					setBreakpointMobileInputValue(newValue);
					if ( value == '' ) {
						newValue = 600;
					}
					setAttributes( { breakpointMobile: newValue ? parseInt( newValue ) : newValue } );
				} }
				step={ 1 }
			/>
		</BaseControl>
	)
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
		
	let unitDefaults = [];
	unitDefaults["px"] = 400;
	unitDefaults["vh"] = 100;
	unitDefaults["vw"] = 30;

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
						setHeight( unitDefaults[ value ] );
						setAttributes( { 
							unit: value,
							height: unitDefaults[value],
						} );
					} 
					}
				/>
			</BaseControl>
			<BaseControl 
				label={ __( 'Image height', 'citadela-directory' ) + ( height !== undefined ? `: ${height}${unit}` : ': ' + __( 'default image size', 'citadela-directory' ) ) } 
			>
				<TextControl
					type="number"
					value={ heightState }
					step={ unitStep }
					onChange={ ( value ) => {
						let newValue = value;
						setHeight(newValue);
						if ( value == '' ) {
							setHeight( '' );
							newValue = undefined;
						}
						if(unit != "px"){
							setAttributes( { height: newValue ? parseFloat(newValue) : newValue } );
						}else{
							setAttributes( { height: newValue ? parseInt(newValue) : newValue } );
						}
					} }
				/>
			</BaseControl>

		</>
	)
}

const HeightOptionsMobile = ({
	attributes,
	setAttributes,
}) => {
	const { 
		unitMobile,
		heightMobile,
	} = attributes;

	const [ heightState, setHeight ] = useState( heightMobile );
		
	let unitDefaults = [];
	unitDefaults["px"] = 400;
	unitDefaults["vh"] = 100;
	unitDefaults["vw"] = 30;

	let unitStep = 1;
	if( unitMobile != "px" ){
		unitStep = 0.1;
	}

	return (
		<>
			<BaseControl 
				label={ __('Height unit', 'citadela-directory') } 
				>
				<SelectControl
					value={ unitMobile }
					options={ [
						{ label: 'px', value: 'px' },
						{ label: 'vh', value: 'vh' },
						{ label: 'vw', value: 'vw' },
					] }
					onChange={ ( value ) => {
						setHeight( unitDefaults[ value ] );
						setAttributes( { 
							unitMobile: value,
							heightMobile: unitDefaults[value],
						} );
					} 
					}
				/>
			</BaseControl>
			<BaseControl 
				label={ __( 'Image height', 'citadela-directory' ) + ( heightMobile !== undefined ? `: ${heightMobile}${unitMobile}` : ': ' + __( 'default image size', 'citadela-directory' ) ) } 
			>
				<TextControl
					type="number"
					value={ heightState }
					step={ unitStep }
					onChange={ ( value ) => {
						let newValue = value;
						setHeight(newValue);
						if ( value == '' ) {
							setHeight( '' );
							newValue = undefined;
						}
						if(unitMobile != "px"){
							setAttributes( { heightMobile: newValue ? parseFloat(newValue) : newValue } );
						}else{
							setAttributes( { heightMobile: newValue ? parseInt(newValue) : newValue } );
						}
					} }
				/>
			</BaseControl>

		</>
	)
}

const MinHeightOptions = ({
	attributes,
	setAttributes,
}) => {
	const { 
		minUnit,
		minHeight,
	} = attributes;

	const [ heightState, setHeight ] = useState( minHeight );
		
	let unitDefaults = [];
	unitDefaults["px"] = 250;
	unitDefaults["vh"] = 100;
	unitDefaults["vw"] = 30;

	let unitStep = 1;
	if( minUnit != "px" ){
		unitStep = 0.1;
	}

	return (
		<>
			<BaseControl 
				label={ __('Minimum height unit', 'citadela-directory') } 
				>
				<SelectControl
					value={ minUnit }
					options={ [
						{ label: 'px', value: 'px' },
						{ label: 'vh', value: 'vh' },
						{ label: 'vw', value: 'vw' },
					] }
					onChange={ ( value ) => {
						setHeight( unitDefaults[ value ] );
						setAttributes( { 
							minUnit: value,
							minHeight: unitDefaults[value],
						} );
					} 
					}
				/>
			</BaseControl>
			<BaseControl 
				label={ __( 'Minimum image height', 'citadela-directory' ) + ( minHeight !== undefined ? `: ${minHeight}${minUnit}` : '' ) } 
			>
				<TextControl
					type="number"
					value={ heightState }
					step={ unitStep }
					onChange={ ( value ) => {
						let newValue = value;
						setHeight(newValue);
						if ( value == '' ) {
							setHeight( '' );
							newValue = unitDefaults[ minUnit ];
						}
						if(minUnit != "px"){
							setAttributes( { minHeight: newValue ? parseFloat(newValue) : newValue } );
						}else{
							setAttributes( { minHeight: newValue ? parseInt(newValue) : newValue } );
						}
					} }
				/>
			</BaseControl>

		</>
	)
}

const MinHeightOptionsMobile = ({
	attributes,
	setAttributes,
}) => {
	const { 
		minUnitMobile,
		minHeightMobile,
	} = attributes;

	const [ heightState, setHeight ] = useState( minHeightMobile );
		
	let unitDefaults = [];
	unitDefaults["px"] = 250;
	unitDefaults["vh"] = 100;
	unitDefaults["vw"] = 30;

	let unitStep = 1;
	if( minUnitMobile != "px" ){
		unitStep = 0.1;
	}

	return (
		<>
			<BaseControl 
				label={ __('Minimum height unit', 'citadela-directory') } 
				>
				<SelectControl
					value={ minUnitMobile }
					options={ [
						{ label: 'px', value: 'px' },
						{ label: 'vh', value: 'vh' },
						{ label: 'vw', value: 'vw' },
					] }
					onChange={ ( value ) => {
						setHeight( unitDefaults[ value ] );
						setAttributes( { 
							minUnitMobile: value,
							minHeightMobile: unitDefaults[value],
						} );
					} 
					}
				/>
			</BaseControl>
			<BaseControl 
				label={ __( 'Minimum image height', 'citadela-directory' ) + ( minHeightMobile !== undefined ? `: ${minHeightMobile}${minUnitMobile}` : '' ) } 
			>
				<TextControl
					type="number"
					value={ heightState }
					step={ unitStep }
					onChange={ ( value ) => {
						let newValue = value;
						setHeight(newValue);
						if ( value == '' ) {
							setHeight( '' );
							newValue = unitDefaults[ minUnitMobile ];
						}
						if(minUnitMobile != "px"){
							setAttributes( { minHeightMobile: newValue ? parseFloat(newValue) : newValue } );
						}else{
							setAttributes( { minHeightMobile: newValue ? parseInt(newValue) : newValue } );
						}
					} }
				/>
			</BaseControl>

		</>
	)
}