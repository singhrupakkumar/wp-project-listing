import edit from './edit';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Listing Advanced Filters", 'citadela-directory' ),
	description: __( "Filters to specify results of displayed items.", 'citadela-directory' ),
    edit: edit,
	save: () => {
		return null;
	}
} );