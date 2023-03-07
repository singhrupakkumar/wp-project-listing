import edit from './edit';
import save from './save';
import deprecated from './deprecated';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Service', 'citadela-pro' ),
	description: __( 'Allows you to easily add company services to your website.', 'citadela-pro' ),
	deprecated,
	edit,
	save,
	example: {
		attributes: {
			serviceTitle: "Lorem ipsum",
			serviceDescription: "Natoque penatibus et magnis",
			serviceDesignIconClass: "fas fa-heart",
			serviceDesignIconColor: "#3178d8",
		}
	}
} );