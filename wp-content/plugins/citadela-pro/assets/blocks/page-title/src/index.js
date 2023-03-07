import edit from './edit';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Custom Page Title', 'citadela-pro' ),
	description: __( 'Allows you to put page title anywhere you like. For example after header image.', 'citadela-pro' ),
	edit,
	save: () => {
		return null;
	},
	example: {
		attributes: {
			subtitle: "Custom subtitle text",
			titleColor: '#3178d8',
		}
	}

} );