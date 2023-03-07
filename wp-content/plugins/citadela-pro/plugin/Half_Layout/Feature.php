<?php

namespace Citadela\Pro\Half_Layout;

class Feature {

    protected $feature_slug = 'half-layout';
    protected $is_half_layout = false;
    protected $theme_instance = null;

    function __construct() {

        //allow only for directory plugin for now
        if ( ! defined('CITADELA_DIRECTORY_PLUGIN') ) return;

        $this->theme_instance = \Citadela_Theme::get_instance();

        $this->register_pages_meta();
      
        add_action( 'body_class', [ $this, 'body_class' ] );
        add_action( 'citadela_half_layout_content', [ $this, 'half_layout_content' ] );
        add_filter( 'theme_page_templates', [ $this, 'theme_page_templates' ] );
        add_filter( 'theme_special_page_templates', [ $this, 'theme_page_templates' ] );
        
        // load Item Page Template only for frontend, we set template post meta with custom input on item edit page
        if( ! is_admin() ){
            add_filter( 'theme_citadela-item_templates', [ $this, 'theme_item_templates' ] );
        }

        if( ! current_user_can( 'edit_posts' ) ) return;
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
    }

    function body_class( $classes ){
        
        if( $this->theme_instance->get_page_template_type() == 'half-layout' ) {
            $post_id = $this->theme_instance->get_page_id();
            $position = get_post_meta( $post_id, '_citadela_half_layout_position', true );
            $position = $position ? $position : 'right';
            $classes[] = "{$position}-part";
        }

        return $classes;
    }

    function half_layout_content() {

        if( $this->theme_instance->get_page_template_type() != 'half-layout' ){ 
            return '';
        }

        ?>
        <div class="half-layout-part">
            <?php
                //render by default map from Directory plugin for now
                \CitadelaDirectory::getInstance()->HalfLayoutMap_instance->render_map_html();
            ?>
        </div>
        <?php
    }

    function enqueue_block_editor_assets(){
        $current_screen = get_current_screen();
        // enqueue settings only for editor on pages or posts
        if( $current_screen && $current_screen->id != 'widgets' ){
            wp_enqueue_script( "citadela-pro-half-layout-editor-js" );
        }
    }

    function register_pages_meta() {

        $post_types = [ 'page', 'special_page' ];

        if( defined('CITADELA_DIRECTORY_PLUGIN') ){
            if( \CitadelaDirectory::getInstance()->ItemPageLayout_instance->allowed_editor ){
                $post_types[] = 'citadela-item';            
            }
        }

        foreach ( $post_types as $post_type ) {

            register_meta( 'post', '_citadela_half_layout_position', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

        }

        $path = \ctdl\pro\path( "/assets/{$this->feature_slug}" );
        $url = \ctdl\pro\url( "/assets/{$this->feature_slug}" );
        
        $editor_asset_file = include( "{$path}/build/editor.asset.php" );

        wp_register_script(
            "citadela-pro-{$this->feature_slug}-editor-js",
            "{$url}/build/editor.js",
            array_merge( [ 'wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-element' ], $editor_asset_file[ 'dependencies' ] ) ,
            filemtime( "{$path}/build/editor.js" ),
            true
        );
        wp_set_script_translations( "citadela-pro-{$this->feature_slug}-editor-js", 'citadela-pro', \ctdl\pro\path( '/languages' ) );
    }

    function theme_page_templates( $templates ){
        if ( !(isset($_GET['page'], $_GET['post_type']) && $_GET['page'] === 'tribe-common' && $_GET['post_type'] === 'tribe_events') ) {
            $templates["half-layout-template"] = __( 'Half layout page', 'citadela-pro');
        }
        return $templates;
    }

    function theme_item_templates( $templates ){
        global $post;
        $item_detail_options = get_option('citadela_directory_item_detail');
        $allowed_editor = $item_detail_options && isset( $item_detail_options['enable'] ) && $item_detail_options['enable'];
        $ignore_special_page = get_post_meta( $post->ID, '_citadela_ignore_special_page', true );
        
        if( $allowed_editor && $ignore_special_page ){
            $templates["half-layout-template"] = __( 'Half layout page', 'citadela-pro');
        }
        return $templates;
    }

}