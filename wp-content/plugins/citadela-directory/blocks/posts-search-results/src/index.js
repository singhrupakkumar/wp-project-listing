import ToolbarLayout from '../../components/toolbar-layout';
import ToolbarSize from '../../components/toolbar-size';
import CustomColorControl from '../../components/custom-color-control';
import metadata from './block.json';

const { __ } = wp.i18n;
const { Fragment } = wp.element;
const { registerBlockType } = wp.blocks;
const { BlockControls, InspectorControls } = wp.blockEditor;
const { ToolbarGroup, ToolbarItem, PanelBody, ToggleControl, Icon, SelectControl, RadioControl, RangeControl, BaseControl, ColorPalette, ColorIndicator } = wp.components;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Posts Search Results', 'citadela-directory' ),
	description: __( 'Displays posts based on current results from search form.', 'citadela-directory' ),
	edit: ({ name, attributes, setAttributes }) => {
        const block = wp.blocks.getBlockType(name);
        const { 
            layout,
            size,
            showFeaturedImage,
            showDate,
            showDescription,
            showCategories,
            showLocations,
            postsOrder,
            postsOrderBy,
            borderColor,
            backgroundColor,
            textColor,
            decorColor,
            borderWidth,
            skipStartPosts,
            dateColor,
        } = attributes;

        const activeProPlugin = typeof CitadelaProSettings === "undefined" ? false : true;
        const WPpostsPerPage = parseInt(CitadelaDirectorySettings.wpSettings.postsPerPage);
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

        //set grid type
        var gridType = "grid-type-1";
        if( layout == "list"){
            gridType = "grid-type-3";
        }
        if( layout == "simple"){
            gridType = "";
        }

        //styles
        let thumbnailStyle = {};
        let articleStyle = {};
        let itemContentStyle = {};
        let footerStyle = {};
        let itemDataStyle = {};
        let dateStyle = {};
        let entryMeta = {};

        if( activeProPlugin ){
            thumbnailStyle = {
                ...( decorColor ? { color: decorColor } : {} ),
            };

            articleStyle = {
                ...( layout == "simple" && textColor ? { color: textColor } : {} ),
                ...( layout == "simple" && backgroundColor ? { backgroundColor: backgroundColor } : {} ),
                ...( layout == "simple" && borderColor ? { borderColor: borderColor } : {} ),
            };

            itemContentStyle = {
                ...( layout != "simple" && textColor ? { color: textColor } : {} ),
                ...( layout != "simple" && backgroundColor ? { backgroundColor: backgroundColor } : {} ),
                ...( layout != "simple" && borderColor ? { borderColor: borderColor } : {} ),
            };

            footerStyle = {
                ...( layout != "simple" && borderColor ? { borderColor: borderColor } : {} ),
            };

            itemDataStyle = {
                ...( layout != "simple" && borderColor ? { borderColor: borderColor } : {} ),
                ...( decorColor ? { color: decorColor } : {} ),
                ...( decorColor ? { borderColor: decorColor } : {} ),
            };

            dateStyle = {
                ...( layout == "list" && decorColor ? { color: decorColor } : {} ),
                ...( layout == "box" && decorColor ? { backgroundColor: decorColor } : {} ),
                ...( layout == "box" && dateColor ? { color: dateColor } : {} ),
            };

            entryMeta = {
                ...( layout == "simple" && decorColor ? { color: decorColor } : {} ),
            };
        }

		return (
            <Fragment>
                <BlockControls key='controls'>
                    <ToolbarGroup>
                        <ToolbarItem>
                            { ( toggleProps ) => (
                                <ToolbarLayout 
                                    allowedLayouts={ ['simple', 'list', 'box'] } 
                                    value={ layout } 
                                    onChange={ ( value ) => setAttributes( { layout: value } ) }
                                    toggleProps={ toggleProps }
                                />
                                )}
                        </ToolbarItem>
                    
                    {['list', 'box'].includes(layout) && 
                        <ToolbarItem>
                            { ( toggleProps ) => (
                                <ToolbarSize 
                                    value={ size } 
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
                        title={__('Filters', 'citadela-directory')}
                        initialOpen={true}
                        className="citadela-panel"
                    >
                        <RangeControl
                            label={ __( 'Skip number of posts from start', 'citadela-directory' ) }
                            value={ skipStartPosts }
                            onChange={ (value) => {
                                setAttributes( { skipStartPosts: value } );
                            } }
                            min={ 0 }
                            max={ 12 }
                        />
                    </PanelBody>
                    <PanelBody
                        title={ __( 'Order Options', 'citadela-directory' ) }
                        initialOpen={true}
                        className="citadela-panel"
                    >
                        <SelectControl
                            label={ __( 'Order by', 'citadela-directory' ) }
                            value={ postsOrderBy }
                            options={ [
                                { label: __( 'Date', 'citadela-directory' ), value: 'date' },
                                { label: __( 'Title', 'citadela-directory' ), value: 'title' },
                                { label: __( 'Random', 'citadela-directory' ), value: 'rand' },
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
                                    { label: __( 'Descending', 'citadela-directory' ), value: 'DESC' },
                                    { label: __( 'Ascending', 'citadela-directory' ), value: 'ASC' },
                                ] }
                                onChange={ ( value ) => {
                                        setAttributes( { postsOrder: value } );
                                    }
                                }
                            />
                        }

                    </PanelBody>

                    <PanelBody
                        title={__('Item Details', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <ToggleControl
                            label={__('Show featured image', 'citadela-directory')}
                            checked={ showFeaturedImage }
                            onChange={ ( checked ) => setAttributes( { showFeaturedImage: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show date', 'citadela-directory')}
                            checked={ showDate }
                            onChange={ ( checked ) => setAttributes( { showDate: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show description', 'citadela-directory')}
                            checked={ showDescription }
                            onChange={ ( checked ) => setAttributes( { showDescription: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show categories', 'citadela-directory')}
                            checked={ showCategories }
                            onChange={ ( checked ) => setAttributes( { showCategories: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show locations', 'citadela-directory')}
                            checked={ showLocations }
                            onChange={ ( checked ) => setAttributes( { showLocations: checked } ) }
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

                            { layout == 'box' &&
                                <BaseControl
                                    label={ __('Date color', 'citadela-directory') }
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
                        </PanelBody>
                    }
                </InspectorControls>

                <div className={classNames(
                        "wp-block-citadela-blocks",
                        "ctdl-posts-search-results",
                        attributes.className,
                        gridType,
                        "layout-"+layout,
                        "size-"+size,
                        `border-${borderWidth}`,
                        { "show-item-featured-image": showFeaturedImage ? true : false },
                        { "show-item-date": attributes.showDate ? true : false },
                        { "show-item-description": showDescription ? true : false },
                        { "show-item-categories": showCategories ? true : false },
                        { "show-item-locations": showLocations ? true : false },
                        { "custom-text-color": activeProPlugin && textColor ? true : false },
                        { "custom-decor-color": activeProPlugin && decorColor ? true : false },
                        { "custom-background-color": activeProPlugin && backgroundColor ? true : false },
                        { "custom-date-color": activeProPlugin && dateColor ? true : false },
                    )}
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

                    <div class="citadela-block-articles">
                        <div class="citadela-block-articles-wrap">
                            {Array.from(Array(WPpostsPerPage), (e, i) => {
                                if ( layout == 'simple' ) {
                                    return (
                                        <article class="citadela-article" style={ articleStyle }>
                                            <div class="item-content" style={ itemContentStyle }>
                                                
                                                <div class="item-title">
                                                    <div class="item-title-wrap">
                                                        <div class="post-title"></div>
                                                        <div class="post-meta" style={ entryMeta }></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="item-thumbnail" style={ thumbnailStyle }></div>
                                                
                                                <div class="item-body">
                                                    <div class="item-text">
                                                        <div class="item-description">
                                                            <span class="line"></span>
                                                            <span class="line"></span>
                                                            <span class="line"></span>
                                                            <span class="line"></span>
                                                        </div>
                                                    </div>

                                                    <div class="item-footer" style={ footerStyle }>
                                                        <div class="item-data location" style={ itemDataStyle }>
                                                            <span class="label"></span>
                                                            <span class="values"></span>
                                                        </div>
                                                        <div class="item-data categories" style={ itemDataStyle }>
                                                            <span class="label"></span>
                                                            <span class="values"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </article>
                                    );
                                } else {
                                    return (
                                        <article class="citadela-article" style={ articleStyle }>
                                            <div class="item-content" style={ itemContentStyle }>
                                                <div class="item-thumbnail" style={ thumbnailStyle }>
                                                    { layout == "box" &&
                                                        <div class="item-date" style={ dateStyle }></div>
                                                    }
                                                </div>
                                                <div class="item-body">
                                                    <div class="item-title">
                                                        <div class="item-title-wrap">
                                                            { layout == "box" && ! showFeaturedImage  &&
                                                                <div class="item-date" style={ dateStyle }></div>
                                                            }
                                                            <div class="post-title"></div>
                                                            { layout == "list" && ! showDescription  &&
                                                                <div class="item-date" style={ dateStyle }></div>
                                                            }
                                                            <div class="post-subtitle"></div>
                                                        </div>
                                                    </div>
    
                                                    <div class="item-text">
                                                        <div class="item-description">
                                                            { layout == "list" && showDescription &&
                                                                <div class="item-date" style={ dateStyle }></div>
                                                            }
                                                            <span class="line"></span>
                                                            <span class="line"></span>
                                                            <span class="line"></span>
                                                            <span class="line"></span>
                                                        </div>
                                                    </div>
    
                                                    <div class="item-footer" style={ footerStyle }>
                                                        <div class="item-data address" style={ itemDataStyle }>
                                                            <span class="label"></span>
                                                            <span class="values"></span>
                                                        </div>
    
                                                        <div class="item-data web" style={ itemDataStyle }>
                                                            <span class="label"></span>
                                                            <span class="values"></span>
                                                        </div>
                                                        
                                                        <div class="item-data location" style={ itemDataStyle }>
                                                            <span class="label"></span>
                                                            <span class="values"></span>
                                                        </div>

                                                        <div class="item-data categories" style={ itemDataStyle }>
                                                            <span class="label"></span>
                                                            <span class="values"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                    );
                                }
                            })}
                        </div>
                    </div>
                </div>
            </Fragment>
        );
	},
	save: () => {
		return null;
	}
} );