import edit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
    title: __( "Item Featured Image", 'citadela-directory' ),
	description: __( 'Displays main item image of specific listing item.', 'citadela-directory' ),
	edit: edit,
	save: () => {
		return null;
	}
} );