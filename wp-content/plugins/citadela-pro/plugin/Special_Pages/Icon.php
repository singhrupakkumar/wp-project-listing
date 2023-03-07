<?php

namespace Citadela\Pro\Special_Pages;

use Citadela\Pro\Asset;

class Icon {

	static function data_url() {
		return 'data:image/svg+xml;base64,' . base64_encode( self::svg() );
	}



	static function svg() {
		return file_get_contents( Asset::path( '/images/citadela-logo-special-pages.svg' ) );
	}



	static function html( $size = 32 ) {
		return sprintf(
			'<i style="
				display: inline-block;
				height: %1$spx;
				width: %1$spx;
				background-image: url(\'%2$s\');
				background-repeat: no-repeat;
				background-position: center;
				background-size: %1$spx auto;
				vertical-align: text-top;
			"></i>',
			$size,
			self::data_url()
		);
	}
}
