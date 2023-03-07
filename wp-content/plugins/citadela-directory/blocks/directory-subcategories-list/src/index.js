import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;
const { BlockControls, InspectorControls, RichText } = wp.blockEditor;
const { PanelBody, Icon, BaseControl, ToggleControl } = wp.components;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Listing Subcategories List", 'citadela-directory' ),
	description: __( "Displays listing subcategories based on current category page.", 'citadela-directory' ),
	edit: ( props ) => {
        const { name, attributes, setAttributes } = props;
        const { onlyFeatured } = attributes;
        
        const block = wp.blocks.getBlockType(name);
        
        return (
            <Fragment>
                <InspectorControls key='inspector'>
                    <PanelBody
                        title={__('Filters', 'citadela-directory')}
                        initialOpen={true}
                        className="citadela-panel"
                    >
                        <ToggleControl
                            label={__('Only featured', 'citadela-directory')}
                            help={__('Show only categories marked as featured.', 'citadela-directory')}
                            checked={ onlyFeatured }
                            onChange={ ( checked ) => setAttributes( { onlyFeatured: checked } ) }
                        />
                    </PanelBody>
                </InspectorControls>
                
                <div className={classNames(
                        "wp-block-citadela-blocks ctdl-directory-subcategories-list grid-type-2 size-small items-auto",
                        attributes.className,
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
                </div>
            </Fragment>
        );
	},
	save: () => {
		return null;
	}
} );