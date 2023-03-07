<?php

namespace Citadela\Directory\Blocks;

class ItemContactDetails extends Block {

    protected static $slug = 'item-contact-details';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        global $post;
        if(!isset($post)) return '';

        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        
        $meta = \CitadelaDirectoryFunctions::getItemMeta($post->ID);
        $pluginOptions = \CitadelaDirectory::getInstance()->pluginOptions;
        $item_detail_options = get_option('citadela_directory_item_detail');
        $blockTitle = $attributes['title'];

        ob_start();
        include dirname( __FILE__ ) . "/../../plugin/cpt/item/templates/parts/single-item-contact-details.php";
        return ob_get_clean();
    }

}