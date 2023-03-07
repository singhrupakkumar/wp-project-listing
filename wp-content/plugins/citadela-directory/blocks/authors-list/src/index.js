import edit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Listing Authors List", 'citadela-directory' ),
	description: __( "Displays information about selected authors.", 'citadela-directory' ),
	edit: edit,
	save: () => {
		return null;
	},
	example:{}
} );