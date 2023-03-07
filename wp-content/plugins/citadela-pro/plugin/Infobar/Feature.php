<?php

namespace Citadela\Pro\Infobar;

use Citadela\Pro\Asset;
use Citadela\Pro\Template;

class Feature {

	function __construct() {
		if ( ! is_admin() and $this->settings()->value( 'show' ) and ! empty( $this->settings()->value( 'text' ) ) ) {
			add_action( 'wp_footer', [ $this, 'display_on_frontend' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'frontend_enqueue' ] );
		}
	}



	function frontend_enqueue() {
		Asset::enqueue( 'citadela-pro-vendor-cookie-lib', '/vendor/js.cookie.js' );
		Asset::enqueue( 'citadela-pro-infobar', '/js/infobar.js', [ 'jquery', 'citadela-pro-vendor-cookie-lib' ] );
	}



	function settings() {
		static $settings = null;
		if ( ! $settings ) {
			$settings = new Settings;
		}
		return $settings;
	}



	function display_on_frontend() {
		Template::load( '/infobar/infobar', $this->settings()->values() );
	}
}
