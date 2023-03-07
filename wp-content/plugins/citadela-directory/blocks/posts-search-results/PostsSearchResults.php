<?php

namespace Citadela\Directory\Blocks;

class PostsSearchResults extends Block {

    protected static $slug = 'posts-search-results';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        if ( is_search() && isset( $_REQUEST[ 'ctdl' ] ) ) {
            
            $args = [
                's'                 => isset($_REQUEST['s']) ? $_REQUEST['s'] : '',
                'category'          => isset($_REQUEST['category']) ? $_REQUEST['category'] : null,
                'location'          => isset($_REQUEST['location']) ? $_REQUEST['location'] : null,
                'posts_per_page'    => isset($_REQUEST['posts_per_page']) ? $_REQUEST['posts_per_page'] : get_query_var( 'posts_per_page' ),
                'order'             => $attributes['postsOrder'],
                'orderby'           => $attributes['postsOrderBy'],
                'paged'             => get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1 ,
            ];
            if( isset( $attributes['skipStartPosts'] ) && $attributes['skipStartPosts'] > 0 ){
                $args['offset'] = $attributes['skipStartPosts'];
            }

            $query = \CitadelaDirectorySearch::getBlogPosts( $args );
            
        } elseif ( is_category() || is_tag() || is_date() || is_author() ) {
            global $wp_query;
            $query = $wp_query;
        } else {
            return; //prevent loading block elsewhere
        }

        $activeProPlugin = defined( 'CITADELA_PRO_PLUGIN' );

        //set grid type
        $gridType = "grid-type-1";
        if( $attributes['layout'] == "list"){
            $gridType = "grid-type-3";
        }
        if( $attributes['layout'] == "simple"){
            $gridType = "";
        }

        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $classes[] = "layout-".$attributes['layout'];
        if( $attributes['layout'] !== "simple" ){
            $classes[] = "size-".$attributes['size'];
        }
        $classes[] = $gridType;
        if($attributes['showFeaturedImage']) $classes[] = "show-item-featured-image";
        if($attributes['showDate']) $classes[] = "show-item-date";
        if($attributes['showDescription']) $classes[] = "show-item-description";
        if($attributes['showCategories']) $classes[] = "show-item-categories";
        if($attributes['showLocations']) $classes[] = "show-item-locations";

        if ( $activeProPlugin ){
            $classes[] = "border-{$attributes['borderWidth']}";
            if( $attributes['textColor'] ) $classes[] = "custom-text-color";
            if( $attributes['decorColor'] ) $classes[] = "custom-decor-color";
            if( $attributes['backgroundColor'] ) $classes[] = "custom-background-color";
            if( $attributes['dateColor'] ) $classes[] = "custom-date-color";
        }

        $styles = [
            'articleStyle' => '',
            'itemContentStyle' => '',
            'footerStyle' => '',
            'itemDataStyle' => '',
            'itemDataLocationStyle' => '',
            'itemDataCategoryStyle' => '',
            'dateStyle' => '',
            'entryMetaLinksStyle' => '',
            'commentsLinkStyle' => '',
            'stickyStyle' => '',
        ];
        
        if( $activeProPlugin ){
            $articleStyle = implode('', [ 
                $attributes['layout'] == 'simple' && $attributes['textColor'] ? "color: " . esc_attr( $attributes['textColor'] ) . ";" : "",
                $attributes['layout'] == 'simple' && $attributes['backgroundColor'] ? "background-color: " . esc_attr( $attributes['backgroundColor'] ) . ";" : "",
                $attributes['layout'] == 'simple' && $attributes['borderColor'] ? "border-color: " . esc_attr( $attributes['borderColor'] ) . ";" : "",
            ] ) . '"';
            $styles['articleStyle'] = $articleStyle ? "style=\"{$articleStyle}\"" : "";
            
            $itemContentStyle = implode('', [ 
                $attributes['layout'] != 'simple' && $attributes['textColor'] ? "color: " . esc_attr( $attributes['textColor'] ) . ";" : "",
                $attributes['layout'] != 'simple' && $attributes['backgroundColor'] ? "background-color: " . esc_attr( $attributes['backgroundColor'] ) . ";" : "",
                $attributes['layout'] != 'simple' && $attributes['borderColor'] ? "border-color: " . esc_attr( $attributes['borderColor'] ) . ";" : "",
            ] ) . '"';
            $styles['itemContentStyle'] = $itemContentStyle ? "style=\"{$itemContentStyle}\"" : "";
            
            $footerStyle = implode('', [ 
                $attributes['layout'] != 'simple' && $attributes['borderColor'] ? "border-color: " . esc_attr( $attributes['borderColor'] ) . ";" : "",
            ] ) . '"';
            $styles['footerStyle'] = $footerStyle ? "style=\"{$footerStyle}\"" : "";
            
            $itemDataStyle = implode('', [ 
                $attributes['layout'] != 'simple' && $attributes['borderColor'] ? "border-color: " . esc_attr( $attributes['borderColor'] ) . ";" : "",
            ] ) . '"';
            $styles['itemDataStyle'] = $itemDataStyle ? "style=\"{$itemDataStyle}\"" : "";
            
            $itemDataLocationStyle = implode('', [ 
                $attributes['decorColor'] ? "color: " . esc_attr( $attributes['decorColor'] ) . ";" : "",
            ] ) . '"';
            $styles['itemDataLocationStyle'] = $itemDataLocationStyle ? "style=\"{$itemDataLocationStyle}\"" : "";
            
            $itemDataCategoryStyle = implode('', [ 
                $attributes['decorColor'] ? "border-color: " . esc_attr( $attributes['decorColor'] ) . ";" : "",
            ] ) . '"';
            $styles['itemDataCategoryStyle'] = $itemDataCategoryStyle ? "style=\"{$itemDataCategoryStyle}\"" : "";
            
            $dateStyle= implode('', [ 
                $attributes['layout'] == 'list' && $attributes['decorColor'] ? "color: " . esc_attr( $attributes['decorColor'] ) . ";" : "",
                $attributes['layout'] == 'box' && $attributes['decorColor'] ? "background-color: " . esc_attr( $attributes['decorColor'] ) . ";" : "",
                $attributes['layout'] == 'box' && $attributes['dateColor'] ? "color: " . esc_attr( $attributes['dateColor'] ) . ";" : "",
            ] );
            $styles['dateStyle'] = $dateStyle ? "style=\"{$dateStyle}\"" : "";
            
            $entryMetaLinksStyle = implode('', [ 
                $attributes['layout'] == 'simple' && $attributes['decorColor'] ? "color: " . esc_attr( $attributes['decorColor'] ) . ";" : "",
            ] );
            $styles['entryMetaLinksStyle'] = $entryMetaLinksStyle ? "style=\"{$entryMetaLinksStyle}\"" : "";

            $commentsLinkStyle = implode('', [ 
                $attributes['layout'] == 'simple' && $attributes['borderColor'] ? "border-color: " . esc_attr( $attributes['borderColor'] ) . ";" : "",
                $attributes['layout'] == 'simple' && $attributes['decorColor'] ? "color: " . esc_attr( $attributes['decorColor'] ) . ";" : "",
            ] );
            $styles['commentsLinkStyle'] = $commentsLinkStyle ? "style=\"{$commentsLinkStyle}\"" : "";

            $stickyStyle = implode('', [ 
                $attributes['decorColor'] ? "color: " . esc_attr( $attributes['decorColor'] ) . ";" : "",
            ] );
            $styles['stickyStyle'] = $stickyStyle ? "style=\"{$stickyStyle}\"" : "";
        }

        $template_args = $attributes;

        $postType = 'post';

        ob_start();
        ?>

        <div class="wp-block-citadela-blocks ctdl-posts-search-results <?php echo esc_attr( implode(" ", $classes) ); ?>">
            <?php include dirname( __FILE__ ) . "/../../plugin/parts/search-results.php"; ?>
        </div>

        <?php

        /* Restore original Post Data */
        wp_reset_postdata();

        return ob_get_clean();
    }
    
}