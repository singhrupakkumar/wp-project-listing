import ImageSizes from '../../components/image-sizes';
import CarouselPanel from '../../components/panel-carousel';
import ResponsiveOptionsTabs from '../../components/responsive-options-tabs';
import StateIcons from '../../components/state-icons';

const { Component, useState } = wp.element;
const { __ } = wp.i18n;
const { InspectorControls, RichText, } = wp.blockEditor;
const { 
    PanelBody, 
    ToggleControl, 
    Icon, 
    SelectControl, 
    ColorPalette, 
    ColorIndicator, 
    BaseControl,
    TextControl,
} = wp.components;

export default class Edit extends Component {
    constructor() {
        super( ...arguments );
        this.state = {
            responsiveTabHeight: "desktop",
        };
    }

    render() {
        const activeProPlugin = typeof CitadelaProSettings === "undefined" ? false : true;

        const { attributes, setAttributes, name, isSelected } = this.props;
        const { 
            size, 
            useCarousel,
            carouselNavigation,
            carouselPagination,
            carouselAutoplay,
            carouselAutoHeight,
            carouselLoop,
            carouselColor,
            textColor,
            imageSize,
            useResponsiveOptionsImageHeight,
            breakpointMobileImageHeight,
            proportionalImageHeight,
            proportionalImageHeightMobile,
            imageHeightType,
            imageHeightTypeMobile,
            imageObjectPosition,
            imageObjectPositionMobile,
            imageHeight,
            imageHeightMobile,
            imageHeightUnit,
            imageHeightUnitMobile,
            captionPosition,
            imagesVerticalAlign,
        } = attributes;
    
        const colorsSet = [
            { color: '#00d1b2' },
            { color: '#3373dc' },
            { color: '#209cef' },
            { color: '#22d25f' },
            { color: '#ffdd57' },
            { color: '#ff3860' },
            { color: '#7941b6' },
            { color: '#392F43' },
        ];

        const block = wp.blocks.getBlockType(name);

        let responsiveSettings = "";

        // default and desktop responsive options
        if ( ! useResponsiveOptionsImageHeight || ( useResponsiveOptionsImageHeight && this.state.responsiveTabHeight == "desktop" ) ) {
            responsiveSettings = 
				<>
                    <ToggleControl 
                        label={ __( 'Proportional image height', 'citadela-directory' ) }
                        checked={ proportionalImageHeight }
                        onChange={ (value) => { setAttributes( { proportionalImageHeight: value } ) }}
                    />
                    { ! proportionalImageHeight &&
                        <>
                        <SelectControl
                            label={ __( 'Image height', 'citadela-directory' ) }
                            value={ imageHeightType }
                            options={ [
                                { label: __( 'Default', 'citadela-directory' ), value: 'default' },
                                { label: __( 'Custom', 'citadela-directory' ), value: 'custom' },
                            ] }
                            onChange={ ( value ) => { setAttributes( { imageHeightType: value } ); } }
                        />
                        { imageHeightType == 'custom' &&
                            <ImageHeightOptions attributes={ attributes } setAttributes={ setAttributes } />
                        }
                        <SelectControl
							value={ imageObjectPosition }
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
							onChange={ ( value ) => setAttributes( { imageObjectPosition: value } ) }
						/>
                        </>
                    }
				</>
        }

        // mobile responsive options
		if ( useResponsiveOptionsImageHeight && this.state.responsiveTabHeight == "mobile" ) {
            responsiveSettings = 
				<>
                    
                    <MobileWidthBreakpoint attributes={ attributes } setAttributes={ setAttributes } />

                    <ToggleControl 
                        label={ __( 'Proportional image height', 'citadela-directory' ) }
                        checked={ proportionalImageHeightMobile }
                        onChange={ (value) => { setAttributes( { proportionalImageHeightMobile: value } ) }}
                    />

                    { ! proportionalImageHeightMobile &&
                        <>
                        <SelectControl
                            label={ __( 'Image height', 'citadela-directory' ) }
                            value={ imageHeightTypeMobile }
                            options={ [
                                { label: __( 'Default', 'citadela-directory' ), value: 'default' },
                                { label: __( 'Custom', 'citadela-directory' ), value: 'custom' },
                            ] }
                            onChange={ ( value ) => { setAttributes( { imageHeightTypeMobile: value } ); } }
                        />
                        { imageHeightTypeMobile == 'custom' &&
                            <ImageHeightMobileOptions attributes={ attributes } setAttributes={ setAttributes } />
                        }
                        <SelectControl
							value={ imageObjectPositionMobile }
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
							onChange={ ( value ) => setAttributes( { imageObjectPositionMobile: value } ) }
						/>
                        </>
                    }

				</>
        }

        //set grid type
        var gridType = "grid-type-1";
        var gridLayout = "layout-default";
        
        const currentAttr = ( useResponsiveOptionsImageHeight && this.state.responsiveTabHeight == 'mobile' )
		? {
            imageHeight: imageHeightMobile,
            imageHeightUnit: imageHeightUnitMobile,
            proportionalImageHeight: proportionalImageHeightMobile,
            imageHeightType: imageHeightTypeMobile,
            imageObjectPosition: imageObjectPositionMobile,
        }
		: {
            imageHeight: imageHeight,
            imageHeightUnit: imageHeightUnit,
            proportionalImageHeight: proportionalImageHeight,
            imageHeightType: imageHeightType,
            imageObjectPosition: imageObjectPosition,
		}

        let carouselStyle = {};
        let thumbnailStyle = {};
        let itemContentStyle = {};

        thumbnailStyle = {
            ...thumbnailStyle,
            ...( ! currentAttr.proportionalImageHeight && currentAttr.imageHeightType == 'custom' && currentAttr.imageHeight !== undefined ? { height: currentAttr.imageHeight + currentAttr.imageHeightUnit } : {} ),
        }

        if( activeProPlugin ){
            carouselStyle = {
                ...( carouselColor ? { color: carouselColor } : {} ),
            };
            itemContentStyle = {
                ...( textColor ? { textColor: textColor } : {} ),
            };
        }


        const numberOfItems = 10;
        return (
            <>
                <InspectorControls key='inspector'>
                    <PanelBody
                        title={__('Default Layout Options', 'citadela-directory')}
                        initialOpen={true}
                        className="citadela-panel"
                    >
                        <SelectControl
                            label={ __( 'Grid size', 'citadela-directory' ) }
                            value={ size }
                            options={ [
                                { label: __( 'Mini', 'citadela-directory' ), value: 'mini' },
                                { label: __( 'Small', 'citadela-directory' ), value: 'small' },
                                { label: __( 'Medium', 'citadela-directory' ), value: 'medium' },
                                { label: __( 'Large', 'citadela-directory' ), value: 'large' },
                            ] }
                            onChange={ ( value ) => { setAttributes( { size: value } ); } }
                        />

                        <ImageSizes
                            value={ imageSize }
                            customSizes={ { citadela_item_thumbnail: __( 'Default', 'citadela-directory' ) } }
                            onChange={ ( value ) => setAttributes( { imageSize: value } ) }
                        />
                        
                        <SelectControl
                            label={ __( 'Images vertical align', 'citadela-directory' ) }
                            value={ imagesVerticalAlign }
                            options={ [
                                { label: __( 'Top', 'citadela-directory' ), value: 'top' },
                                { label: __( 'Center', 'citadela-directory' ), value: 'center' },
                                { label: __( 'Bottom', 'citadela-directory' ), value: 'bottom' },
                            ] }
                            onChange={ ( value ) => { setAttributes( { imagesVerticalAlign: value } ); } }
                        />

                        <ToggleControl 
                            label={ __( 'Use responsive options', 'citadela-directory' ) }
                            checked={ useResponsiveOptionsImageHeight }
                            onChange={ (value) => { setAttributes( { useResponsiveOptionsImageHeight: value } ) }}
                        />
                        { useResponsiveOptionsImageHeight 
                        ? 
                            <div class="citadela-responsive-settings-holder">
                                <ResponsiveOptionsTabs activeTab={ this.state.responsiveTabHeight } onChange={ (value) => { this.setState( { responsiveTabHeight: value } ) } } />
                                { responsiveSettings }
                            </div>
                        :
                            <>
                                { responsiveSettings }
                            </>
                        }
                    </PanelBody>
                    
                    <PanelBody
                        title={__('Gallery Options', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <SelectControl
                        value={ captionPosition }
                        label={ __('Images caption text', 'citadela-directory') } 
                        options={ [
                            { label: __( 'Do not show', 'citadela-directory' ), value: 'hidden' },
                            { label: __( 'Inside image', 'citadela-directory' ), value: 'inside-image' },
                            { label: __( 'Under image', 'citadela-directory' ), value: 'under-image' },
                        ] }
                        onChange={ ( value ) => setAttributes( { captionPosition: value } ) }
                        />                        

                    </PanelBody>

                    
                    <CarouselPanel
                        attributes={ attributes }
                        onChange={ ( values ) => this.props.setAttributes( 
                            { 
                                useCarousel: values.useCarousel,
                                carouselNavigation: values.carouselNavigation,
                                carouselPagination: values.carouselPagination,
                                carouselAutoplay: values.carouselAutoplay,
                                carouselAutoHeight: values.carouselAutoHeight,
                                carouselAutoplayDelay: values.carouselAutoplayDelay,
                                carouselLoop: values.carouselLoop,
                            } 
                            ) }
                    />

                    { activeProPlugin &&
                    <PanelBody
                        title={__('Design Options', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <BaseControl
                            label={ __('Text color', 'citadela-directory') }
                            className="block-editor-panel-color-settings"
                        >
                            { textColor && <ColorIndicator colorValue={ textColor } /> }
                            <ColorPalette
                                value={ textColor }
                                onChange={ (value) => { setAttributes( { textColor: value } ); } }
                                colors={ colorsSet }
                            />
                        </BaseControl>

                        { ( useCarousel && ( carouselPagination || carouselNavigation ) ) &&
                            <BaseControl
                                label={ __('Carousel decoration color', 'citadela-directory') }
                                className="block-editor-panel-color-settings"
                            >
                                { carouselColor && <ColorIndicator colorValue={ carouselColor } /> }
                                <ColorPalette
                                    value={ carouselColor }
                                    onChange={ (value) => { setAttributes( { carouselColor: value } ); } }
                                    colors={ colorsSet }
                                />
                            </BaseControl>
                        }
                    </PanelBody>
                    }
                    
                </InspectorControls>

                <div className={classNames(
                        "wp-block-citadela-blocks",
                        "ctdl-item-gallery",
                        attributes.className,
                        gridType,
                        gridLayout,
                        `size-${size}`,
                        `image-vertical-align-${imagesVerticalAlign}`,
                        gridLayout == 'layout-default' ? 'items-auto' : null,
                        { "custom-carousel-color": activeProPlugin && carouselColor ? true : false },
                        { "use-carousel": useCarousel ? true : false },
                        { "carousel-navigation": useCarousel && carouselNavigation ? true : false },
                        { "carousel-pagination": useCarousel && carouselPagination ? true : false },
                        { "carousel-autoheight": useCarousel && carouselAutoHeight ? true : false },
                        `image-size-${imageSize}`,
                        captionPosition !== 'hidden' ? `caption-${captionPosition}` : null,
                        this.state.responsiveTabHeight == 'desktop' && proportionalImageHeight ? 'proportional-image-height' : null,
                        this.state.responsiveTabHeight == 'mobile' && proportionalImageHeightMobile ? 'proportional-image-height' : null,
                        this.state.responsiveTabHeight == 'desktop' && ! proportionalImageHeight ? `${imageHeightType}-image-height` : null,
                        this.state.responsiveTabHeight == 'mobile' && ! proportionalImageHeightMobile ? `${imageHeightTypeMobile}-image-height` : null,
                        this.state.responsiveTabHeight == 'desktop' && ! proportionalImageHeight ? `image-position-${imageObjectPosition.replace(" ", "-" )}` : null,
                        this.state.responsiveTabHeight == 'mobile' && ! proportionalImageHeightMobile ? `image-position-${imageObjectPositionMobile.replace(" ", "-" )}` : null,
                    )}
                >
                    <StateIcons 
                        useResponsiveOptions= { useResponsiveOptionsImageHeight } 
                        isSelected={ isSelected } 
                        currentView={ this.state.responsiveTabHeight }
                        carouselAutoplay={ useCarousel && carouselAutoplay }
                        carouselLoop={ useCarousel && carouselLoop }
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

                    <div class="citadela-block-header">
                        <RichText
                            tagName='h3'
                            value={ attributes.title }
                            onChange={ (title) => setAttributes( { title } ) }
                            placeholder={ block.title }
                            keepPlaceholderOnFocus={true}
                            allowedFormats={ [] }
                        />
                    </div>

                    <div class="citadela-block-articles">
                        <div class="citadela-block-articles-wrap">
                            {Array.from(Array(numberOfItems), (e, i) => {
                                return (
                                    <article class="citadela-directory-gallery-item">
                                        <div class="item-content" style={ itemContentStyle }>
                                            <div class="item-thumbnail" style={ thumbnailStyle }></div>
                                            { captionPosition !== 'hidden' && 
                                                <div class="caption"></div>
                                            }
                                        </div>
                                    </article>
                                );
                            })}
                        </div>
                        

                    </div>
                    
                    { useCarousel && carouselNavigation &&
                        <div class="carousel-navigation-wrapper" style={ carouselStyle }>
                            <div class="carousel-button-prev"><i class="fas fa-chevron-left"></i></div>
                            <div class="carousel-button-next"><i class="fas fa-chevron-right"></i></div>
                        </div>
                    }

                    { useCarousel && carouselPagination &&
                        <div class="carousel-pagination-wrapper" style={ carouselStyle }>
                            <span class="carousel-bullet"></span>
                            <span class="carousel-bullet"></span>
                            <span class="carousel-bullet"></span>
                        </div>
                    }
                    
                </div>
            </>
        );
    }
}


const ImageHeightOptions = ({
	attributes,
	setAttributes,
}) => {
	const { 
		imageHeightUnit,
		imageHeight,
	} = attributes;

	const [ heightState, setHeight ] = useState( imageHeight );
		
	let unitDefaults = [];
	unitDefaults["px"] = 250;
	unitDefaults["vh"] = 100;
	unitDefaults["vw"] = 30;

	let unitStep = 1;
	if( imageHeightUnit != "px" ){
		unitStep = 0.1;
	}

	return (
		<>
			<BaseControl 
				label={ __('Image height unit', 'citadela-directory') } 
				>
				<SelectControl
					value={ imageHeightUnit }
					options={ [
						{ label: 'px', value: 'px' },
						{ label: 'vh', value: 'vh' },
						{ label: 'vw', value: 'vw' },
					] }
					onChange={ ( value ) => {
						setHeight( unitDefaults[ value ] );
						setAttributes( { 
							imageHeightUnit: value,
							imageHeight: unitDefaults[value],
						} );
					} 
					}
				/>
			</BaseControl>
			<BaseControl 
				label={ __( 'Image height', 'citadela-directory' ) + ( imageHeight !== undefined ? `: ${imageHeight}${imageHeightUnit}` : ': ' + __( 'default image size', 'citadela-directory' ) ) } 
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
							newValue = unitDefaults[ imageHeightUnit ];
						}
						if(imageHeightUnit != "px"){
							setAttributes( { imageHeight: newValue ? parseFloat(newValue) : newValue } );
						}else{
							setAttributes( { imageHeight: newValue ? parseInt(newValue) : newValue } );
						}
					} }
				/>
			</BaseControl>

		</>
	)
}

const ImageHeightMobileOptions = ({
	attributes,
	setAttributes,
}) => {
	const { 
		imageHeightUnitMobile,
		imageHeightMobile,
	} = attributes;

	const [ heightState, setHeight ] = useState( imageHeightMobile );
		
	let unitDefaults = [];
	unitDefaults["px"] = 250;
	unitDefaults["vh"] = 100;
	unitDefaults["vw"] = 30;

	let unitStep = 1;
	if( imageHeightUnitMobile != "px" ){
		unitStep = 0.1;
	}

	return (
		<>
			<BaseControl 
				label={ __('Image height unit', 'citadela-directory') } 
				>
				<SelectControl
					value={ imageHeightUnitMobile }
					options={ [
						{ label: 'px', value: 'px' },
						{ label: 'vh', value: 'vh' },
						{ label: 'vw', value: 'vw' },
					] }
					onChange={ ( value ) => {
						setHeight( unitDefaults[ value ] );
						setAttributes( { 
							imageHeightUnitMobile: value,
							imageHeightMobile: unitDefaults[value],
						} );
					} 
					}
				/>
			</BaseControl>
			<BaseControl 
				label={ __( 'Image height', 'citadela-directory' ) + ( imageHeightMobile !== undefined ? `: ${imageHeightMobile}${imageHeightUnitMobile}` : ': ' + __( 'default image size', 'citadela-directory' ) ) } 
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
							newValue = unitDefaults[ imageHeightUnitMobile ];
						}
						if(imageHeightUnitMobile != "px"){
							setAttributes( { imageHeightMobile: newValue ? parseFloat(newValue) : newValue } );
						}else{
							setAttributes( { imageHeightMobile: newValue ? parseInt(newValue) : newValue } );
						}
					} }
				/>
			</BaseControl>

		</>
	)
}

const MobileWidthBreakpoint = ( { attributes, setAttributes } ) => {
	const { 
		breakpointMobileImageHeight,
	} = attributes;

	const [ inputBreakpointMobileValue, setBreakpointMobileInputValue ] = useState( breakpointMobileImageHeight );

	return (
		<BaseControl 
			label={ __( 'Mobile width breakpoint', 'citadela-directory' ) }
			help={ __( 'Responsive options applied under screen width', 'citadela-directory' ) + ` ${breakpointMobileImageHeight}px` }
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
					setAttributes( { breakpointMobileImageHeight: newValue ? parseInt( newValue ) : newValue } );
				} }
				step={ 1 }
			/>
		</BaseControl>
	)
}