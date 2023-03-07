import edit from './edit';
import save from './save';
import deprecated from './deprecated';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Responsive Spacer', 'citadela-pro' ),
	description: __( 'Add empty space between blocks that will look differently on desktop and on mobile devices.', 'citadela-pro' ),
	deprecated,
	edit,
	save,
	example: {
		attributes: {
			height: 40,
		}
	}
} );