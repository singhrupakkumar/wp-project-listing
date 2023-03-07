<?php

namespace Citadela\Directory\ItemPageLayout;

class Feature {

    protected $plugin = null;

    public $allowed_editor = null;
    public $ignore_special_page = null;

    function __construct() {
        $this->plugin = \CitadelaDirectory::getInstance();
        
        $item_detail_options = get_option('citadela_directory_item_detail');
        $this->allowed_editor = $item_detail_options && isset( $item_detail_options['enable'] ) && $item_detail_options['enable'];

        add_action('after_setup_theme', function () {
			if (\Citadela::$allowed) {
                add_action( 'init', [ $this, 'init' ] );
                add_action( 'wp', [ $this, 'wp' ] );
            }
        }, 100);

    }

    function init() {
        $this->register_pages_meta();

        if( ! current_user_can( 'edit_posts' ) ) return;
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
    }

    function wp(){
        global $post;
        if( $post ){
            $this->ignore_special_page = $this->allowed_editor && get_post_meta( $post->ID, '_citadela_ignore_special_page', true );
        }
    }

    function enqueue_block_editor_assets(){
        $current_screen = get_current_screen();
        // enqueue settings only for editor on pages or posts
        if( $current_screen && $current_screen->id != 'widgets' ){
            wp_enqueue_script( 'citadela-directory-item-page-layout-editor-js' );
        }
    }

    function register_pages_meta() {
        $post_types = [ 'citadela-item' ];
        
        foreach ( $post_types as $post_type ) {
            
            register_meta( 'post', '_citadela_ignore_special_page', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'boolean',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_page_template', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );
            
        }
        
        $path = "{$this->plugin->paths->dir->assets}/citadela/item-page-layout";
        $url = "{$this->plugin->paths->url->assets}/citadela/item-page-layout";
        
        $editor_asset_file = include( "{$path}/build/editor.asset.php" );

        wp_register_script(
            'citadela-directory-item-page-layout-editor-js',
            "{$url}/build/editor.js",
            array_merge( [ 'wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-element' ], $editor_asset_file[ 'dependencies' ] ) ,
            filemtime( "{$path}/build/editor.js" ),
            true
        );
        wp_set_script_translations( 'citadela-directory-item-page-layout-editor-js', 'citadela-directory', $this->plugin->paths->dir->languages );

    }
}