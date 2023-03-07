<?php

namespace Citadela\Pro\Comments_Extension;

use Citadela\Pro\Template;
use Citadela\Pro\Settings_Api;

class Settings {

	use Settings_Api;


	function __construct() {
		$this->register( [
			'show'          => false,
			'name_label'    => '',
			'name_help'     => __( 'We keep your name so we can communicate with you about our product and services. For further details please see <a href="#">our privacy policy</a>.', 'citadela-pro' ),
			'email_label'   => '',
			'email_help'    => __( 'We keep your email so we can communicate with you about our product and services. For further details please see <a href="#">our privacy policy</a>.', 'citadela-pro' ),
			'website_label' => '',
			'website_help'  => __( 'We keep your website URL so we can communicate with you about our product and services. For further details please see <a href="#">our privacy policy</a>.', 'citadela-pro' ),
			'comment_label' => '',
			'comment_help'  => __( 'Your comments help us to improve our products and services so we keep recording them. We use the comment also for future reference. For further details please see <a href="#">our privacy policy</a>.', 'citadela-pro' ),
			'website_disable_links' => false,
			'comment_disable_links_from_guest' => false,
			'comment_disable_links_from_editor' => false,
			'comment_disable_links_from_noneditor' => false,
		] );
		$this->maybe_upgrade();
	}



	function slug() {
		return 'comments-extension';
	}



	function tab() {
		return [
			'label' => __( 'Comments extension', 'citadela-pro' ),
		];
	}



	function display() {
		Template::load( '/comments-extension/settings', [
			'settings' => $this
		]);
	}



	function add_fields() {
		$this->add_section( 'default', [ 'description' => __( 'Comments Extension can be used to add help text to every single input in WordPress Comment form.', 'citadela-pro' ) ] );
		$this->add_field( 'show', [
			'title' => __( 'Enable', 'citadela-pro' ),
			'args' => [
				'type'  => 'checkbox',
				'label' => __( 'Turn on Comments extension functionality', 'citadela-pro' ),
			],
		] );

		$this->add_section( 'name', [ 'title' => __( 'Name field', 'citadela-pro' ) ] );

		$this->add_field( 'name_label', [
			'title' => __( 'Field label', 'citadela-pro' ),
			'section' => 'name',
			'args' => [
				'type' => 'text',
				'description' => __( 'Replace default label of the field.', 'citadela-pro' ),
				'attrs' => [
					'class' => 'regular-text',
				],
			]
		] );
		$this->add_field( 'name_help', [
			'title' => __( 'Additional text', 'citadela-pro' ),
			'section' => 'name',
			'args' => [
				'type' => 'textarea',
				'description' => __( 'Additional text displayed with the field. It can contain HTML formatting such as urls to link to your terms or privacy policy.', 'citadela-pro' ),
				'attrs' => [
					'class' => 'widefat',
					'rows' => 3,
				],
			]
		] );

		$this->add_section( 'email', [ 'title' => __( 'Email field', 'citadela-pro' ) ] );

		$this->add_field( 'email_label', [
			'title' => __( 'Field label' , 'citadela-pro' ),
			'section' => 'email',
			'args' => [
				'type' => 'text',
				'description' => __( 'Replace default label of the field.', 'citadela-pro' ),
				'attrs' => [
					'class' => 'regular-text',
				],
			]
		] );
		$this->add_field( 'email_help', [
			'title' => __( 'Additional text', 'citadela-pro' ),
			'section' => 'email',
			'args' => [
				'type' => 'textarea',
				'description' => __( 'Additional text displayed with the field. It can contain HTML formatting such as urls to link to your terms or privacy policy.', 'citadela-pro' ),
				'attrs' => [
					'class' => 'widefat',
					'rows' => 3,
				],
			]
		] );

		$this->add_section( 'website', [ 'title' => __( 'Website URL field', 'citadela-pro' ) ] );

		$this->add_field( 'website_label', [
			'title' => __( 'Field label' , 'citadela-pro' ),
			'section' => 'website',
			'args' => [
				'type' => 'text',
				'description' => __( 'Replace default label of the field.', 'citadela-pro' ),
				'attrs' => [
					'class' => 'regular-text',
				],
			]
		] );
		$this->add_field( 'website_help', [
			'title' => __( 'Additional text', 'citadela-pro' ),
			'section' => 'website',
			'args' => [
				'type' => 'textarea',
				'description' => __( 'Additional text displayed with the field. It can contain HTML formatting such as urls to link to your terms or privacy policy.', 'citadela-pro' ),
				'attrs' => [
					'class' => 'widefat',
					'rows' => 3,
				],
			]
		] );
		
		$this->add_section( 'comment', [ 'title' => __( 'Comment text field', 'citadela-pro' ) ] );
		$this->add_field( 'comment_label', [
			'title' => __( 'Field label', 'citadela-pro' ),
			'section' => 'comment',
			'args' => [
				'type' => 'text',
				'description' => __( 'Replace default label of the field.', 'citadela-pro' ),
				'attrs' => [
					'class' => 'regular-text',
				],
			]
		] );
		$this->add_field( 'comment_help', [
			'title' => __( 'Additional text', 'citadela-pro' ),
			'section' => 'comment',
			'args' => [
				'type' => 'textarea',
				'description' => __( 'Additional text displayed with the field. It can contain HTML formatting such as urls to link to your terms or privacy policy.', 'citadela-pro' ),
				'attrs' => [
					'class' => 'widefat',
					'rows' => 3,
				],
			]
		] );

		$this->add_section( 'comments_links', [ 'title' => __( 'Comments links settings', 'citadela-pro' ) ] );
		$this->add_field( 'website_disable_links', [
			'title' => __( 'Disable author name link', 'citadela-pro' ),
			'section' => 'comments_links',
			'args' => [
				'type' => 'checkbox',
				'label' => __( 'Comment author name will not be linked to url inserted in Website field', 'citadela-pro' ),
				'attrs' => [
					'class' => 'regular-text',
				],
			]
		] );

		$this->add_field( 'comment_disable_links_from_guest', [
			'title' => __( 'Disable guest comment links', 'citadela-pro' ),
			'section' => 'comments_links',
			'args' => [
				'type' => 'checkbox',
				'label' => __( 'Urls from guest comment text will be removed', 'citadela-pro' ),
			]
		] );

		$this->add_field( 'comment_disable_links_from_editor', [
			'title' => __( 'Disable editor comment links', 'citadela-pro' ),
			'section' => 'comments_links',
			'args' => [
				'type' => 'checkbox',
				'label' => __( 'Urls from editor comment text will be removed. Applied for user roles with edit post capability like Contributor and higher.', 'citadela-pro' ),
			]
		] );

		$this->add_field( 'comment_disable_links_from_noneditor', [
			'title' => __( 'Disable non-editor comment links', 'citadela-pro' ),
			'section' => 'comments_links',
			'args' => [
				'type' => 'checkbox',
				'label' => __( 'Urls from non-editor comment text will be removed. Applied for user roles without edit post capability like Subscriber.', 'citadela-pro' ),
			]
		] );

	}



	function sanitize( $values ) {
		$values['show']          			= ( isset( $values['show'] ) && $values['show'] !== false ) ? true : false;
		$values['name_label']   			= wp_strip_all_tags( $values['name_label'], true );
		$values['name_help']     			= wp_kses_post( $values['name_help'] );
		$values['email_label']   			= wp_strip_all_tags( $values['email_label'], true );
		$values['email_help']    			= wp_kses_post( $values['email_help'] );
		$values['website_label'] 			= wp_strip_all_tags( $values['website_label'], true );
		$values['website_help']  			= wp_kses_post( $values['website_help'] );
		$values['website_disable_links'] 	= ( isset( $values['website_disable_links'] ) && $values['website_disable_links'] !== false ) ? true : false;
		$values['comment_label'] 			= wp_strip_all_tags( $values['comment_label'], true );
		$values['comment_help']  			= wp_kses_post( $values['comment_help'] );
		$values['comment_disable_links_from_guest'] 	= ( isset( $values['comment_disable_links_from_guest'] ) && $values['comment_disable_links_from_guest'] !== false ) ? true : false;
		$values['comment_disable_links_from_editor'] 	= ( isset( $values['comment_disable_links_from_editor'] ) && $values['comment_disable_links_from_editor'] !== false ) ? true : false;
		$values['comment_disable_links_from_noneditor'] 	= ( isset( $values['comment_disable_links_from_noneditor'] ) && $values['comment_disable_links_from_noneditor'] !== false ) ? true : false;
		return $values;
	}



	protected function maybe_upgrade() {
		$options = get_option( $this->settings_slug(), [] );
		if( ! empty( $options['ce_name_help' ] ) ) {
			remove_filter( "sanitize_option_{$this->settings_slug()}", [ $this, 'sanitize' ] );
			update_option( $this->settings_slug(), [
				'show'          						=> $options['ce_show'],
				'name_label'    						=> $options['ce_name_label'],
				'name_help'     						=> $options['ce_name_help'],
				'email_label'   						=> $options['ce_email_label'],
				'email_help'    						=> $options['ce_email_help'],
				'website_label' 						=> $options['ce_website_label'],
				'website_help'  						=> $options['ce_website_help'],
				'website_disable_links' 				=> $options['ce_website_disable_links'],
				'comment_label' 						=> $options['ce_comment_label'],
				'comment_help'  						=> $options['ce_comment_help'],
				'comment_disable_links_from_guest' 		=> $options['comment_disable_links_from_guest'],
				'comment_disable_links_from_editor' 	=> $options['comment_disable_links_from_editor'],
				'comment_disable_links_from_noneditor' 	=> $options['comment_disable_links_from_noneditor'],
			]);
		}
	}
}
