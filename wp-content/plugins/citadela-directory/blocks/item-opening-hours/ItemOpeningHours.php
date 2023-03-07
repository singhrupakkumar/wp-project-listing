<?php

namespace Citadela\Directory\Blocks;

class ItemOpeningHours extends Block {

    protected static $slug = 'item-opening-hours';

    protected static $attributes = [
        'title' => [
            'type' => 'string',
            'default' => '',
        ],
    ];

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        global $post;
        if(!isset($post)) return '';
    
        $blockTitle = $attributes['title'];
        
        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 

        $template = '<div class="wp-block-citadela-blocks ctdl-item-opening-hours ' . esc_attr( implode( " ", $classes ) ) .'">';
    
        $meta = \CitadelaDirectoryFunctions::getItemMeta($post->ID);
    
        ob_start();
        include dirname( __FILE__ ) . "/../../plugin/cpt/item/templates/parts/single-item-opening-hours.php";
    
        $template .= ob_get_clean();
    
        $template .= '</div>';
    
        return $template;
    }

}