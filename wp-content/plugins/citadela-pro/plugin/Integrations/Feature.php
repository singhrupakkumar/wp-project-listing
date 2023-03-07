<?php

namespace Citadela\Pro\Integrations;

class Feature {

	static $settings;
	
	function __construct() {
		
		if ( $this->settings()->value('show_reusable_blocks_menu') ) {
			add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		}
		
		if ( $this->settings()->value('posts_simple_text_styles') ) {
			add_filter( 'body_class', [ $this, 'body_class' ] );
		}

		if ( $this->settings()->value('posts_disable_featured_image_link') ) {
			add_filter( 'ctdl_use_post_featured_image_link', [ $this, 'use_post_featured_image_link' ] );
		}
		if ( $this->settings()->value('disable_layout_import_export') ) {
			add_action( 'ctdl_disable_layout_import_export_content', [ $this, 'disable_layout_import_export_content' ] );
		}
		
		if ( ! is_admin() ) {
			if ( $this->settings()->value('ga_tracking_id') ) {
				add_action( 'wp_print_scripts', [ $this, 'print_ga_js' ], 2 );
			}
			if ( $this->settings()->value('custom_header_js') ) {
				add_action( 'wp_print_scripts', [ $this, 'print_custom_header_js' ], 99 );
			}
			if ( $this->settings()->value('custom_footer_js') ) {
				add_action( 'wp_print_footer_scripts', [ $this, 'print_custom_footer_js' ], 99 );
			}
		}
		
	}



	function disable_layout_import_export_content( ){
		ob_start();
		?>
		<div class="citadela-screen-section">
			<h2 class="citadela-screen-title"><?php esc_html_e('Layouts import and export is disabled', 'citadela-pro'); ?></h2>
			<p class="citadela-screen-subtitle"><?php 
				// translators: %1s - Opening anchor HTML tag, %2s - Closing anchor HTML tag
				echo sprintf(
					esc_html__('You have disabled functionality to import and export Citadela Layouts. To use this tool, enable it again in the %1sGeneral settings%2s tab.'),
					"<a href=\"" . esc_url( admin_url( "admin.php?page=citadela-pro-settings&tab=integrations#section-import-export-layout-settings" ) )."\">",
					"</a>"
				); ?></p>
		</div>
		<?php
		echo ob_get_clean();
	}



	function body_class( $classes ) {
		if( is_singular('post') ){
			$classes[] = 'simple-text-styles';
		}
		return $classes;
	}



	function admin_menu() {
		add_menu_page( 
			__( 'Reusable Blocks', 'citadela-pro' ),
			__( 'Reusable Blocks', 'citadela-pro' ),
			'manage_options',
			'edit.php?post_type=wp_block',
			'',
			'dashicons-block-default',
			21
		);
	}



	function use_post_featured_image_link( $use ) {
		global $post;
		if( $post && $post->post_type == 'post' ){
			$use = false;
		}
		return $use;
	}


	function print_ga_js() {
		$id = $this->settings()->value('ga_tracking_id');

		$config = $this->settings()->value('ga_anonymize_ip')
			? "gtag('config', '{$id}', { 'anonymize_ip': true });"
			: "gtag('config', '{$id}');";

		echo "
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src='https://www.googletagmanager.com/gtag/js?id={$id}'></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());
		  {$config}
		</script>
		";
	}



	function print_custom_header_js() {
		echo $this->settings()->value('custom_header_js');
	}



	function print_custom_footer_js() {
		echo $this->settings()->value('custom_footer_js');
	}



	function settings() {
		static $settings = null;
		if ( ! $settings ) {
			$settings = new Settings;
		}
		return $settings;
	}
}
