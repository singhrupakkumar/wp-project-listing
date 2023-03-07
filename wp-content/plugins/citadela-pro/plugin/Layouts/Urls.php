<?php

namespace Citadela\Pro\Layouts;

class Urls {

	protected $map;



	function __construct( $map ) {
		$this->map = $map;
	}



	function remap( &$array ) {
		foreach( $array as $key => &$value ) {
			if ( is_serialized( $value ) ) {
				$value = maybe_serialize( $this->deep_remap( maybe_unserialize( $value ) ) );
			} else { // plain string or JSON string
				$value = str_replace( array_keys( $this->map ), array_values( $this->map ), $value );
			}
		}
	}



	function deep_remap( $unserialized ) {
		if( is_array( $unserialized ) ) {
			array_walk_recursive( $unserialized, function( &$item, $key ) {
				if ( is_string( $item) ) {
					$item = str_replace( array_keys( $this->map ), array_values( $this->map ), $item );
				}
			} );
		}
		return $unserialized;
	}
}
