import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { RichText } = wp.blockEditor;
const { Fragment } = wp.element;
const { TextControl, BlockTitle, Icon } = wp.components;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Item Opening Hours', 'citadela-directory' ),
	description: __( 'Displays opening hours of specific listing item.', 'citadela-directory' ),
	edit( { attributes, setAttributes, name } ) {
        const block = wp.blocks.getBlockType(name);

        return (
			<div className={classNames(
                    "wp-block-citadela-blocks ctdl-item-opening-hours",
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
                        {Array.from(Array(7), (e, i) => {
                            return (
                                <div class="oh-day">
                                    <div class="oh-label"></div>
                                    <div class="oh-data"></div>
                                </div>
                            );
                        })}
                    </div>
                </div>

                <div class="citadela-block-note">
                    <span class="line"></span>
                </div>

			</div>
		);
	},
	save: () => {
		return null;
	}
} );