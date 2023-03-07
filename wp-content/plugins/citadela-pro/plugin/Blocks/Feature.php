<?php

namespace Citadela\Pro\Blocks;

use Citadela\Pro\Asset;
use \Citadela\Pro\Special_Pages\Page;

class Feature {

	protected $blocks = [];



	function __construct() {
        global $wp_version;
        // WP 5.8 and early compatibility, refer to https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#block_categories_all
        if( class_exists("WP_Block_Editor_Context") ){
            add_filter('block_categories_all', [ $this, 'block_categories'], 10, 2);
        }else{
            add_filter('block_categories', [ $this, 'block_categories'], 10, 2);
        }
        
        add_action( "enqueue_block_editor_assets", [ $this, 'enqueue_editor_assets' ] );

        foreach ( [
            new Blog_Posts,
            new Posts,
        ] as $block ) {
            $this->blocks[] = $block;
        }

        // blocks from Citadela Blocks plugin, register only after Blocks plugin deactivation
        if( ! defined( 'CITADELA_BLOCKS_PLUGIN') ){
            foreach ( [
                new Price_Table,
                new Spacer,
                new Service,
                new Responsive_Text,
                new Page_Title,
                new Opening_Hours,
                new Cluster,
            ] as $block ) {
                $this->blocks[] = $block;
            }
        }
    }



    function block_categories( $categories, $post ) {
        $new_categories = [
            'citadela-blocks' => [
                'slug'  => 'citadela-blocks',
                'title' => __( 'Citadela Blocks', 'citadela-pro' ),
            ],

            'citadela-posts-blocks' => [
                'slug'  => 'citadela-posts-blocks',
                'title' => __( 'Citadela Posts Blocks', 'citadela-pro' ),
            ],
        ];

        foreach ( $categories as $cat ) {
			if ( $cat[ 'slug' ] === 'citadela-pro-blocks' ) {
                unset( $new_categories[ 'citadela-pro-blocks' ] );
            }

            if ( $cat[ 'slug' ] === 'citadela-posts-blocks' ) {
                unset( $new_categories[ 'citadela-posts-blocks' ] );
            }
        }

        return array_merge( array_values( $new_categories ), $categories );
    }



    function enqueue_editor_assets() {
        global $post;
        
        // enqueue blocks        
        $current_screen = get_current_screen();
        
        $deps = [
            'wp-blocks',
            'wp-i18n',
            'wp-element',
            'wp-editor',
            'wp-api-fetch',
            'wp-components',
            'citadela-pro-vendor-classnames',
            'citadela-pro-vendor-fontawesome-iconpicker',
        ];

        if( $current_screen && $current_screen->id == 'widgets' ){
            // "wp-editor" script should not be enqueued together with the new widgets editor (wp-edit-widgets or wp-customize-widgets)
            unset( $deps[array_search('wp-editor', $deps)] );
            $deps[] = 'wp-edit-widgets';
        }
        
        foreach ($this->blocks as $block) {
            // register scripts for blocks, scripts are enqueued via block.json file
            Asset::enqueue( "citadela-{$block->slug}-block", "/blocks/{$block->slug}/index.js", $deps );
	    wp_set_script_translations( "citadela-{$block->slug}-block", 'citadela-pro', \ctdl\pro\path( '/languages'));
            if( method_exists( $block, 'block_vars' ) ){
                Asset::localize( "citadela-{$block->slug}-block",  '_citadela_'.str_replace('-', '_', $block->slug).'_block_vars', $block->block_vars() );
            }
        }
        
        // enqueue javascript to deregister unwanted blocks

        $special_blocks = array_filter( $this->blocks, function( $block ) {
            return method_exists( $block, 'allowed_contexts' );
        } );

        $unregister = array_map( function( $block ) {
            return "citadela-pro/{$block->slug}";
        }, $special_blocks );

        $special_page = $post ? Page::slug( $post->ID ) : false;

        if ( $special_page ) {
            foreach ( $special_blocks as $block ) {
                if ( in_array( $special_page, $block->allowed_contexts() ) ) {
                    $unregister = array_diff( $unregister, ["citadela-pro/{$block->slug}"] );
                }
            }
        }

        Asset::enqueue( 'citadela-pro-special-blocks', '/js/special-blocks.js' );
		Asset::localize( 'citadela-pro-special-blocks',  '_citadelaProSpecialBlocks', [
            'disallowed_blocks' => $unregister,
		] );
    }
}
