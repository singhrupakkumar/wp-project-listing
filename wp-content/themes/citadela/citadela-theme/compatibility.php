<?php

function citadela_support_php() {
    // https://wordpress.org/about/requirements/
    if ( version_compare( PHP_VERSION, '5.6.20', '<' ) ) {
        add_filter( 'template_include', '__return_null', 99 );
        switch_theme( WP_DEFAULT_THEME );
        unset( $_GET['activated'] );

        add_action( 'admin_notices', function() {
            printf(
                '<div class="notice notice-warning notice-large"><p><strong class="notice-title">%1$s</strong><br>%2$s</p></div>',
                // translators: %s - theme's name
                sprintf( esc_html__( '%s theme and PHP incompatiblity', 'citadela' ), 'Citadela' ),
                // translators: 1. url to upgrading guide, 2. theme's name
                sprintf(
                    wp_kses_post( __( "We've noticed that you're running an outdated version of PHP which is no longer supported. Make sure your site is fast and secure, by <a href='%1\$s'>upgrading PHP to the latest version</a>. Minimal requirement for %2\$s is <strong>PHP 5.6.20</strong>.", 'citadela' ) ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
                    esc_url( wp_get_update_php_url() ),
                    'Citadela'
                )
            );
        } );
        return false;
    }
    return true;
}



function citadela_support_wp() {
    global $wp_version;
    if ( version_compare( $wp_version, '5.2', '<' ) ) {
        add_filter( 'template_include', '__return_null', 99 );
        switch_theme( WP_DEFAULT_THEME );
        unset( $_GET['activated'] );

        add_action( 'admin_notices', function() {
            printf(
                '<div class="notice notice-warning notice-large"><p><strong class="notice-title">%1$s</strong><br>%2$s</p></div>',
                // translators: %s - theme's name
                sprintf( esc_html__( '%s theme and WordPress version incompatiblity', 'citadela' ), 'Citadela' ),
                // translators: 1. url to upgrading guide, 2. theme's name
                sprintf(
                    wp_kses_post( __( "We've noticed that you're running an outdated version of WordPress. Make sure your site is secure, by <a href='%1\$s'>updating WordPress to the latest version</a>. Minimal requirement for %2\$s is <strong>WordPress 5.2</strong>.", 'citadela' ) ),  // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
                    esc_url( network_admin_url( 'update-core.php' ) ),
                    'Citadela'
                )
            );
        } );
        return false;
    }
    return true;
}



function citadela_handle_ait_plugins_support() {
    $ait_plugins = array_filter(get_option( 'active_plugins', [] ), function ( $plugin ) {
        return ( substr( $plugin, 0, 4 ) === 'ait-' );
    } );

    if( empty( $ait_plugins ) ) return;

    if ( ! function_exists( 'deactivate_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
    }
    deactivate_plugins( $ait_plugins );

    add_action( 'admin_notices', function() use ($ait_plugins) {
        unset( $_GET['activate'] );

        $ait_plugin_names = array_map( function ( $plugin ) {
            return dirname( $plugin );
        }, $ait_plugins );

        printf(
            '<div class="notice notice-warning notice-large"><p><strong class="notice-title">%1$s</strong><br>%2$s</p></div>',
            // translators: %s - theme's name
            sprintf( esc_html__( '%s theme and AIT plugins incompatiblity', 'citadela' ), 'Citadela' ),
            sprintf(
                // translators: %s - list of plugins
                esc_html__( "We've noticed that you're running Citadela incompatible AIT plugins (%s) so we deactivated them.", 'citadela' ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
                implode( ', ', $ait_plugin_names ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            )
        );
    } );

}
