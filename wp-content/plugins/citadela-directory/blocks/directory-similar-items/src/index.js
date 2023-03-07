import edit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Listing Similar Items", 'citadela-directory' ),
	description: __( "Displays listing items similar to selected reference item post.", 'citadela-directory' ),
	edit: edit,
	save: () => {
		return null;
	}
} );