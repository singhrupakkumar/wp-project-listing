<?php

class CitadelaDirectoryCompatibility
{

    protected static $name = 'Citadela Listing';
    protected static $pluginBasename = 'citadela-directory/citadela-directory.php';



    public static function supportPhp() {
        // https://wordpress.org/about/requirements/
        if ( version_compare( PHP_VERSION, '5.6.20', '<' ) ) {

            if ( ! function_exists( 'deactivate_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            deactivate_plugins( self::$pluginBasename );

            add_action( 'admin_notices', function() {
                unset( $_GET['activate'] ); // to get rid off WP's notification msg that plugin was activated, which is not true

                printf(
                    '<div class="notice notice-warning notice-large"><p><strong class="notice-title">%1$s</strong><br>%2$s</p></div>',
                    // translators: %s plugin's name
                    sprintf( esc_html__( '%s plugin and PHP incompatiblity', 'citadela-directory' ), self::$name ),
                    sprintf(
                        // translators: 1. url to upgrading guide, 2. plugin's name
                        wp_kses_post( __( "We've noticed that you're running an outdated version of PHP which is no longer supported, therefore <em>the plugin was deactivated</em>. Make sure your site is fast and secure, by <a href='%1\$s'>upgrading PHP to the latest version</a>. Minimal requirement for %2\$s is <strong>PHP 5.6.20</strong>.", 'citadela-directory' ) ),
                        esc_url( wp_get_update_php_url() ),
                        self::$name
                    )
                );
            } );
            return false;
        }
        return true;
    }



    public static function supportWp() {
        global $wp_version;

        if ( version_compare( $wp_version, '5.2', '<' ) ) {

            if ( ! function_exists( 'deactivate_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            deactivate_plugins( self::$pluginBasename );

            add_action( 'admin_notices', function() {
                unset( $_GET['activate'] ); // to get rid off WP's notification msg that plugin was activated, which is not true

                printf(
                    '<div class="notice notice-warning notice-large"><p><strong class="notice-title">%1$s</strong><br>%2$s</p></div>',
                    // translators: %s plugin's name
                    sprintf( esc_html__( '%s plugin and WordPress incompatiblity', 'citadela-directory' ), self::$name ),
                    sprintf(
                        // translators: 1. url to WordPress Update page, 2. plugin's name
                        wp_kses_post( __( "We've noticed that you're running an outdated version of WordPress, therefore <em>the plugin was deactivated</em>. Make sure your site is secure and up to date, by <a href='%1\$s'>updating WordPress to the latest version</a>. Minimal requirement for %2\$s is <strong>WordPress 5.2</strong>.", 'citadela-directory' ) ),
                        esc_url( network_admin_url( 'update-core.php' ) ),
                        self::$name
                    )
                );
            } );
            return false;
        }
        return true;
    }



    public static function handleThemesSupport() {

        register_activation_hook( self::$pluginBasename, function() {
            if ( ! self::isAitThemeActive() ) return;
            $message = sprintf(
                '<h3 class="notice-title">%1$s</h3><p>%2$s</p>',
                // translators: %s plugin's name
                sprintf( esc_html__( '%s plugin incompatibility', 'citadela-directory' ), self::$name ),
                // translators: %s plugin's name
                sprintf( esc_html__( 'The %s plugin could not be activated. It is not compatible with the current active theme.', 'citadela-directory' ), self::$name )
            );
            wp_die( $message, '',  array( 'back_link' => true ) );
        });

        add_action( 'after_switch_theme', function() {
            if ( ! self::isAitThemeActive() ) return;
            if ( ! function_exists( 'deactivate_plugins' ) )  require_once ABSPATH . 'wp-admin/includes/plugin.php';
            deactivate_plugins( self::$pluginBasename );
        } );

        add_action( 'admin_notices', function() {
            if ( ! self::isAitThemeActive() ) return;
            printf(
                '<div class="notice notice-info notice-large"><p><strong class="notice-title">%1$s</strong><br>%2$s</p></div>',
                // translators: %s plugin's name
                sprintf( esc_html__( '%s plugin incompatibility', 'citadela-directory' ), self::$name ),
                // translators: %s plugin's name
                sprintf( esc_html__( "Recently activated theme is not compatible with %s plugin, therefore the plugin was deactivated.", 'citadela-directory' ), self::$name )
            );
        }, 12 );
    }



    protected static function isAitThemeActive() {
        return ( defined( 'AIT_SKELETON_VERSION' ) or defined( 'AIT_FRAMEWORK_DIR' ) );
    }
}
