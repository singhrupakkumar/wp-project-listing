import edit from './edit';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Listing Search Results', 'citadela-directory' ),
	description: __( 'Displays listing items based on current category/location page or results from search form.', 'citadela-directory' ),
	edit: edit,
	save: () => {
		return null;
	}
} );