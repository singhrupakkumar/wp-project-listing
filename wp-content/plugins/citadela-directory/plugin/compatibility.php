<?php

function ctdl_directory_check_lite_version() {

    if ( ! defined( 'CITADELA_DIRECTORY_LITE_PLUGIN' ) ) return;
    
    $pluginBasename = 'citadela-directory-lite/citadela-directory-lite.php';
    
    deactivate_plugins( $pluginBasename );

    $message = sprintf(
        '<h3 class="notice-title">%1$s</h3><p>%2$s</p>',
        // translators: %s plugin's name
        sprintf( esc_html__( '%s plugin is active', 'citadela-directory' ), esc_html__( 'Citadela Directory Lite', 'citadela-directory' ) ),
        // translators: %s plugin's name
        esc_html__( "We've deactivated Citadela Directory Lite plugin. You can now activate and enjoy full version of the plugin.", 'citadela-directory' )
    );
    wp_die( $message, '',  array( 'back_link' => true ) );

}