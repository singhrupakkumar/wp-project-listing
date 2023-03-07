<?php

namespace Citadela\Pro\Integrations;

use Citadela\Pro\Asset;
use Citadela\Pro\Template;
use Citadela\Pro\Settings_Api;

class Settings {

	use Settings_Api;


	function __construct() {
		$this->register( [
			'ga_tracking_id' => '',
			'ga_anonymize_ip' => false,
			'custom_header_js' => '',
			'custom_footer_js' => '',
			'show_reusable_blocks_menu' => true,
			'posts_simple_text_styles' => false,
			'posts_disable_featured_image_link' => false,
			'disable_layout_import_export' => false,
		] );
	}



	function slug() {
		return 'integrations';
	}



	function tab() {
		return [
			'label' => __(  'General settings', 'citadela-pro'  ),
			'default' => true,
		];
	}



	function admin_enqueue() {
		wp_enqueue_code_editor( [ 'type' => 'text/html' ] );
		Asset::enqueue( 'citadela-pro-settings-fields' );
	}



	function display() {
		Template::load( '/integrations/settings', [
			'settings' => $this
		]);
	}



	function add_fields() {
		$this->add_section('ga', [ 'title' => __( 'Google Analytics', 'citadela-pro' ),  'description' => __( 'Insert your Google Analytics Tracking ID. Google Analytics tracking code will be added to your website.', 'citadela-pro' ) ] );
		$this->add_field( 'ga_tracking_id', [
			'title' => __( 'Tracking ID', 'citadela-pro' ),
			'section' => 'ga',
			'args' => [
				'description' => __( 'Google Analytics tracking ID.', 'citadela-pro' ),
				'type' => 'text',
				'attrs' => [
					'class' => 'regular-text',
					'placeholder' => 'UA-XXXXXXXX-X'
				],
			]
		] );
		$this->add_field( 'ga_anonymize_ip', [
			'title' => __( 'Tracking privacy', 'citadela-pro' ),
			'section' => 'ga',
			'args' => [
				'label' => __( 'Anonymize IP address of your website visitors.', 'citadela-pro' ),
				'type' => 'checkbox',
			]
		] );


		$this->add_section('custom_js', [  'title' => __( 'Custom codes', 'citadela-pro' ), 'description' => __( 'Insert custom  code into header or footer of your website. Use proper code including <script> or <style> html tags.' ) ] );
		$this->add_field( 'custom_header_js', [
			'title' => __( 'Header code', 'citadela-pro' ),
			'section' => 'custom_js',
			'args' => [
				'type' => 'code-editor',
				'mode' => 'htmlmixed',
				'attrs' => [
					'rows' => 10,
					'class' => 'widefat',
				],
				'language' => 'javascript',
			]
		] );
		$this->add_field( 'custom_footer_js', [
			'title' => __( 'Footer code', 'citadela-pro' ),
			'section' => 'custom_js',
			'args' => [
				'type' => 'code-editor',
				'mode' => 'htmlmixed',
				'attrs' => [
					'rows' => 10,
					'class' => 'widefat',
				],
				'language' => 'javascript',
			]
		] );

		$this->add_section('posts_settings', [ 'title' => __( 'Posts settings', 'citadela-pro' ),  'description' => '' ] );
		$this->add_field( 'posts_simple_text_styles', [
			'title' => __( 'Simple text styles', 'citadela-pro' ),
			'section' => 'posts_settings',
			'args' => [
				'label' => __( 'Simplified typography styles for blog posts pages', 'citadela-pro' ),
				'type' => 'checkbox',
			]
		] );
		$this->add_field( 'posts_disable_featured_image_link', [
			'title' => __( 'Featured image link', 'citadela-pro' ),
			'section' => 'posts_settings',
			'args' => [
				'label' => __( 'Disable link to featured image in blog post', 'citadela-pro' ),
				'type' => 'checkbox',
			]
		] );

		$this->add_section('reusable_blocks', [ 'title' => __( 'Reusable blocks settings', 'citadela-pro' ),  'description' => '' ] );
		$this->add_field( 'show_reusable_blocks_menu', [
			'title' => __( 'Show in menu', 'citadela-pro' ),
			'section' => 'reusable_blocks',
			'args' => [
				'label' => __( 'Show link to manage Reusable blocks in admin menu', 'citadela-pro' ),
				'type' => 'checkbox',
			]
		] );

		$this->add_section('import_export_layout_settings', [ 'title' => __( 'Layout import/export settings', 'citadela-pro' ),  'description' => '' ] );
		$this->add_field( 'disable_layout_import_export', [
			'title' => __( 'Disable import/export', 'citadela-pro' ),
			'section' => 'import_export_layout_settings',
			'args' => [
				'label' => __( 'Import and export layout settings pages will be disabled', 'citadela-pro' ),
				'type' => 'checkbox',
			]
		] );
	}



	function sanitize( $values ) {
		$values['ga_tracking_id'] = wp_strip_all_tags( $values['ga_tracking_id'], true );
		$values['ga_anonymize_ip'] = ( isset( $values['ga_anonymize_ip'] ) && $values['ga_anonymize_ip'] !== false ) ? true : false;
		$values['show_reusable_blocks_menu'] = ( isset( $values['show_reusable_blocks_menu'] ) && $values['show_reusable_blocks_menu'] !== false ) ? true : false;
		$values['posts_simple_text_styles'] = ( isset( $values['posts_simple_text_styles'] ) && $values['posts_simple_text_styles'] !== false ) ? true : false;
		$values['posts_disable_featured_image_link'] = ( isset( $values['posts_disable_featured_image_link'] ) && $values['posts_disable_featured_image_link'] !== 
			false ) ? true : false;
		$values['disable_layout_import_export'] = ( isset( $values['disable_layout_import_export'] ) && $values['disable_layout_import_export'] !== 
			false ) ? true : false;
		
		return $values;
	}
}
