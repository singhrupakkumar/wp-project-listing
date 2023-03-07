<?php

namespace Citadela\Pro\Infobar;

use Citadela\Pro\Template;
use Citadela\Pro\Settings_Api;

class Settings {

	use Settings_Api;


	function __construct() {
		$this->register( [
			'show'        => false,
			'position'    => 'bottom',
			'text'        => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. <a href="#">Read more here</a>',
			'button_text' => __( 'Accept', 'citadela-pro' ),
			'expiration'  => 365,
		] );

		$this->maybe_upgrade();
	}



	function slug() {
		return 'infobar';
	}



	function tab() {
		return [
			'label' => __( 'Infobar', 'citadela-pro' ),
		];
	}



	function display() {
		Template::load( '/infobar/settings', [
			'settings' => $this
		]);
	}



	function add_fields() {
		$this->add_section( 'default', [
			'description' => __( 'Infobar can be used to display cookies notification on your website. It is displayed to all new visitors that come to your website. They can click on the button to hide it. Infobar is then hidden for number of days set it “Cookie expiration” field.', 'citadela-pro' ),
		] );

		$this->add_field( 'show', [
			'title' => __( 'Enable', 'citadela-pro' ),
			'args' => [
				'type'  => 'checkbox',
				'label' => __( 'Use Infobar on your website', 'citadela-pro' ),
			],
		] );
		$this->add_field( 'position', [
			'title' => __( 'Position on website', 'citadela-pro' ),
			'args' => [
				'type'  => 'select',
				'options' => [
					'bottom' => _x( 'Bottom', 'position', 'citadela-pro'),
					'top'    => _x( 'Top', 'position', 'citadela-pro'),
				],
			],
		] );
		$this->add_field( 'text', [
			'title' => __( 'Text', 'citadela-pro' ),
			'args' => [
				'type'  => 'textarea',
				'description' => __('Text displayed in the Infobar. It can contain HTML formatting such as URLs to link to your terms or privacy policy.', 'citadela-pro'),
				'attrs'  => [
					'rows' => 5,
					'class' => 'widefat',
				],
			],
		] );
		$this->add_field( 'button_text', [
			'title' => __( 'Button text', 'citadela-pro' ),
			'args' => [
				'type'  => 'text',
				'description' => __('Text displayed on the button. If no text is specified, X sign will be used to close Infobar.', 'citadela-pro'),
				'attrs'  => [
					'class' => 'regular-text',
				],
			],
		] );
		$this->add_field( 'expiration', [
			'title' => __( 'Cookies expiration', 'citadela-pro' ),
			'args' => [
				'type'  => 'number',
				'description' => __('Expiration of saved cookies in days.', 'citadela-pro'),
				'attrs'  => [
					'step'  => 1,
					'min'   => 0,
				],
			],
		] );
	}



	function sanitize( $values ) {
		$values['show']        = ( isset( $values['show'] ) && $values['show'] !== false ) ? true : false;
		$values['position']    = ! in_array( $values['position'], array_keys( $this->field_args( 'position', 'options' ) ) ) ? $this->defaults( 'position' ) : $values['position'];
		$values['text']        = wp_kses_post( $values['text'] );
		$values['button_text'] = wp_strip_all_tags( $values['button_text'], true );
		$values['expiration']  = absint( $values['expiration'] );
		return $values;
	}



	protected function maybe_upgrade() {
		$options = get_option( $this->settings_slug(), [] );
		if( ! empty( $options['infobar_position' ] ) ) {
			remove_filter( "sanitize_option_{$this->settings_slug()}", [ $this, 'sanitize' ] );
			update_option( $this->settings_slug(), [
				'show'        => $options['infobar_show'],
				'position'    => $options['infobar_position'],
				'text'        => $options['infobar_text'],
				'button_text' => $options['infobar_button_text'],
				'expiration'  => $options['infobar_expiration'],
			]);
		}
	}
}
