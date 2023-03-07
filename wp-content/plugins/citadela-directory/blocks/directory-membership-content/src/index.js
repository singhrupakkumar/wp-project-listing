import edit from './edit';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks } = wp.blockEditor;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Listing Membership Content", 'citadela-directory' ),
	description: __( "Displays content only for users with active membership, or only for visitors without membership.", 'citadela-directory' ),
	edit: edit,
	save: () => {
		return (
			<InnerBlocks.Content/>
        );
	}
} );