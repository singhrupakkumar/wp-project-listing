import ToolbarLayout from '../../components/toolbar-layout';
import ToolbarSize from '../../components/toolbar-size';
import CategorySelect from '../../components/category-select';
import CarouselPanel from '../../components/panel-carousel';
import StateIcons from '../../components/state-icons';

const { apiFetch } = wp;
const { Component, Fragment } = wp.element;
const { __ } = wp.i18n;
const { BlockControls, InspectorControls, RichText } = wp.blockEditor;
const { ToolbarGroup, ToolbarItem, PanelBody, Icon, Tooltip, ToggleControl } = wp.components;
// const { withSelect } = wp.data;

const CATEGORIES_LIST_QUERY = {
	per_page: -1,
};

export class Edit extends Component {
    constructor() {
        super( ...arguments );
        this.state = {
            categoriesList: [],
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
    }

    render() {
        const { categoriesList } = this.state;
        const { attributes, setAttributes, name } = this.props;
        const { 
            category,
            layout,
            size, 
            showCategoryDescription, 
            showCategoryIcon, 
            onlyFeatured,
            useCarousel,
            carouselNavigation,
            carouselPagination,
            carouselAutoplay,
            carouselAutoHeight,
            carouselLoop,
         } = attributes;

        const block = wp.blocks.getBlockType(name);
        
        return (
            <Fragment>
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
                            noOptionLabel={ __( 'Only parent categories', 'citadela-directory' ) }
                            selectedCategoryId={ category }
                            onChange={ ( value ) => { setAttributes( { category: value } ) } }
                        />

                        <ToggleControl
                            label={__('Only featured', 'citadela-directory')}
                            help={__('Show only categories marked as featured.', 'citadela-directory')}
                            checked={ onlyFeatured }
                            onChange={ ( checked ) => setAttributes( { onlyFeatured: checked } ) }
                        />

                    </PanelBody>
                    <PanelBody
                        title={__('Category Details', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                            <ToggleControl
                                label={__('Show Category icon', 'citadela-directory')}
                                checked={ showCategoryIcon }
                                onChange={ ( checked ) => setAttributes( { showCategoryIcon: checked } ) }
                            />
                            <ToggleControl
                                label={__('Show Category description', 'citadela-directory')}
                                checked={ showCategoryDescription }
                                onChange={ ( checked ) => setAttributes( { showCategoryDescription: checked } ) }
                            />

                    </PanelBody>

                    <CarouselPanel
                        attributes={ attributes }
                        onChange={ ( values ) => setAttributes( 
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
                        "ctdl-directory-categories-list",
                        attributes.className,
                        "grid-type-2",
                        "items-auto", //class items-auto used for design purposes in admin to hide items in block preview
                        "layout-"+layout,
                        "size-"+size,
                        { "hide-description": !showCategoryDescription ? true : false },
                        { "hide-icon": !showCategoryIcon ? true : false },
                        { "use-carousel": useCarousel ? true : false },
                        { "carousel-navigation": useCarousel && carouselNavigation ? true : false },
                        { "carousel-pagination": useCarousel && carouselPagination ? true : false },
                    )}
                >

                    <StateIcons 
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
                            {Array.from(Array(12), (e, i) => {
                                return (
                                    <article class="folder-card">
                                        <div class="folder-header">
                                            <div class="folder-icon"></div>
                                        </div>

                                        <div class="folder-content">
                                            <div class="folder-content-wrap">
                                                <div class="folder-title"></div>
                                                <div class="folder-description">
                                                    <span class="line"></span>
                                                    <span class="line"></span>
                                                    <span class="line"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                );
                            })}
                        </div>
                        

                    </div>
                    
                    { useCarousel && carouselNavigation &&
                        <div class="carousel-navigation-wrapper">
                            <div class="carousel-button-prev"><i class="fas fa-chevron-left"></i></div>
                            <div class="carousel-button-next"><i class="fas fa-chevron-right"></i></div>
                        </div>
                    }

                    { useCarousel && carouselPagination &&
                        <div class="carousel-pagination-wrapper">
                            <span class="carousel-bullet"></span>
                            <span class="carousel-bullet"></span>
                            <span class="carousel-bullet"></span>
                        </div>
                    }
                    
                </div>
            </Fragment>
        );
    }
}

export default Edit;