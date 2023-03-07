<?php

namespace Citadela\Directory\Blocks;

class ItemContactForm extends Block {

    protected static $slug = 'item-contact-form';

    function __construct() {
        parent::__construct();
    }


    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        global $post;
        if(!isset($post)) return '';

        $blockTitle = $attributes['title'];

        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        
        $meta = \CitadelaDirectoryFunctions::getItemMeta($post->ID);
        $pluginOptions = \CitadelaDirectory::getInstance()->pluginOptions;
        $dir = dirname( __FILE__ );
        wp_register_script(
            "citadela-item-contact-form-submit",
            plugins_url( 'src/form-submit.js', __FILE__ ),
            array(),
            filemtime( "$dir/src/form-submit.js" )
        );
        wp_enqueue_script("citadela-item-contact-form-submit");

        ob_start();
        include dirname( __FILE__ ) . "/../../plugin/cpt/item/templates/parts/single-item-contact-form.php";
        return ob_get_clean();
    }

}