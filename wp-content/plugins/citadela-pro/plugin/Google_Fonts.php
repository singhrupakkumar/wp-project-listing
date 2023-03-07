<?php

namespace Citadela\Pro;

class Google_Fonts {

	protected static $fonts;



	static function get()
	{
		if ( is_null( self::$fonts ) ) {
			$json = @file_get_contents( Asset::path( '/fonts/google-fonts.json' ) );
			if ( $json === false ){
				self::$fonts = [];
			} else {
				self::$fonts = json_decode( $json )->items;
			}
		}

		return self::$fonts;
	}



	static function for_selectbox() {
		$select = [];
		foreach( self::get() as $font ) {
			$select[ $font->family ] = $font->family;
		}
		return $select;
	}



	static function url()
	{
		$selected_fonts = [];

		foreach( Customize::config( 'controls.typography' ) as $key => $value ) {
			$selected_fonts[] = get_theme_mod( "citadela_setting_$key", $value[ 'default' ] );
		}

		if ( empty( $selected_fonts ) ) return '';

		$family = $subsets = [];

		foreach( self::get() as $font ) {
			if( in_array( $font->family, $selected_fonts, true ) ) {
				$family[] = $font->family . ':' . implode( ',', $font->variants );
				$subsets = array_merge( $subsets, $font->subsets );
			}
		}

		return add_query_arg( [
			'family' => implode( '|', $family ),
			'subset' => implode( ',', $subsets ),
			'display' => 'swap',
		], 'https://fonts.googleapis.com/css' );
	}
}
