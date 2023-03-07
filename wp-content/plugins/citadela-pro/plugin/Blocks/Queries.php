<?php

namespace Citadela\Pro\Blocks;

class Queries {

    static function posts( $block_attributes ) {
        $tax_query = [];

        if ( $block_attributes['category'] ) {
            array_push( $tax_query, [
                'taxonomy' => 'category',
                'field' => 'id',
                'terms' => $block_attributes['category'],
            ]);
        }

        $args = [
            'post_type'      => 'post',
            'ignore_sticky_posts' => ! $block_attributes['stickyPostsFirst'],
            'posts_per_page' => $block_attributes['count'],
            'order'          => $block_attributes['postsOrder'],
            'orderby'        => $block_attributes['postsOrderBy'],
            'tax_query'      => $tax_query,
            'post_status'    => 'publish',
        ];
        
        if( isset( $block_attributes['skipStartPosts'] ) && $block_attributes['skipStartPosts'] > 0 ){
            $args['offset'] = $block_attributes['skipStartPosts'];
        }

        $query = new \WP_Query( $args );

        
        $sticky_posts = get_option( 'sticky_posts' );
        if( $block_attributes['stickyPostsFirst'] && is_array( $sticky_posts ) && count( $sticky_posts ) > 0 ){
            // there are sticky posts in the front, rather limit query loop later than delete posts from query to make sure the correct number of posts will be displayed
            $query->set( 'showMaxSticky', $block_attributes['count'] );
        }

        return $query ;
    }

    /*
    *   replacement for default wp query, we need custom query modified by Block settings
    */
    static function blog_posts( $block_attributes ) {
        $args = [
            'post_type'      => 'post',
            'ignore_sticky_posts' => ! $block_attributes['stickyPostsFirst'],
            'post_status'    => 'publish',
            'order'          => $block_attributes['postsOrder'],
            'orderby'        => $block_attributes['postsOrderBy'],
            'paged'          => get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1 ,
        ];

        if( isset( $block_attributes['skipStartPosts'] ) && $block_attributes['skipStartPosts'] > 0 ){
            $per_page = intval( get_option( 'posts_per_page' ) );
            $current_page = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
            $offset = ( $current_page - 1 ) * $per_page + $block_attributes['skipStartPosts'];
            $args['offset'] = $offset;
        }

        return new \WP_Query( $args );
    }
}
