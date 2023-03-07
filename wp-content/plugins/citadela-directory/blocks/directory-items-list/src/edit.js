import ImageSizes from '../../components/image-sizes';
import ResponsiveOptionsTabs from '../../components/responsive-options-tabs';
import StateIcons from '../../components/state-icons';
import ToolbarLayout from '../../components/toolbar-layout';
import ToolbarSize from '../../components/toolbar-size';
import CategorySelect from '../../components/category-select';
import CarouselPanel from '../../components/panel-carousel';
import CustomColorControl from '../../components/custom-color-control';

const { apiFetch } = wp;
const { Component, useState } = wp.element;
const { __ } = wp.i18n;
const { BlockControls, InspectorControls, RichText, } = wp.blockEditor;
const { 
    ToolbarGroup, 
    ToolbarItem,
    PanelBody, 
    RangeControl, 
    ToggleControl, 
    Icon, 
    Tooltip, 
    SelectControl, 
    RadioControl, 
    ColorPalette, 
    ColorIndicator, 
    BaseControl,
    TextControl,
} = wp.components;

const CATEGORIES_LIST_QUERY = {
	per_page: -1,
};

export default class Edit extends Component {
    constructor() {
        super( ...arguments );
        this.state = {
            categoriesList: [],
            locationsList: [],
            responsiveTabHeight: "desktop",
        };
    }

    componentDidMount() {
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

    render() {
        const activeProPlugin = typeof CitadelaProSettings === "undefined" ? false : true;

        const { categoriesList, locationsList } = this.state;
        const { attributes, setAttributes, name, isSelected } = this.props;
        const { 
            category,
            location,
            onlyFeaturedCategory,
            onlyFeatured,
            featuredFirst,
            numberOfItems, 
            layout, 
            size, 
            showItemFeaturedImage, 
            showItemSubtitle, 
            showItemDescription, 
            showItemAddress, 
            showItemWeb, 
            showItemCategories, 
            showItemLocations, 
            itemsOrderBy, 
            itemsOrder,
            useCarousel,
            carouselNavigation,
            carouselPagination,
            carouselAutoplay,
            carouselAutoHeight,
            carouselLoop,
            borderWidth,
            borderColor,
            backgroundColor,
            textColor,
            decorColor,
            carouselColor,
            showRating,
            ratingColor,
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

        const itemReviewsEnabled = CitadelaDirectorySettings.features.item_reviews;
        const showRatingGenerally = itemReviewsEnabled && showRating ? true : false;

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
        if( layout == "list"){
            gridType = "grid-type-3";
        }

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

        //styles
        let bordersStyle = {};
        let itemContentStyle = {};
        let thumbnailStyle = {};
        let articleStyle = {};
        let carouselStyle = {};
        let ratingStyle = {}
        
        thumbnailStyle = {
            ...thumbnailStyle,
            ...( ! currentAttr.proportionalImageHeight && currentAttr.imageHeightType == 'custom' && currentAttr.imageHeight !== undefined ? { height: currentAttr.imageHeight + currentAttr.imageHeightUnit } : {} ),
        }

        if( activeProPlugin ){
            bordersStyle = {
                ...( borderColor ? { borderColor: borderColor } : {} ),
            };
            itemContentStyle = {
                ...( borderColor ? { borderColor: borderColor } : {} ),
                ...( backgroundColor ? { backgroundColor: backgroundColor } : {} ),
                ...( textColor ? { textColor: textColor } : {} ),
            };
            thumbnailStyle = {
                ...thumbnailStyle,
                ...( decorColor ? { color: decorColor } : {} ),
            };
            articleStyle = {
                ...( textColor ? { color: textColor } : {} ),
            };
            carouselStyle = {
                ...( carouselColor ? { color: carouselColor } : {} ),
            };
            ratingStyle = {
                ...( ratingColor ? { color: ratingColor } : {} ),
            }
        }



        return (
            <>
                <BlockControls key='controls'>
                    <ToolbarGroup>
                        <ToolbarItem>
                            { ( toggleProps ) => (
                                <ToolbarLayout 
                                    value={ layout } 
                                    onChange={ ( value ) => setAttributes( { layout: value } ) } 
                                    toggleProps={ toggleProps }
                                />
                            )}
                        </ToolbarItem>
                        <ToolbarItem>
                            { ( toggleProps ) => (
                                <ToolbarSize 
                                    value={ size } 
                                    onChange={ ( value ) => setAttributes( { size: value } ) } 
                                    toggleProps={ toggleProps }
                                />
                            )}
                        </ToolbarItem>
                    </ToolbarGroup>
                </BlockControls>

                <InspectorControls key='inspector'>
                    
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
                        <RangeControl
                            label={ __( 'Number of items', 'citadela-directory' ) }
                            value={ numberOfItems }
                            onChange={ (value) => {
                                setAttributes( { numberOfItems: value } );
                            } }
                            min={ 1 }
                            max={ 50 }
                        />
                    </PanelBody>

                    <PanelBody
                        title={__('Item Details', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <ToggleControl
                            label={__('Show featured image', 'citadela-directory')}
                            checked={ showItemFeaturedImage }
                            onChange={ ( checked ) => this.props.setAttributes( { showItemFeaturedImage: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show subtitle', 'citadela-directory')}
                            checked={ showItemSubtitle }
                            onChange={ ( checked ) => this.props.setAttributes( { showItemSubtitle: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show description', 'citadela-directory')}
                            checked={ showItemDescription }
                            onChange={ ( checked ) => this.props.setAttributes( { showItemDescription: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show address', 'citadela-directory')}
                            checked={ showItemAddress }
                            onChange={ ( checked ) => this.props.setAttributes( { showItemAddress: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show web', 'citadela-directory')}
                            checked={ showItemWeb }
                            onChange={ ( checked ) => this.props.setAttributes( { showItemWeb: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show categories', 'citadela-directory')}
                            checked={ showItemCategories }
                            onChange={ ( checked ) => this.props.setAttributes( { showItemCategories: checked } ) }
                        />
                        { showItemCategories && 
                            <ToggleControl
                                label={__('Show only featured category', 'citadela-directory')}
                                checked={ onlyFeaturedCategory }
                                onChange={ ( checked ) => setAttributes( { onlyFeaturedCategory: checked } ) }
                            />
                        }
                        <ToggleControl
                            label={__('Show locations', 'citadela-directory')}
                            checked={ showItemLocations }
                            onChange={ ( checked ) => this.props.setAttributes( { showItemLocations: checked } ) }
                        />
                        {itemReviewsEnabled &&
                            <ToggleControl
                                label={__('Show rating', 'citadela-directory')}
                                checked={ showRating }
                                onChange={ ( checked ) => setAttributes( { showRating: checked } ) }
                            />
                        }

                    </PanelBody>

                    <PanelBody
                        title={ __( 'Order Options', 'citadela-directory' ) }
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <SelectControl
                            label={ __( 'Order by', 'citadela-directory' ) }
                            value={ itemsOrderBy }
                            options={ [
                                { label: __( 'Date', 'citadela-directory' ), value: 'date' },
                                { label: __( 'Title', 'citadela-directory' ), value: 'title' },
                                { label: __( 'Order number', 'citadela-directory' ), value: 'menu_order' },
                                { label: __( 'Random', 'citadela-directory' ), value: 'rand' },
                            ] }
                            onChange={ ( value ) => {
                                    setAttributes( { itemsOrderBy: value } );
                                }
                            }
                        />

                        { itemsOrderBy != 'rand' && 
                            <RadioControl
                                selected={ itemsOrder }
                                options={ [
                                    { label: __( 'Descending', 'citadela-directory' ), value: 'DESC' },
                                    { label: __( 'Ascending', 'citadela-directory' ), value: 'ASC' },
                                ] }
                                onChange={ ( value ) => {
                                        setAttributes( { itemsOrder: value } );
                                    }
                                }
                            />
                        }
                        <ToggleControl
							label={__('Featured items first', 'citadela-directory')}
                            help={__('Show featured items on the top of list before all other item posts.', 'citadela-directory')}
							checked={ featuredFirst }
							onChange={ ( checked ) => setAttributes( { featuredFirst: checked } ) }
						/>

                    </PanelBody>
                    
                    { activeProPlugin &&
                    <PanelBody
                        title={__('Design Options', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <BaseControl
                            label={ __('Decoration color', 'citadela-directory') }
                            className="block-editor-panel-color-settings"
                        >
                            { decorColor && <ColorIndicator colorValue={ decorColor } /> }
                            <ColorPalette
                                value={ decorColor }
                                onChange={ (value) => { setAttributes( { decorColor: value } ); } }
                                colors={ colorsSet }
                            />
                        </BaseControl>

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


                        <CustomColorControl 
                            label={ __('Background color', 'citadela-directory') }
                            color={ backgroundColor }
                            onChange={ (value) => { setAttributes( { backgroundColor: value } ); } }
                        />

                        <CustomColorControl 
                            label={ __('Borders color', 'citadela-directory') }
                            color={ borderColor }
                            onChange={ (value) => { setAttributes( { borderColor: value } ); } }
                        />
                        
                        { itemReviewsEnabled &&
                            <CustomColorControl 
                                label={ __('Rating stars color', 'citadela-directory') }
                                color={ ratingColor }
                                onChange={ (value) => { setAttributes( { ratingColor: value } ); } }
                                disableAlpha
                            />
                        }
                        
                        <SelectControl
                            label={ __( 'Borders width', 'citadela-directory' ) }
                            value={ borderWidth }
                            options={ [
                                { label: __( 'No borders', 'citadela-directory' ), value: 'none' },
                                { label: __( 'Thin borders', 'citadela-directory' ), value: 'thin' },
                                { label: __( 'Thick borders', 'citadela-directory' ), value: 'thick' },
                            ] }
                            onChange={ ( value ) => { setAttributes( { borderWidth: value } ); }
                        }
                        />
                        
                        { useCarousel && 
                            <>
                            { ( carouselPagination || carouselNavigation ) &&
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
                            </>
                        }


                    </PanelBody>
                    }
                    
                    
                    <PanelBody
                        title={__('Item Image Options', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <ImageSizes
                            value={ imageSize }
                            customSizes={ { citadela_item_thumbnail: __( 'Default', 'citadela-directory' ) } }
                            onChange={ ( value ) => setAttributes( { imageSize: value } ) }
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
                    
                </InspectorControls>

                <div className={classNames(
                        "wp-block-citadela-blocks",
                        "ctdl-directory-items-list",
                        attributes.className,
                        gridType,
                        `layout-${layout}`,
                        `size-${size}`,
                        activeProPlugin ? `border-${borderWidth}` : null,
                        { "custom-text-color": activeProPlugin && textColor ? true : false },
                        { "custom-decor-color": activeProPlugin && decorColor ? true : false },
                        { "custom-background-color": activeProPlugin && backgroundColor ? true : false },
                        { "custom-carousel-color": activeProPlugin && carouselColor ? true : false },
                        
                        { "show-item-featured-image": showItemFeaturedImage ? true : false },
                        { "show-item-subtitle": showItemSubtitle ? true : false },
                        { "show-item-description": showItemDescription ? true : false },
                        { "show-item-address": showItemAddress ? true : false },
                        { "show-item-web": showItemWeb ? true : false },
                        { "show-item-categories": showItemCategories ? true : false },
                        { "show-item-locations": showItemLocations ? true : false },
                        { "use-carousel": useCarousel ? true : false },
                        { "carousel-navigation": useCarousel && carouselNavigation ? true : false },
                        { "carousel-pagination": useCarousel && carouselPagination ? true : false },
                        { "carousel-autoheight": useCarousel && carouselAutoHeight ? true : false },
                        { "show-item-rating": showRatingGenerally ? true : false },
                        `image-size-${imageSize}`,
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
                                    <article class="citadela-article" style={ articleStyle }>
                                        <div class="item-content" style={ itemContentStyle }>
                                            <div class="item-thumbnail" style={ thumbnailStyle }></div>
                                            <div class="item-body">
                                                <div class="item-title">
                                                    <div class="item-title-wrap">
                                                        <div class="post-title"></div>
                                                        <div class="post-subtitle"></div>
                                                    </div>
                                                </div>

                                                { showRatingGenerally &&
                                                    <div class="item-rating" style={ ratingStyle }></div>
                                                }
                                                
                                                <div class="item-text">
                                                    <div class="item-description">
                                                        <span class="line"></span>
                                                        <span class="line"></span>
                                                        <span class="line"></span>
                                                        <span class="line"></span>
                                                    </div>
                                                </div>

                                                <div class="item-footer" style={ bordersStyle }>
                                                	<div class="item-data location" style={ bordersStyle }>
                                                        <span class="label"></span>
                                                        <span class="values"></span>
                                                    </div>

                                                    <div class="item-data address" style={ bordersStyle }>
                                                        <span class="label"></span>
                                                        <span class="values"></span>
                                                    </div>

                                                    <div class="item-data web" style={ bordersStyle }>
                                                        <span class="label"></span>
                                                        <span class="values"></span>
                                                    </div>

                                                    <div class="item-data categories" style={ bordersStyle }>
                                                        <span class="label"></span>
                                                        <span class="values"></span>
                                                    </div>
                                                </div>
                                            </div>
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