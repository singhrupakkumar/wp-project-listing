import googleFonts from '../../fonts/google-fonts.json';

const { __ } = wp.i18n;
const { Component, useCallback } = wp.element;
const { BaseControl, RangeControl, SelectControl } = wp.components;

export default class GoogleFontsSelect extends Component {

	constructor() {
		super( ...arguments );
		this.getSelectOptions = this.getSelectOptions.bind(this);
		this.changeFont = this.changeFont.bind(this);
	}

	getFontObject( fontFamily ){
		for ( let i = 0; i < googleFonts.items.length; i++ ) {
			
			if ( googleFonts.items[ i ]['family'] === fontFamily ) {
				return googleFonts.items[ i ];
			}
		}
	}

	getSelectOptions() {
		const def = [ { label: " - " + __( "Use default font", "citadela-pro" ) + " - ", value: '' } ];

		const selection = googleFonts.items.map( ( font ) => {
			const label = font['family'];
			const value = label.replace( /\s+/g, '+' );

			return {
				label: label,
				value: value,
			};
		} );

		return def.concat( selection );
	}

	loadGoogleFont( fontFamily ) {
		if( fontFamily == '' ) return;
		if( this.props.state.loadedFonts.includes( fontFamily ) ) return;

		const font = this.getFontObject( fontFamily.replace( /\+/g, ' ' ) );
		const head = document.head;
		const link = document.createElement( 'link' );

		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = 'https://fonts.googleapis.com/css?family=' + fontFamily + ':' + font.variants.join( ',' ) + '&display=swap';

		head.appendChild( link );
		var loadedFonts = this.props.state.loadedFonts;
		loadedFonts.push( fontFamily.replace( /\s+/g, '+' ) );
		this.props.setState( { loadedFonts: loadedFonts } )
	}

	changeFont( fontFamily ) {
		const { onChange } = this.props;

		this.loadGoogleFont( fontFamily );
		
		const family = fontFamily.replace( /\+/g, ' ' );
		const font = family != '' ? this.getFontObject( family ) : '';
		
		const value = {
			family: family,
			variants: font != '' ? font.variants : [],
			subsets:  font != '' ? font.subsets : [],
			category: font != '' ? font.category : "",
		}

		onChange( value );
	}

	render() {
		const { googleFont, label } = this.props;
		const selectedFamily = googleFont['family'].replace( /\s+/g, '+' )
		return(
			<BaseControl
				label={ label }
			>
				<SelectControl
					value={ selectedFamily }
					options={ this.getSelectOptions() }
					onChange={ ( value ) => {
						this.changeFont( value );
					} 
					}
				/>
			</BaseControl>
	    );
	}
}