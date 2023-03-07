<?php

namespace Citadela\Pro;

class Plugin {

	protected static $name = 'Citadela Pro';

	static function run() {
		
		register_activation_hook( CITADELA_PRO_PLUGIN_FILE, [ __CLASS__, 'plugin_activation' ] );
		register_activation_hook( CITADELA_PRO_PLUGIN_FILE, [ __CLASS__, 'delete_less_compiler_cache' ] );
		register_deactivation_hook( CITADELA_PRO_PLUGIN_FILE, [ __CLASS__, 'delete_less_compiler_cache' ] );

		add_action('after_setup_theme', function () {
			if (\Citadela::$allowed) {
				add_action( 'init', [ __CLASS__, 'init' ] );
				add_action( 'admin_notices', [ __CLASS__, 'plugin_activation_message' ] );
			} else {
				// allow frontend scripts and compile less for frontend even plugin is disabled
				add_action( 'wp_enqueue_scripts', [ __CLASS__, 'frontend_enqueue' ] );
				add_action( 'admin_enqueue_scripts', function () {
					wp_enqueue_style('citadela-admin-style', plugin_dir_url(__DIR__) .  '/assets/css/admin/admin-style.css');
				});
			}
		}, 100);

	}

	
	static function init() {
		if (!defined('WP_CLI')) {
			if (!Compatibility::support_php() or ! Compatibility::support_wp()) {
				return;
			}
			if (!Compatibility::is_citadela_active()) {
				Compatibility::handle_themes_support();
				return;
			}
			if (defined('CITADELA_THEME') && !class_exists('CITADELA_THEME')) {
				return;
			}
		}
		
		load_plugin_textdomain( 'citadela-pro', false, 'citadela-pro/languages' );
		
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_enqueue' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'frontend_enqueue' ] );
		add_action( 'customize_save_after', [ __CLASS__, 'customizer_after_save' ] );

		add_filter( 'body_class', [ __CLASS__, 'body_class' ] );
		add_filter( 'admin_body_class', [ __CLASS__, 'admin_body_class' ] );
		
		add_action( 'citadela_render_icon', function(){
			echo Icon::html();
		});
		
		add_action( 'customize_register', [ Customize::class, 'register' ] );
		add_filter( 'get_custom_logo', [ __CLASS__, 'get_custom_logo' ] );

		add_filter( 'image_size_names_choose', [ __CLASS__, 'custom_image_sizes_names' ] );

		Features::register();
		WooCommerce::run();
		Settings_Page::init();

		self::custom_image_sizes();
		self::update_functions();
		self::deactivate_blocks_plugin();
	}



	static function admin_enqueue() {
		// admin css styles
		Asset::enqueue( 'citadela-pro-admin-styles', '/css/admin/admin-style.css' );

		// classnames
		Asset::register( 'citadela-pro-vendor-classnames', '/vendor/classnames.js' );

		// fontawesome picker
		Asset::enqueue( 'citadela-pro-vendor-fontawesome-iconpicker', '/vendor/fontawesome-iconpicker/fontawesome-iconpicker.css' );
		Asset::register( 'citadela-pro-vendor-fontawesome-iconpicker', '/vendor/fontawesome-iconpicker/fontawesome-iconpicker.js' );

		// flatpickr
		Asset::register( 'citadela-pro-vendor-flatpickr', '/vendor/flatpickr/flatpickr.min.css' );
		Asset::register( 'citadela-pro-vendor-flatpickr', '/vendor/flatpickr/flatpickr.min.js' );

		$flatpickr_locale = substr( get_user_locale(), 0, 2 );
		$flatpickr_locale_src = ( get_user_locale() !== 'en_US' and file_exists( Asset::path( "/vendor/flatpickr/l10n/{$flatpickr_locale}.js" ) ) )
			? "/vendor/flatpickr/l10n/{$flatpickr_locale}.js"
			: "/vendor/flatpickr/l10n/default.js";
		Asset::register( "citadela-pro-vendor-flatpickr-{$flatpickr_locale}" , $flatpickr_locale_src, [ "citadela-pro-vendor-flatpickr" ] );

		// settings fields
		Asset::register( 'citadela-pro-settings-fields', '/css/settings-fields.css', [ 'citadela-pro-vendor-flatpickr' ] );
		Asset::register( 'citadela-pro-settings-fields', '/js/settings-fields.js', [ "citadela-pro-vendor-flatpickr-{$flatpickr_locale}" ] );
	}



	static function frontend_enqueue() {
		Less_Compiler::compile();

		if( ( $url = Google_Fonts::url() ) ) {
			wp_enqueue_style( 'citadela-pro-google-fonts', $url );
		}

		Asset::enqueue( 'citadela-pro', '/js/script.js', [ 'jquery' ] );
	}



	static function delete_less_compiler_cache() {
		if( Compatibility::is_citadela_active() ) {
			Less_Compiler::delete_cache();
		}
	}


	static function customizer_after_save() {
		Less_Compiler::compile(true);
	}

	static function body_class( $classes ) {
		$classes[] = 'pro-plugin-active';
		
		$sticky_header_desktop = get_theme_mod( 'citadela_setting_stickyHeaderDesktop', 'sticky-header-desktop-none' );
		if( $sticky_header_desktop == 'sticky-header-desktop-full' ){
			$classes[] = 'sticky-header-enabled';
		}
		$classes[] = $sticky_header_desktop;

		$sticky_header_mobile = get_theme_mod( 'citadela_setting_stickyHeaderMobile', 'sticky-header-mobile-burger' );
		if( $sticky_header_mobile == 'sticky-header-mobile-full' ){
			$classes[] = ! isset( $classes['sticky-header-enabled'] ) ? 'sticky-header-enabled' : '';
		}
		$classes[] = $sticky_header_mobile;
		
		if( get_theme_mod( 'citadela_setting_collapseMenuToButton', false ) ){
			$classes[] = 'responsive-menu-on-desktop';
			$classes[] = 'responsive-menu';
		}

		if( get_theme_mod( 'citadela_setting_footerCollapsibleWidgetsApply', true ) ){
			$classes[] = 'footer-collapsible-widgets-enabled';
		}

		if( get_theme_mod( 'citadela_setting_footerCollapsibleWidgetsOpened' ) ){
			$classes[] = 'footer-widgets-default-opened';
		}

		return $classes;
	}

	static function admin_body_class( $classes ) {
		$classes .= ' pro-plugin-active ';
		return $classes;
	}

	static function get_custom_logo( $html ) {
		return $html;
	}

	static function custom_image_sizes(){
		add_image_size( 'citadela_service', 480, 360, true );
	}

	static function custom_image_sizes_names( $sizes ){
		$sizes['citadela_service'] = __('Citadela Service Block image size', 'citadela-pro');
		return $sizes;
	}

	static function plugin_activation() {
		set_transient( 'citadela-pro-activation-transient', true, 5 );
	}
	
	static function plugin_activation_message() {
		if( get_transient( 'citadela-pro-activation-transient' ) ){
			printf(
                '<div class="notice notice-success notice-large"><p><strong class="notice-title">%1$s</strong><br>%2$s</p></div>',
                esc_html__( 'Thank you for activating Citadela Pro.', 'citadela-pro' ),
                esc_html__( 'You can import any of the available Citadela Layouts.', 'citadela-pro' ) . '&nbsp;' . sprintf( 
                	'<a href="%1$s">%2$s</a>', 
                	esc_url( admin_url( "admin.php?page=citadela-pro-settings&tab=layouts" ) ), 
                	esc_html__( 'View layouts', 'citadela-pro' ) )
            );
			delete_transient( 'citadela-pro-activation-transient' );
		}
	}

	static function update_functions(){
		if( ! get_option('citadela_update_header_fullwidth_option') ){
			$theme_layout = get_theme_mod( 'citadela_setting_themeLayout', 'classic' );
			$header_layout = get_theme_mod( 'citadela_setting_headerLayout', 'classic' );
			if( $theme_layout == 'modern' && $header_layout == 'classic' ){
				set_theme_mod( 'citadela_setting_headerFullwidth', true );
			}else{
				set_theme_mod( 'citadela_setting_headerFullwidth', false );	
			}
			update_option('citadela_update_header_fullwidth_option', true );
		}

		if( ! get_option('citadela_update_fullsizewidth_option') ){
			$mod = get_theme_mod( 'citadela_setting_fullSizeWidth', '' );
			if( intval( $mod ) === 1920 ){
				set_theme_mod( 'citadela_setting_fullSizeWidth', '' );
			}
			update_option('citadela_update_fullsizewidth_option', true );
		}

		if( ! get_option('citadela_update_comment_disable_links_option') ){
			$option = get_option( 'citadela_pro_comments_extension' );
			if( is_array( $option ) && isset( $option['comment_disable_links'] ) && $option['comment_disable_links'] ===  true ){
				unset($option['comment_disable_links']);
				$option['comment_disable_links_from_guest'] = true;
				$option['comment_disable_links_from_editor'] = true;
				$option['comment_disable_links_from_noneditor'] = true;
				update_option('citadela_pro_comments_extension', $option );
			}
			update_option('citadela_update_comment_disable_links_option', true );
		}

	}

	static function deactivate_blocks_plugin(){
		if( defined( 'CITADELA_BLOCKS_PLUGIN' ) ){
			if ( ! function_exists( 'deactivate_plugins' ) ) {
		        require_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		    }
			deactivate_plugins( 'citadela-blocks/citadela-blocks.php' );
			add_action( 'admin_notices', function() {
				unset( $_GET['activate'] ); // to get rid off WP's notification msg that plugin was activated, which is not true in case the Blocks plugin is trying to be activated

				printf(
					'<div class="notice notice-warning notice-large"><p><strong class="notice-title">%1$s</strong><br>%2$s</p></div>',
					// translators: %s plugin's name
					sprintf( esc_html__( '%s plugin has beed deactivated', 'citadela-pro' ), 'Citadela Blocks' ),
					// translators: %s plugin's name
					sprintf(
						wp_kses_post( __( "All blocks were moved to %s plugin.", 'citadela-pro' ) ),
						self::$name
					)
				);
			} );
		}
	}
}
