<?php

namespace Citadela\Pro;

class Config {

	static function get( $key = null ) {
		return \ctdl\pro\dot_get( [

			'theme_design' => [
				'default' => _x( 'Default', 'theme design', 'citadela-pro' ),
			],
			'theme_layouts' => [
				'classic' => _x( 'Classic', 'theme layout', 'citadela-pro' ),
				'modern' => _x( 'Modern', 'theme layout', 'citadela-pro' ),
			],
			'header_layouts' => [
				'classic' => _x( 'Classic', 'header layout', 'citadela-pro' ),
				'center' => _x( 'Center', 'header layout', 'citadela-pro' ),
			],

		], $key );
	}
}
