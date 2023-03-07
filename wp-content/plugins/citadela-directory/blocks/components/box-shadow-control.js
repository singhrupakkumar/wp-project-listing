import CitadelaRangeControl  from './range-control';
import CustomColorControl from './custom-color-control';
/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;

export default class BoxShadowControl extends Component {

	constructor(){
		super( ...arguments );
		this.updateShadowValues = this.updateShadowValues.bind( this );

		this.defaults = {
			horizontal: 10, 
			vertical: 10, 
			blur: 20, 
			spread: 0
		};

	}

	updateShadowValues( property, value ) {
		const { color, horizontal, vertical, blur, spread } = this.props.value;
		let boxShadow = {
			color: color == undefined ? this.defaults.color : color,
			horizontal: horizontal == undefined ? this.defaults.horizontal : horizontal,
			vertical: vertical == undefined ? this.defaults.vertical : vertical,
			blur: blur == undefined ? this.defaults.blur : blur,
			spread: spread == undefined ? this.defaults.spread : spread,
		};

		boxShadow[property] = value;
		this.props.onChange( boxShadow );
	
	}

	render() {
		const { allowColorReset = true } = this.props;
		const { color, horizontal = this.defaults.horizontal, vertical = this.defaults.vertical, blur = this.defaults.blur, spread = this.defaults.spread } = this.props.value;

		let pickerColor = color;
		if( typeof color === 'object' ){
			pickerColor = `rgba(${color.r}, ${color.g}, ${color.b}, ${color.a})`;
		}

		return(
			<div class="citadela-box-shadow-control">
				
				<CustomColorControl 
					label={ __('Shadow color', 'citadela-directory') }
					color={ pickerColor }
					returnObject
					allowReset={ allowColorReset }
					onChange={ (value) => { this.updateShadowValues( "color", value ) } }
				/>
		
				<CitadelaRangeControl
					label={__('Horizontal offset', 'citadela-directory') + `: ${horizontal}px`}
					rangeValue={ horizontal == undefined ? this.defaults.horizontal : horizontal }
					onChange={ ( value ) => { this.updateShadowValues( "horizontal", value ) } }
					min={ -50 }
					max={ 50 }
					initial={ this.defaults.horizontal }
					allowReset
				/>

				<CitadelaRangeControl
					label={__('Vertical offset', 'citadela-directory') + `: ${vertical}px`}
					rangeValue={ vertical == undefined ? this.defaults.vertical : vertical }
					onChange={ ( value ) => { this.updateShadowValues( "vertical", value ) } }
					min={ -50 }
					max={ 50 }
					initial={ this.defaults.vertical }
					allowReset
				/>

				<CitadelaRangeControl
					label={__('Blur radius', 'citadela-directory') + `: ${blur}px`}
					rangeValue={ blur == undefined ? this.defaults.blur : blur }
					onChange={ ( value ) => { this.updateShadowValues( "blur", value ) } }
					min={ 0 }
					max={ 50 }
					initial={ this.defaults.blur }
					allowReset
				/>

				<CitadelaRangeControl
					label={__('Spread radius', 'citadela-directory') + `: ${spread}px`}
					rangeValue={ spread == undefined ? this.defaults.spread : spread }
					onChange={ ( value ) => { this.updateShadowValues( "spread", value ) } }
					min={ 0 }
					max={ 50 }
					initial={ this.defaults.spread }
					allowReset
				/>
		
				
			</div>
	    );
	}
}