import edit from './edit';
import metadata from './block.json';
import deprecated from './deprecated';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Posts", 'citadela-pro' ),
	description: __( "Displays list of posts based on filters. For example posts only from specific category.", 'citadela-pro' ),
    anchor: 'true',
    deprecated, // keep for backward compatibility, block was redesigned to dynamic block and we do not need maintain deprecated blocks anymore
    edit,
	save: () => {
		return null;
	},
} );