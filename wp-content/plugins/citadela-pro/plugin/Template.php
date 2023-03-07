<?php

namespace Citadela\Pro;

class Template {

	static function path( $relative_path = '' ) {
		return \ctdl\pro\path( '/templates/' . trim( $relative_path, '/\\' ) );
	}



	static function load( $relative_path, $__data = [] ) {
		$__data = apply_filters( 'citadela\pro\template\data', $__data, $relative_path );
		extract( $__data, EXTR_SKIP );
		unset( $__data );
		require \ctdl\pro\path( '/templates/' . trim( $relative_path, '/\\' ) . '.php' );
	}



	static function insert_data( $relative_path, $data ) {
		add_filter( 'citadela\pro\template\data', function( $__data, $__relative_path) use( $relative_path, $data ) {
			if ( trim( $__relative_path, '/\\' ) === trim( $relative_path, '/\\' ) ) {
				return array_merge( $__data, $data );
			}
			return $__data;
		}, 10, 2 );
	}
}
