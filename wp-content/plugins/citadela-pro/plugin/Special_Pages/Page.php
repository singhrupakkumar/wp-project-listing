<?php

namespace Citadela\Pro\Special_Pages;

use Citadela\Pro\Asset;
use Citadela\Pro\Template;

class Page {

    static function config( $key = null ) {
        return \ctdl\pro\dot_get( [

			'blog' => [
                'option_key' => 'citadela_blog_page',
                'title'      => __( "Blog Page", 'citadela-pro' ),
                'template'   => Template::path( '/special-pages/blog-page.php' ),
			],

		], $key );
    }



    static function id( $slug ) {
        return get_option( self::config( "{$slug}.option_key" ) );
    }

    static function get_ids() {
        $pages_ids = [];
        foreach ( self::config() as $slug => $settings ) {
            array_push( $pages_ids, get_option( $settings['option_key'] ) );
        }
        return $pages_ids;
    }

    static function slug( $id ) {
        foreach ( self::config() as $slug => $settings ) {
            if ( get_option( $settings['option_key'] ) == $id ) {
                return $slug;
            }
        }
        return null;
    }



    static function prepare() {
        $missing = [];
        
        foreach ( self::config() as $slug => $settings ) {
            $id = self::id( $slug);
            $post_type = get_post_type( intval( $id ) );
            if ( $post_type !== "special_page" ) {
                $missing[] = $slug;
            }
        }

        if ( $missing ) {
            $defaultContent = json_decode( file_get_contents( Asset::path( '/blocks/default-content.json' ) ) );

            foreach ( $missing as $slug ) {
                $args = [
                    'post_type' => 'special_page',
                    'post_status' => 'publish',
                    'post_title' => self::config( "{$slug}.title" ),
                    'post_content' => $defaultContent->$slug->content,
                ];

                $id = wp_insert_post( $args );
                update_option( self::config( "{$slug}.option_key" ), $id );
            }
        }
    }



    static function content( $key ) {
        $pageId = get_option( self::config( "{$key}.option_key" ) );
        $page = get_post( $pageId );

        return $page->post_content;
    }
}
