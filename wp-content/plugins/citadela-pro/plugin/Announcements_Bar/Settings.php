<?php

namespace Citadela\Pro\Announcements_Bar;

use Citadela\Pro\Asset;
use Citadela\Pro\Template;
use Citadela\Pro\Settings_Api;

class Settings {

	use Settings_Api;


	function __construct() {
		$this->register( [
			'enable'      => false,
			'date_from'   => null,
			'date_to'     => null,
			'type'        => 'simple',
			'html'        => '',
			'css'         => '',
			'text'        => '',
			'button_text' => '',
			'button_url'  => '',
		] );
	}



	function slug() {
		return 'announcements-bar';
	}



	function tab() {
		return [
			'label' => __( 'Announcements bar', 'citadela-pro' ),
		];
	}



	function admin_enqueue() {
		wp_enqueue_code_editor( [ 'type' => 'text/html' ] );
		Asset::enqueue( 'citadela-pro-settings-fields' );
	}



	function display() {
		Template::load( '/announcements-bar/settings', [
			'settings' => $this
		]);
	}



	function add_fields() {
		$this->add_section( 'default', [ 'description' => __( 'Announcements bar allows you to present any important announcements on the top of all your pages.', 'citadela-pro' ) ] );
		$this->add_field( 'enable', [
			'title' => __( 'Enable', 'citadela-pro' ),
			'args' => [
				'type'  => 'checkbox',
				'label' => __( 'Turn on Announcements bar functionality', 'citadela-pro' ),
			],
		] );
		$this->add_field( 'date_from', [
			'title' => __( 'Date from', 'citadela-pro' ),
			'args' => [
				'type'  => 'datetime',
				'description' => __( 'Announcements bar is displayed after selected date and time.', 'citadela-pro' ),
				'attrs' => [
					'value' => ! empty( $this->value( 'date_from' ) ) ? date( 'Y-m-d H:i', $this->value( 'date_from' ) ) : '',
				]
			],
		] );
		$this->add_field( 'date_to', [
			'title' => __( 'Date to', 'citadela-pro' ),
			'args' => [
				'type'  => 'datetime',
				'description' => __( 'Announcements bar is displayed until selected date and time.', 'citadela-pro' ),
				'attrs' => [
					'value' => ! empty( $this->value( 'date_to' ) ) ? date( 'Y-m-d H:i', $this->value( 'date_to' ) ) : '',
				]
			],
		] );
		$this->add_field( 'type', [
			'title' => __( 'Type', 'citadela-pro' ),
			'args' => [
				'type'  => 'radio-list',
				'attrs' => [
					'class' => 'section-switch',
				],
				'list' => [
					'simple'    => _x( 'Simple', 'settings', 'citadela-pro' ),
					'advanced'  => _x( 'Advanced', 'settings', 'citadela-pro' ),
				]
			],
		] );

		$this->add_section( 'simple',  [
			'title' => __( 'Simple settings', 'citadela-pro' ),
			'description' => __( 'Simple settings allow you to show simple text and button in your announcements bar. Colors can be customized via WordPress customizer.', 'citadela-pro' ),
			'callback' => function( $description ) {
				$button_html = sprintf( '<p><a href="%s" class="button button-secondary" target="_blank">%s</a></p>',
					admin_url( 'customize.php?autofocus[section]=citadela_section_announcements_bar'),
					__('Customize colors', 'citadela-pro')
				);
				$this->section( $description, $button_html );
			}
		] );
		$this->add_field( 'text', [
			'title' => __( 'Text of announcement', 'citadela-pro' ),
			'section' => 'simple',
			'args' => [
				'type' => 'textarea',
				'attrs' => [
					'class' => 'widefat',
					'rows' => 2,
				],
			]
		] );
		$this->add_field( 'button_text', [
			'title' => __( 'Button text', 'citadela-pro' ),
			'section' => 'simple',
			'args' => [
				'type' => 'text',
				'description' => __( 'Text displayed on the button in announcements bar. Leave empty to hide the button.', 'citadela-pro' ),
				'attrs' => [
					'class' => 'regular-text',
				],
			]
		] );
		$this->add_field( 'button_url', [
			'title' => __( 'Button URL', 'citadela-pro' ),
			'section' => 'simple',
			'args' => [
				'type' => 'url',
				'description' => __( 'Insert URL for button. Leave empty to hide the button.', 'citadela-pro' ),
				'attrs' => [
					'class' => 'regular-text',
				],
			]
		] );

		$this->add_section( 'advanced', [ 'title' => __( 'Advanced settings', 'citadela-pro' ), 'description' => __( 'With advanced settings you have full control over html and styles for your announcements bar.', 'citadela-pro' ) ] );
		$this->add_field( 'html', [
			'title' => 'HTML',
			'section' => 'advanced',
			'args' => [
				'type' => 'code-editor',
				'mode' => 'htmlmixed',
				'attrs' => [
					'class' => 'widefat',
					'rows' => 10
				],
			]
		] );
		$this->add_field( 'css', [
			'title' => 'CSS',
			'section' => 'advanced',
			'args' => [
				'type' => 'code-editor',
				'mode' => 'css',
				'attrs' => [
					'class' => 'widefat',
					'rows' => 10
				],
			]
		] );
	}



	function sanitize( $values ) {
		$values['enable'] = ( isset( $values['enable'] ) && $values['enable'] !== false ) ? true : false;
		if ( ! empty( $values['date_from'] ) and is_string( $values['date_from' ] ) ) {
			$values['date_from'] = strtotime( trim( $values['date_from'] ) );
		}
		if ( ! empty( $values['date_to'] ) and is_string( $values['date_to' ] ) ) {
			$values['date_to'] = strtotime( trim( $values['date_to'] ) );
		}
		$values['type'] = ! in_array( $values['type'], array_keys( $this->field_args( 'type', 'list' ) ) ) ? $this->defaults( 'type' ) : $values['type'];
		$values['html'] = wp_kses_post( $values['html'] );
		$values['css'] = wp_strip_all_tags( $values['css'] );
		$values['text'] = wp_kses( $values['text'], [ 'a' => [ 'href' => [], 'title' => [], 'target' => [], 'follow' => [] ], 'br' => [], 'em' => [], 'strong' => [], 'i' => [] ] );
		$values['button_text'] = wp_strip_all_tags( $values['button_text'], true );
		$values['button_url'] = esc_url_raw( $values['button_url'] );
		return $values;
	}

}
