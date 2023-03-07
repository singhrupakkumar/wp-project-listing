<?php

namespace Citadela\Pro\Content_Settings;

use Citadela\Pro\Asset;

class Feature {

	function __construct() {

		$this->register_pages_meta();
      
		add_action( 'body_class', [ $this, 'body_class' ] );

        if( ! current_user_can( 'edit_posts' ) ) return;
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
	}

	function register_pages_meta() {
		$post_types = [ 'page', 'special_page' ];

        if( defined('CITADELA_DIRECTORY_PLUGIN') ){
            if( \CitadelaDirectory::getInstance()->ItemPageLayout_instance->allowed_editor ){
                $post_types[] = 'citadela-item';            
            }
        }
        
        foreach ( $post_types as $post_type ) {

            register_meta( 'post', '_citadela_content_width', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

        }
        
        $script_path = \ctdl\pro\path( '/assets/content-settings' );
        $script_url = \ctdl\pro\url( '/assets/content-settings' );
        
        $editor_asset_file = include( "{$script_path}/build/editor.asset.php" );
       
        wp_register_script(
            'citadela-pro-content-settings-editor-js',
            "{$script_url}/build/editor.js",
            array_merge( [ 'wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-element' ], $editor_asset_file[ 'dependencies' ] ) ,
            filemtime( "{$script_path}/build/editor.js" ),
            true
        );
        wp_set_script_translations( "citadela-pro-content-settings-editor-js", 'citadela-pro', \ctdl\pro\path( '/languages' ) );

	}

    function enqueue_block_editor_assets(){
        $current_screen = get_current_screen();
        // enqueue settings only for editor on pages or posts
        if( $current_screen && $current_screen->id != 'widgets' ){
            wp_enqueue_script( 'citadela-pro-content-settings-editor-js' );
        }
    }

	function sanitize_checkbox( $meta_value, $meta_key, $meta_type ) {
		return $meta_value ? $meta_value : 0;
	}


	function body_class( $classes ) {
		$themeInstance = \Citadela_Theme::get_instance();

        $template = $themeInstance->get_page_template_type();
        if( $template == 'page-fullwidth' ){
            $content_width = $themeInstance->get_page_meta( '_citadela_content_width' );
            $content_width = $content_width ? $content_width : "default";
            if( $content_width != 'default' && $content_width != '' ){
                $classes[] = "{$content_width}-content-width";
            }
        }

		return $classes;
    }

}
