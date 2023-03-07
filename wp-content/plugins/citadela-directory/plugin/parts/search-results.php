<?php

/* required template variables should be defined before including this file */
$query = isset($query) ? $query : null;
$postType = isset($postType) ? $postType : 'citadela-item';
$template_args = isset($template_args) ? $template_args : [];
$styles = isset($styles) ? $styles : [];


if ( $query && $query->have_posts() ) {
    ?>
    <div class="citadela-block-articles">
        <div class="citadela-block-articles-wrap">
            <?php while ( $query->have_posts() ) : ?>
            <?php
            /* activate global tag templates and global $post variable within custom loop (do not forget to reset postdata from where custom loop was created) */
            $query->the_post();
            global $post;

            if ($postType == 'citadela-item') {
                include dirname( __FILE__ ) . '/../cpt/item/templates/parts/item-container.php';
            } elseif ($postType == 'post') {
                if ( isset( $template_args[ 'layout' ] ) && $template_args[ 'layout' ] == 'simple' ) {

                    include dirname( __FILE__ ) . '/../cpt/post/templates/parts/post-container-simple.php';

                } else {

                    include dirname( __FILE__ ) . '/../cpt/post/templates/parts/post-container.php';

                }
            }

            ?>
            <?php endwhile; ?>
        </div>
    </div>

    <?php
    
    $total = $query->max_num_pages;

    // maybe customize pagination parameters if there is offset in query
    if( $template_args ){
        if( isset( $template_args['skipStartPosts'] ) && $template_args['skipStartPosts'] > 0 ){
            $total = max( 0, $query->found_posts - $template_args['skipStartPosts'] );
            $total = ceil( $total / intval( get_option( 'posts_per_page' ) ) );
        }
    }

    $links = paginate_links( [
        'mid_size' => 2,
        'prev_text' => esc_html__( 'Previous', 'citadela-directory' ),
        'next_text' => esc_html__( 'Next', 'citadela-directory' ),
        'total' => $total,
        'current' => get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1,
		'screen_reader_text' => esc_html__('Posts navigation', 'citadela-directory'),
    ] );

    /* wrap links with markup https://developer.wordpress.org/reference/functions/get_the_posts_pagination/ */
    echo _navigation_markup( $links, 'pagination', esc_html__('Posts navigation', 'citadela-directory') );
} else {
    if ( $postType == 'citadela-item' ) {
        include dirname( __FILE__ ) . '/../cpt/item/templates/parts/content-none.php';
    } elseif ( $postType == 'post' ) {
        include dirname( __FILE__ ) . '/../cpt/post/templates/parts/content-none.php';
    }
}