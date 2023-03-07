<?php

namespace Citadela\Pro;

class Asset {

	static function path( $relative_path = '' ) {
		return \ctdl\pro\path( '/assets/' . trim( $relative_path, '/\\' ) );
	}



	static function url( $relative_url = '' ) {
		return \ctdl\pro\url( '/assets/' . trim( $relative_url, '/\\' ) );
	}



	static function register( $handle, $relative_url, $deps = [] ) {
		$ver = false;
		if ( file_exists( $asset_abs_path = self::path( $relative_url ) ) ) {
			$ver = filemtime( $asset_abs_path );
		}
		if ( substr( $relative_url, -3 ) === '.js' ) {
			wp_register_script( $handle,  self::url( $relative_url ), $deps, $ver, true );
		} elseif ( substr( $relative_url, -4 ) === '.css' ) {
			wp_register_style( $handle,  self::url( $relative_url ), $deps, $ver );
		}
	}



	static function enqueue( $handle, $relative_url = '', $deps = [] ) {
		if ( $relative_url ) {
			self::register( $handle, $relative_url, $deps );
		}
		if ( wp_script_is( $handle, 'registered' ) ) {
			wp_enqueue_script( $handle );
		}
		if ( wp_style_is( $handle, 'registered' ) ) {
			wp_enqueue_style( $handle );
		}
	}



	static function localize( $handle, $object_name, $data ) {
		wp_localize_script( $handle, $object_name, $data );
	}
}
