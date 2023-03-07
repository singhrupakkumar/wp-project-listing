import edit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Listing Items List", 'citadela-directory' ),
	description: __( "Displays listing items based on filters. For example items only from specific category, location or featured items.", 'citadela-directory' ),
	edit: edit,
	save: () => {
		return null;
	},
	example:{
		attributes: {
			numberOfItems: 4,
		}
	}
} );