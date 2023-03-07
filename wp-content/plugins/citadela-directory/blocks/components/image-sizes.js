/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { SelectControl } = wp.components;



export default class ImageSizes extends Component {

	render() {

        const { value, onChange, label = __('Image size', 'citadela-directory' ), help = '', customSizes = {} } = this.props;
		
        const { getSettings } = wp.data.select( 'core/block-editor' );
        const { imageSizes } = getSettings();
        const options = [];
        
        if( customSizes ){
            for (let key in customSizes) {
                options.push( { label: customSizes[key], value: key });           
            }
        }

        for (let key in imageSizes) {
            options.push( { label: imageSizes[key]['name'], value: imageSizes[key]['slug'] });           
        }
        
		return(
			<SelectControl
                label={ label }
                help={ help }
                value={ value }
                options={ options }
                onChange={ ( value ) => onChange( value ) }
            />
	    );
	}
}
