import edit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Listing Author Detail", 'citadela-directory' ),
	description: __( "Displays information about author on author archive page.", 'citadela-directory' ),
	edit: edit,
	save: () => {
		return null;
	},
	example:{}
} );