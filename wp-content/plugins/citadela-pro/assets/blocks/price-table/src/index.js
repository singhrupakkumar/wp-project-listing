import edit from './edit';
import save from './save';
import deprecated from './deprecated';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Price Table', 'citadela-pro' ),
	description: __( 'Display product, service or packages offers in the table. Set the best deal, show discounted price and write important features for comparison.', 'citadela-pro' ),
	edit,
	save,
	deprecated,
	example: {
		attributes: {
			rows:[ 
				{ text: "Suspendisse facilisis purus" },
				{ text: "Sed fringilla libero augue" },
				{ text: "Praesent id mi et diam mollis" },
			],
			title: __( "Membership", 'citadela-pro'), 
			subtitle: "Nunc mattis consectetur nisl",
			price: "$70/mo",
			colorHeaderBg: "#3178d8",
			showButton: true,
			buttonText: "Lorem ipsum"
		}
	}
} );