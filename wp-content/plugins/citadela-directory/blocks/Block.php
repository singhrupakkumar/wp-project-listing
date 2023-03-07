<?php

namespace Citadela\Directory\Blocks;

class Block {

    protected static $plugin = null;

    protected static $slug = null;

    protected static $attributes = [];

    function __construct() {
        
        self::$plugin = \CitadelaDirectory::getInstance();
        
        $this->register();

    }

    function register() {
        $slug = static::$slug;

        if (!$slug) return;

        // Skip block registration if Gutenberg is not enabled/merged.
        if ( ! function_exists( 'register_block_type_from_metadata' ) ) {
            return;
        }

        $dir = dirname( __FILE__ );

        $args = [
            'render_callback' => method_exists(get_called_class(), 'renderCallback') ? [get_called_class(), 'renderCallback'] : [],
        ];

        $editor_css = "$slug/editor.css";
        if ( file_exists( "$dir/$editor_css" ) ) {
            wp_register_style(
                "citadela-{$slug}-editor",
                plugins_url( $editor_css, __FILE__ ),
                [],
                filemtime( "$dir/$editor_css" )
            );
            $args[ 'editor_style' ] = "citadela-{$slug}-editor";
        }



        $style_css = "$slug/style.css";
        if ( file_exists( "$dir/$style_css" ) ) {
            wp_register_style(
                "citadela-{$slug}-block-style",
                plugins_url( $style_css, __FILE__ ),
                [],
                filemtime( "$dir/$style_css" )
            );
            $args[ 'style' ] = "citadela-{$slug}-block-style";

        }

        $dir_name = $slug;
        
        register_block_type_from_metadata( __DIR__ . "/${dir_name}/src", $args );
    }
}