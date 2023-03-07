<?php

namespace Citadela\Pro;

class Icon {

	static function data_url() {
		return 'data:image/svg+xml;base64,' . base64_encode( self::svg() );
	}



	static function svg() {
		return file_get_contents( Asset::path( '/images/citadela-logo-pro.svg' ) );
	}



	static function html( $size = 32 ) {
		return sprintf(
			'<i></i>',
			$size,
			self::data_url()
		);
	}
}
