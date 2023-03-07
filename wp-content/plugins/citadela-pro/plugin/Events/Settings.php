<?php

namespace Citadela\Pro\Events;

use Citadela\Pro\Template;
use Citadela\Pro\Settings_Api;

class Settings {

	use Settings_Api;


	function __construct() {
		$this->register( [
			'citadela_css'        => true
		] );
	}



	function slug() {
		return 'events';
	}



	function tab() {
		return [
			'label' => __( 'Events', 'citadela-pro' ),
		];
	}



	function display() {
		Template::load( '/infobar/settings', [
			'settings' => $this
		]);
	}



	function add_fields() {
		if (is_plugin_active('the-events-calendar/the-events-calendar.php')) {
			$this->add_section( 'default', [ 'callback' => function () {
				echo wp_kses_post('<div class="section-description"><p>' . 
					/*translators: 1. Start html anchor tag, 2. End html anchor tag  */ 
					sprintf(__( 'This setting defines whether to use our Citadela styles in %1$sThe Events Calendar%2$s plugin or use the plugin\'s styles.', 'citadela-pro' ), '<a href="https://wordpress.org/plugins/the-events-calendar/" target="_blank">', '</a>')
					. '</p></div>'); 
			} ] );
			$this->add_field( 'citadela_css', [
				'title' => __( 'Styles', 'citadela-pro' ),
				'args' => [
					'type'  => 'checkbox',
					'label' => __( 'Use Citadela styles', 'citadela-pro' ),
				],
			] );
		} else {
			$this->add_section( 'default', [ 'callback' => function () {
				echo wp_kses_post('<div class="section-description"><p>' . 
					/*translators: 1. Start html anchor tag, 2. End html anchor tag  */ 
					sprintf(__( 'Easily create and manage an events calendar with %1$sThe Events Calendar%2$s free plugin.', 'citadela-pro' ), '<a href="https://wordpress.org/plugins/the-events-calendar/" target="_blank">', '</a>')
					. '</p></div>'); 
			} ] );
		}
	}



	function sanitize( $values ) {
		$values['citadela_css']        = ( isset( $values['citadela_css'] ) && $values['citadela_css'] !== false ) ? true : false;
		return $values;
	}

}
