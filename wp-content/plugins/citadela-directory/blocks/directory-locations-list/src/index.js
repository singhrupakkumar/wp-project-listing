import edit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Listing Locations List", 'citadela-directory' ),
	description: __( "Displays listing locations based on specific filter. It can display parent locations or sublocations from specific location.", 'citadela-directory' ),
	edit: edit,
	save: () => {
		return null;
	},
	example:{
		attributes: {
			size: 'small',
		}
	}
} );