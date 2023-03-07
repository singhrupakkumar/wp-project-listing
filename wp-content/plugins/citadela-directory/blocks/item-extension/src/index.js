import edit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, BlockControls, RichText } = wp.blockEditor;
const { Fragment } = wp.element;
const { TextControl, BlockTitle, Icon, ToggleControl } = wp.components;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Item Extension", 'citadela-directory' ),
	description: __( "Displays information from Item Extension inputs assigned to item.", 'citadela-directory' ),
    edit: edit,
	save: () => {
		return null;
	}
} );