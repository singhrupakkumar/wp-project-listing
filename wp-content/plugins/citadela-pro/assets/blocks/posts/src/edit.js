import ArticleExample from '../../components/article-example';
import CategorySelect from '../../components/category-select';
import ToolbarLayout from '../../components/toolbar-layout';
import ToolbarSize from '../../components/toolbar-size';
import CustomColorControl from '../../components/custom-color-control';

const { __ } = wp.i18n;
const { apiFetch } = wp;
const { Component, Fragment } = wp.element;
const { BlockControls, InspectorControls, RichText } = wp.blockEditor;
const { ToolbarGroup, ToolbarItem, PanelBody, ToggleControl, RangeControl, Icon, SelectControl, RadioControl, ColorPalette, ColorIndicator, BaseControl } = wp.components;

export default class Edit extends Component {
    constructor() {
        super( ...arguments );

        this.state = {
            categoriesList: [],
        };
    }

    componentDidMount() {
        this.fetchRequest = apiFetch( {
			path: `/wp/v2/categories/?per_page=-1`,
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
            layout,
            count, 
            postsOrder, 
            postsOrderBy, 
            stickyPostsFirst, 
            borderColor,
            backgroundColor,
            textColor,
            decorColor,
            borderWidth,
            skipStartPosts,
            dateColor,
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

        const activeDirectoryPlugin = typeof CitadelaDirectorySettings !== 'undefined';

        const block = wp.blocks.getBlockType(name);
        
        //set grid type
        let gridType = "grid-type-1";
        if( attributes.layout == "list"){
            gridType = "grid-type-3";
        }
        if( attributes.layout == "simple"){
            gridType = "";
        }

        return (
            <Fragment>
                <BlockControls key='controls'>
                <ToolbarGroup>
                        <ToolbarItem>
                            { ( toggleProps ) => ( 
                                <ToolbarLayout 
                                    allowedLayouts={ ['simple', 'list', 'box'] } 
                                    value={ attributes.layout } 
                                    onChange={ ( value ) => setAttributes( { layout: value } ) } 
                                    toggleProps={ toggleProps }
                                />
                            )}
                        </ToolbarItem>
                        { ['list', 'box'].includes(attributes.layout) && 
                            <ToolbarItem>
                                { ( toggleProps ) => ( 
                                    <ToolbarSize 
                                        value={ attributes.size } 
                                        onChange={ ( value ) => setAttributes( { size: value } ) } 
                                        toggleProps={ toggleProps }
                                    />
                                )}
                            </ToolbarItem>
                        }
                    </ToolbarGroup>
                </BlockControls>

                <InspectorControls key='inspector'>
                    <PanelBody
                        title={__('Filters', 'citadela-pro')}
                        initialOpen={true}
                        className="citadela-panel"
                    >
                        <CategorySelect
                            categoriesList={ categoriesList }
                            label={ __( 'Category', 'citadela-pro' ) }
                            noOptionLabel={ __( 'All', 'citadela-pro' ) }
                            selectedCategoryId={ attributes.category }
                            onChange={ ( value ) => { setAttributes( { category: value } ) } }
                        />
                        <RangeControl
                            label={ __( 'Number of items', 'citadela-pro' ) }
                            value={ attributes.count }
                            onChange={ (value) => {
                                setAttributes( { count: value } );
                            } }
                            min={ 1 }
                            max={ 80 }
                        />
                        <RangeControl
                            label={ __( 'Skip number of posts from start', 'citadela-pro' ) }
                            value={ skipStartPosts }
                            onChange={ (value) => {
                                setAttributes( { skipStartPosts: value } );
                            } }
                            min={ 0 }
                            max={ 12 }
                        />

                        <ToggleControl
                            label={__('Sticky posts first', 'citadela-pro')}
                            help={ __('If enabled, sticky posts will be displayed on the top of listed posts.', 'citadela-pro') }
                            checked={ stickyPostsFirst }
                            onChange={ ( checked ) => setAttributes( { stickyPostsFirst: checked } ) }
                        />
                    </PanelBody>

                    <PanelBody
                        title={ __( 'Order Options', 'citadela-pro' ) }
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <SelectControl
                            label={ __( 'Order by', 'citadela-pro' ) }
                            value={ postsOrderBy }
                            options={ [
                                { label: __( 'Date', 'citadela-pro' ), value: 'date' },
                                { label: __( 'Title', 'citadela-pro' ), value: 'title' },
                                { label: __( 'Random', 'citadela-pro' ), value: 'rand' },
                            ] }
                            onChange={ ( value ) => {
                                    setAttributes( { postsOrderBy: value } );
                                }
                            }
                        />

                        { postsOrderBy != 'rand' && 
                            <RadioControl
                                selected={ postsOrder }
                                options={ [
                                    { label: __( 'Descending', 'citadela-pro' ), value: 'DESC' },
                                    { label: __( 'Ascending', 'citadela-pro' ), value: 'ASC' },
                                ] }
                                onChange={ ( value ) => {
                                        setAttributes( { postsOrder: value } );
                                    }
                                }
                            />
                        }

                    </PanelBody>

                    <PanelBody
                        title={__('Post Details', 'citadela-pro')}
                        initialOpen={true}
                        className="citadela-panel"
                    >
                        <ToggleControl
                            label={__('Show featured image', 'citadela-pro')}
                            checked={ attributes.showFeaturedImage }
                            onChange={ ( checked ) => setAttributes( { showFeaturedImage: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show date', 'citadela-pro')}
                            checked={ attributes.showDate }
                            onChange={ ( checked ) => setAttributes( { showDate: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show description', 'citadela-pro')}
                            checked={ attributes.showDescription }
                            onChange={ ( checked ) => setAttributes( { showDescription: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show categories', 'citadela-pro')}
                            checked={ attributes.showCategories }
                            onChange={ ( checked ) => setAttributes( { showCategories: checked } ) }
                        />
                        { activeDirectoryPlugin && 
                            <ToggleControl
                                label={__('Show locations', 'citadela-pro')}
                                checked={ attributes.showLocations }
                                onChange={ ( checked ) => setAttributes( { showLocations: checked } ) }
                            />
                        }

                    </PanelBody>

                    <PanelBody
                        title={__('Design Options', 'citadela-pro')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <BaseControl
                            label={ __('Decoration color', 'citadela-pro') }
                            className="block-editor-panel-color-settings"
                        >
                            { decorColor && <ColorIndicator colorValue={ decorColor } /> }
                            <ColorPalette
                                value={ decorColor }
                                onChange={ (value) => { setAttributes( { decorColor: value } ); } }
                                colors={ colorsSet }
                            />
                        </BaseControl>

                        { layout == 'box' &&
                            <BaseControl
                                label={ __('Date color', 'citadela-pro') }
                                className="block-editor-panel-color-settings"
                            >
                                { dateColor && <ColorIndicator colorValue={ dateColor } /> }
                                <ColorPalette
                                    value={ dateColor }
                                    onChange={ (value) => { setAttributes( { dateColor: value } ); } }
                                    colors={ colorsSet }
                                />
                            </BaseControl>
                        }

                        <BaseControl
                            label={ __('Text color', 'citadela-pro') }
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
                            label={ __('Background color', 'citadela-pro') }
                            color={ backgroundColor }
                            onChange={ (value) => { setAttributes( { backgroundColor: value } ); } }
                        />

                        <CustomColorControl 
                            label={ __('Borders color', 'citadela-pro') }
                            color={ borderColor }
                            onChange={ (value) => { setAttributes( { borderColor: value } ); } }
                        />
                        
                        <SelectControl
                            label={ __( 'Borders width', 'citadela-pro' ) }
                            value={ borderWidth }
                            options={ [
                                { label: __( 'No borders', 'citadela-pro' ), value: 'none' },
                                { label: __( 'Thin borders', 'citadela-pro' ), value: 'thin' },
                                { label: __( 'Thick borders', 'citadela-pro' ), value: 'thick' },
                            ] }
                            onChange={ ( value ) => { setAttributes( { borderWidth: value } ); }
                        }
                        />
                    </PanelBody>

                </InspectorControls>

                <div className={classNames(
                    "wp-block-citadela-blocks",
                    "ctdl-posts",
                    attributes.className,
                    gridType,
                    `border-${borderWidth}`,
                    `layout-${attributes.layout}`,
                    { [ `size-${attributes.size}` ]: attributes.layout == 'simple' ? false : true },
                    { "show-item-featured-image": attributes.showFeaturedImage ? true : false },
                    { "show-item-date": attributes.showDate ? true : false },
                    { "show-item-description": attributes.showDescription ? true : false },
                    { "show-item-categories": attributes.showCategories ? true : false },
                    { "show-item-locations": activeDirectoryPlugin && attributes.showLocations ? true : false },
                    { "custom-text-color": textColor ? true : false },
                    { "custom-decor-color": decorColor ? true : false },
                    { "custom-background-color": backgroundColor ? true : false },
                    { "custom-date-color": dateColor ? true : false },
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

                    <div class="citadela-block-header">
                        <RichText
                            tagName='h3'
                            value={ attributes.title }
                            onChange={ (value) => setAttributes( { title: value } ) }
                            placeholder={ block.title }
                            keepPlaceholderOnFocus={true}
                            allowedFormats={ [] }
                        />
                    </div>

                    <div class="citadela-block-articles">
                        <ArticleExample attributes={attributes} count={ count } />
                    </div>
                </div>
            </Fragment>
        );

    }
}
