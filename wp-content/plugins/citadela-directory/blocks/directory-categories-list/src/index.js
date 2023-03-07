import edit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Listing Categories List", 'citadela-directory' ),
	description: __( "Displays listing categories based on specific filter. It can display parent categories or subcategories from specific category.", 'citadela-directory' ),
	edit: edit,
	save: () => {
		return null;
	}
} );