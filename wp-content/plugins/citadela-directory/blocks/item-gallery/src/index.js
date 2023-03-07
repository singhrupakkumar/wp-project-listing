import edit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Item Gallery", 'citadela-directory' ),
	description: __( "Displays gallery of images assigned to Item Post.", 'citadela-directory' ),
	edit: edit,
	save: () => {
		return null;
	}
} );