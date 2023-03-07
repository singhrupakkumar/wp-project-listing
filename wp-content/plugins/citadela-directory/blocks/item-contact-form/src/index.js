import ItemContactFormEdit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Item Contact Form', 'citadela-directory' ),
	description: __( 'Displays contact form which send email directly to email address of specific listing item.', 'citadela-directory'),
	edit: ItemContactFormEdit,
	save: () => {
		return null;
	}
} );