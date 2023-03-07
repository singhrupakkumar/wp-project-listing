<?php

namespace Citadela\Pro\Blocks;

use Citadela\Pro\Asset;

class Block {

    public $slug = null;
    protected $namespace = 'citadela-pro';



    function __construct() {
        add_action( 'admin_init', [ $this, 'init' ]);
        add_action( 'enqueue_block_assets', [ $this, 'enqueue_block_assets' ]);

        $this->register();
    }

    function init(){
        return;
    }


    protected function attributes() {
        return [];
    }



    function register() {
        if ( ! $this->slug ) return;

        // Skip block registration if Gutenberg is not enabled/merged.
        if ( ! function_exists( 'register_block_type_from_metadata' ) ) {
            return;
        }

        Asset::register( "citadela-{$this->slug}-block-style", "/blocks/{$this->slug}/style.css" );
        Asset::register( "citadela-{$this->slug}-editor", "/blocks/{$this->slug}/editor.css" );
        
        register_block_type_from_metadata( Asset::path() . "/blocks/{$this->slug}/src", [
            'editor_style'    => "citadela-{$this->slug}-editor",
            'style'           => "citadela-{$this->slug}-block-style",
            'render_callback' => method_exists( $this, 'render' ) ? [ $this, 'render' ] : [],
        ] );
    }

    function enqueue_block_assets() {
        $script_url = \ctdl\pro\url( "/assets/blocks/{$this->slug}/src" ) . "/frontend.js";
        $script_path = \ctdl\pro\path( "/assets/blocks/{$this->slug}/src" ) . "/frontend.js";
        if( file_exists( $script_path ) ){
            $script_dependencies = [ 'jquery' ];

            wp_register_script(
                "{$this->slug}-frontend",
                $script_url,
                $script_dependencies,
                filemtime( $script_path )
            );

            wp_enqueue_script( "{$this->slug}-frontend" );
        }
    }
    
}
