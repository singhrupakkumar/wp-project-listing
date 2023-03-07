import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { Icon } = wp.components;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Item Content", 'citadela-directory' ),
	description: __( "Displays content written in visual editor of specific listing item.", 'citadela-directory' ),
	icon: 'text',
	category: 'citadela-directory-blocks',

	edit: ({ attributes, name }) => {
        const block = wp.blocks.getBlockType(name);

		return (
            <div className={classNames(
                    "wp-block-citadela-blocks ctdl-item-content",
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

                <div class="item-content">
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                </div>
            </div>
        );
	},
	save: () => {
		return null;
	}
} );