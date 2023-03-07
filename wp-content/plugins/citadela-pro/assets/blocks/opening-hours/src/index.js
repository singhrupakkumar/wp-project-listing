import edit from './edit';
import save from './save';
import deprecated from './deprecated';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Opening Hours', 'citadela-pro' ),
	description: __( 'Show opening hours on your website.', 'citadela-pro' ),
	deprecated,
	edit,
	save,
	example: {
		attributes: {
			layout: "box",
			mondayTitle: "<strong>"+__( 'Monday', 'citadela-pro')+"</strong>", 
			tuesdayTitle: "<strong>"+__( 'Tueasday', 'citadela-pro')+"</strong>", 
			wednesdayTitle: "<strong>"+__( 'Wednesday', 'citadela-pro')+"</strong>", 
			thursdayTitle: "<strong>"+__( 'Thursday', 'citadela-pro')+"</strong>", 
			fridayTitle:"<strong>"+__( 'Friday', 'citadela-pro')+"</strong>", 
			saturdayTitle: "<strong>"+__( 'Saturday', 'citadela-pro')+"</strong>",
			sundayTitle: "<strong>"+__( 'Sunday', 'citadela-pro')+"</strong>",
			mondayValue: "07:00 – 19:00", tuesdayValue: "07:00 – 19:00", wednesdayValue: "07:00 – 19:00", thursdayValue: "07:00 – 19:00", fridayValue: "07:00 – 19:00", saturdayValue: "09:00 – 14:00", sundayValue: "09:00 – 14:00",
			dayLabelColor: "#3178d8",
		}
	}
} );