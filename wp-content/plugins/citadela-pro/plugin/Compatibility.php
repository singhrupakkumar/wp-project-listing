<?php

namespace Citadela\Pro;

class Compatibility {

	protected static $name = 'Citadela Pro';



	static function support_php() {
		// https://wordpress.org/about/requirements/
		if ( ! version_compare( PHP_VERSION, '5.6.20', '<' ) ) return true;

		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		deactivate_plugins( CITADELA_PRO_PLUGIN_FILE );

		add_action( 'admin_notices', function() {
			unset( $_GET['activate'] ); // to get rid off WP's notification msg that plugin was activated, which is not true

			printf(
				'<div class="notice notice-warning notice-large"><p><strong class="notice-title">%1$s</strong><br>%2$s</p></div>',
				// translators: %s plugin's name
				sprintf( esc_html__( '%s plugin and PHP incompatiblity', 'citadela-pro' ), self::$name ),
				sprintf(
					// translators: 1. url to upgrading guide, 2. plugin's name
					wp_kses_post( __( "We've noticed that you're running an outdated version of PHP which is no longer supported, therefore <em>the plugin was deactivated</em>. Make sure your site is fast and secure, by <a href='%1\$s'>upgrading PHP to the latest version</a>. Minimal requirement for %2\$s is <strong>PHP 5.6.20</strong>.", 'citadela-pro' ) ),
					esc_url( wp_get_update_php_url() ),
					self::$name
				)
			);
		} );
		return false;
	}



	static function support_wp() {
		global $wp_version;

		if ( ! version_compare( $wp_version, '5.2', '<' ) ) return true;

		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		deactivate_plugins( CITADELA_PRO_PLUGIN_FILE );

		add_action( 'admin_notices', function() {
			unset( $_GET['activate'] ); // to get rid off WP's notification msg that plugin was activated, which is not true

			printf(
				'<div class="notice notice-warning notice-large"><p><strong class="notice-title">%1$s</strong><br>%2$s</p></div>',
				// translators: %s plugin's name
				sprintf( esc_html__( '%s plugin and WordPress incompatiblity', 'citadela-pro' ), self::$name ),
				// translators: 1. url to upgrading guide, 2. plugin's name
				sprintf(
					wp_kses_post( __( "We've noticed that you're running an outdated version of WordPress, therefore <em>the plugin was deactivated</em>. Make sure your site is secure and up to date, by <a href='%1\$s'>updating WordPress to the latest version</a>. Minimal requirement for %2\$s is <strong>WordPress 5.2</strong>.", 'citadela-pro' ) ),
					esc_url( network_admin_url( 'update-core.php' ) ),
					self::$name
				)
			);
		} );

		return false;
	}



	static function handle_themes_support() {
		register_activation_hook( CITADELA_PRO_PLUGIN_FILE, function() {
			$message = sprintf(
				'<h3 class="notice-title">%1$s</h3><p>%2$s</p>',
				// translators: %s plugin's name
				sprintf( esc_html__( '%s plugin incompatibility', 'citadela-pro' ), self::$name ),
				// translators: %s plugin's name
				sprintf( esc_html__( 'The %s plugin could not be activated. It is not compatible with the current active theme.', 'citadela-pro' ), self::$name )
			);
			wp_die( $message, '',  [ 'back_link' => true ] );
		}, 9 );
		add_action( 'after_switch_theme', function() {
			if ( ! function_exists( 'deactivate_plugins' ) )  require_once ABSPATH . 'wp-admin/includes/plugin.php';
			deactivate_plugins( CITADELA_PRO_PLUGIN_FILE );
		} );
		add_action( 'admin_notices', function() {
			printf(
				'<div class="notice notice-info notice-large"><p><strong class="notice-title">%1$s</strong><br>%2$s</p></div>',
				// translators: %s plugin's name
				sprintf( esc_html__( '%s plugin incompatibility', 'citadela-pro' ), self::$name ),
				// translators: %s plugin's name
				sprintf( esc_html__( "Recently activated theme is not compatible with %s plugin, therefore the plugin was deactivated.", 'citadela-pro' ), self::$name )
			);
		}, 12 );
		if ( ! function_exists( 'deactivate_plugins' ) )  require_once ABSPATH . 'wp-admin/includes/plugin.php';
		deactivate_plugins( CITADELA_PRO_PLUGIN_FILE );
	}



	static function is_citadela_active() {
		return defined( 'CITADELA_THEME' );
	}
}
