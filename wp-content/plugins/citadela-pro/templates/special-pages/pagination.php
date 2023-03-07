<?php

$total = $query->max_num_pages;

// maybe customize pagination parameters if there is offset in query
if( $template_args ){
    if( isset( $template_args['skipStartPosts'] ) && $template_args['skipStartPosts'] > 0 ){
        $total = max( 0, $query->found_posts - $template_args['skipStartPosts'] );
        $total = ceil( $total / intval( get_option( 'posts_per_page' ) ) );
    }
}

the_posts_pagination( [
    'mid_size' => 2,
    'prev_text' => __( 'Previous', 'citadela-pro' ),
    'next_text' => __( 'Next', 'citadela-pro' ),
    'total' => $total,
    'paged' => get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1 ,
    'screen_reader_text' => __('Posts navigation', 'citadela-pro'),
] );