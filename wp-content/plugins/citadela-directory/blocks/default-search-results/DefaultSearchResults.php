<?php

namespace Citadela\Directory\Blocks;

class DefaultSearchResults extends Block {

    protected static $slug = 'default-search-results';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        if ( is_search() && ! isset( $_REQUEST[ 'ctdl' ] ) ) {
            
            global $wp_query;
            $query = $wp_query;
            
        } else {
            return; //prevent loading block elsewhere
        }
        
        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $classes[] = "layout-list";
        $classes[] = "show-item-featured-image";
        $classes[] = "show-item-date";
        $classes[] = "show-item-description";
        $classes[] = "show-item-categories";

        $template_args = array_merge( [
            'layout' => 'list',
            'showFeaturedImage' => true,
            'showCategories' => true,
            'showLocations' => true,
            'showDescription' => true,
            'showDate' => true,
        ], $attributes);

        ob_start();
        ?>

        <div class="wp-block-citadela-blocks ctdl-default-search-results <?php echo esc_attr( implode(" ", $classes) ); ?>">
            <?php if ( have_posts() ) : ?>

            <?php

            while ( have_posts() ) :

                the_post();
                $post = get_post();
                include dirname( __FILE__ ) . '/../../plugin/parts/default-search-results-content.php';               
            endwhile;

            $links = paginate_links( [
                'mid_size' => 2,
                'prev_text' => esc_html__( 'Previous', 'citadela-directory' ),
                'next_text' => esc_html__( 'Next', 'citadela-directory' ),
                'total' => $query->max_num_pages,
                'current' => get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1,
                'screen_reader_text' => esc_html__('Posts navigation', 'citadela-directory'),
            ] );

            /* wrap links with markup https://developer.wordpress.org/reference/functions/get_the_posts_pagination/ */
            echo _navigation_markup( $links, 'pagination', esc_html__('Posts navigation', 'citadela-directory') );

        else :

            include dirname( __FILE__ ) . '/../../plugin/cpt/post/templates/parts/content-none.php';

        endif;
        ?>
        </div>

        <?php

        /* Restore original Post Data */
        wp_reset_postdata();

        return ob_get_clean();
    }
    
}