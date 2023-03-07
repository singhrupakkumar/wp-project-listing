import edit from './edit';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Responsive Text', 'citadela-pro' ),
	description: __( 'Allows you to insert text with custom settings and font.', 'citadela-pro' ),
	edit,
	save: () => {
		return null;
	},
	example: {
		attributes: {
			text: "Lorem ipsum text",
			htmlTag: "h2",
			fontSize: 40,
			color: "#3178d8",
			googleFont: { 
				family: "Comfortaa", 
				variants: [ "600" ],
				subsets:[ "cyrillic", "cyrillic-ext", "greek", "latin", "latin-ext", "vietnamese" ],
				category: "display"
			}
		}
	}
} );