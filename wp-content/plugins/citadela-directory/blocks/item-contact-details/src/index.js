import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { RichText } = wp.blockEditor;
const { Fragment } = wp.element;
const { Icon } = wp.components;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Item Contact Details', 'citadela-directory' ),
	description: __( 'Displays address, phone number and other contact information of specific listing item.', 'citadela-directory'),
	edit: ({ className, attributes, setAttributes, name }) => {
        const block = wp.blocks.getBlockType(name);

		return (
			<div className={classNames(
                    "wp-block-citadela-blocks ctdl-item-contact-details",
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
                        <div class="cd-info cd-address">
                            <div class="cd-label"></div>
                            <div class="cd-data"></div>
                        </div>

                        <div class="cd-info cd-gps">
                            <div class="cd-label"></div>
                            <div class="cd-data"></div>
                        </div>

                        <div class="cd-info cd-phone">
                            <div class="cd-label"></div>
                            <div class="cd-data"></div>
                        </div>

                        <div class="cd-info cd-mail">
                            <div class="cd-label"></div>
                            <div class="cd-data"></div>
                        </div>

                        <div class="cd-info cd-web">
                            <div class="cd-label"></div>
                            <div class="cd-data"></div>
                        </div>

                    </div>
                </div>
			</div>
		);
	},
	save: () => {
		return null;
	}
} );