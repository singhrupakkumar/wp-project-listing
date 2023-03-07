import metadata from './block.json';

const { __ } = wp.i18n;
const { Fragment } = wp.element;
const { registerBlockType } = wp.blocks;
const { BlockControls, InspectorControls } = wp.blockEditor;
const { ToolbarGroup, ToolbarItem, PanelBody, ToggleControl, Icon, SelectControl, RadioControl } = wp.components;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Default Search Results', 'citadela-directory' ),
	description: __( 'Displays posts based on current results on default WordPress search results page.', 'citadela-directory' ),
	edit: ({ name, attributes, setAttributes }) => {
        const block = wp.blocks.getBlockType(name);
        const WPpostsPerPage = parseInt(CitadelaDirectorySettings.wpSettings.postsPerPage);

		return (
            <Fragment>
                <div className={classNames(
                        "wp-block-citadela-blocks",
                        "ctdl-default-search-results",
                        attributes.className,
                        "layout-list",
                        "show-item-featured-image",
                        "show-item-date",
                        "show-item-description",
                        "show-item-categories"
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

                    {Array.from(Array(WPpostsPerPage), (e, i) => {
                        return (
                            <article class="citadela-article">
                                <div class="item-content">
                                    <div class="item-thumbnail"></div>
                                    <div class="item-body">
                                        <div class="item-title">
                                            <div class="item-title-wrap">
                                                <div class="post-title"></div>
                                                <div class="post-subtitle"></div>
                                            </div>
                                        </div>

                                        <div class="item-text">
                                            <div class="item-description">
                                                <div class="item-date"></div>
                                                <span class="line"></span>
                                                <span class="line"></span>
                                                <span class="line"></span>
                                                <span class="line"></span>
                                            </div>
                                        </div>

                                        <div class="item-footer">
                                            <div class="item-data categories">
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
            </Fragment>
        );
	},
	save: () => {
		return null;
	}
} );