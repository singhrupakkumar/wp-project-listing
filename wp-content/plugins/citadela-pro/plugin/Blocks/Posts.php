<?php

namespace Citadela\Pro\Blocks;

use Citadela\Pro\Template;

class Posts extends Block {

   public $slug = 'posts';

   function render( $attributes, $content ) {
       
        $wp_query = Queries::posts( $attributes );
        $template_args = $attributes;

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
        $classes[] = "layout-{$attributes['layout']}";
        $classes[] = "size-{$attributes['size']}";
        $classes[] = $gridType;
        $classes[] = "border-{$attributes['borderWidth']}";
        if( $attributes['showFeaturedImage'] ) $classes[] = "show-item-featured-image";
        if( $attributes['showDate'] ) $classes[] = "show-item-date";
        if( $attributes['showDescription'] ) $classes[] = "show-item-description";
        if( $attributes['showCategories'] ) $classes[] = "show-item-categories";
        if( $attributes['showLocations'] ) $classes[] = "show-item-locations";
        if( $attributes['textColor'] ) $classes[] = "custom-text-color";
        if( $attributes['decorColor'] ) $classes[] = "custom-decor-color";
        if( $attributes['backgroundColor'] ) $classes[] = "custom-background-color";
        if( $attributes['dateColor'] ) $classes[] = "custom-date-color";

        ob_start();
        ?>
        
        <div class="wp-block-citadela-blocks ctdl-posts <?php echo esc_attr( implode( " ", $classes ) );?>">
            <?php
            Template::load( '/special-pages/posts-loop', [ 'query' => $wp_query, 'template_args' => $template_args ] );

            wp_reset_postdata();

            ?>
        </div>
        
        <?php
        return ob_get_clean();
    }


    // TODO: maybe use \ctdl\pro\class_attr instead
    protected static function get_classes( $attributes ) {
        $classes = [];
        $classes[] = "layout-".$attributes[ 'layout' ];
        return $classes;
    }
}
