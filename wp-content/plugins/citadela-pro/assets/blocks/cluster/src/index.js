import save from './save';
import edit from './edit';
import deprecated from './deprecated';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Cluster', 'citadela-pro' ),
	description: __( 'Special container that is always on full width if there is no sidebar or columns. You can insert there other blocks and set container background.', 'citadela-pro' ),
	deprecated,
	edit,
	save, 
	example: {
		attributes: {
			backgroundColor: "rgba(31, 135, 180, 1)",
			focalPoint: { x: 0.50, y: 0.00 },
			sectionsSize: "content",
			backgroundType: "image",
			backgroundImageSize: "full-horizontal",
			backgroundImage: { url: _citadela_cluster_block_vars.blockUrl + '/src/example-bg.png' },
			backgroundGradient: { first: "rgba(131, 107, 219, 1)", second: "rgba(145, 104, 169, 1)", degree: 135, type: "linear" },
			backgroundImageColorType: "gradient",
			insideSpace: "large",
		},
		innerBlocks: [
			{
				name: 'citadela-blocks/spacer',
				attributes: {
					height: 8,
					unit: "%",
				},
			},
			{
				name: 'core/heading',
				attributes: {
					content: __( "Cluster Block", 'citadela-pro'),
					textAlign: "center",
					style: { typography: { fontSize: 50 } },
					textColor: "white",
				},
			},
			{
				name: 'core/paragraph',
				attributes: {
					content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. ',
					align: "center",
					textColor: "white",
				},
			},
			{
				name: 'core/buttons',
				attributes: {
					contentJustification: "center",
				},
				innerBlocks: [
					{
						name: 'core/button',
						attributes: {
							text: __( "WP Button Block", 'citadela-pro' ),
						},
					},
				],
			},
			{
				name: 'citadela-blocks/spacer',
				attributes: {
					height: 10,
					unit: "%",
				},
			},
		],
	}
});
