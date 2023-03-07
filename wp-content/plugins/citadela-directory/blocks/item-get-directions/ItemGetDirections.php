<?php

namespace Citadela\Directory\Blocks;

class ItemGetDirections extends Block {

    protected static $slug = 'item-get-directions';

    function __construct() {
        parent::__construct();
    }

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }
        
        global $post;
        if(!isset($post)) return '';
        
        $lat = get_post_meta( $post->ID, '_citadela_latitude', true );
        $long = get_post_meta( $post->ID, '_citadela_longitude', true );

        if( $lat == '0' && $long == '0' ) return '';
        
        $url = "https://www.google.com/maps/dir/?api=1&destination={$lat},{$long}";

        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $classes[] = "align-{$attributes['align']}";
        $classes[] = "{$attributes['style']}-style";

        if( $attributes['textColor'] ) $classes[] = "custom-text-color";
        if( $attributes['style'] != 'text' && $attributes['bgColor'] ) $classes[] = "custom-background-color";
        if( $attributes['style'] != 'text' && isset( $attributes['borderRadius'] ) ) $classes[] = "custom-border-radius";
        
        $styles = $attributes['textColor'] ? "color: {$attributes['textColor']};" : "";
        if( $attributes['style'] != 'text' ){
            if( $attributes['bgColor'] ) $styles .= "background-color: {$attributes['bgColor']};";
            if( isset( $attributes['borderRadius'] ) ) $styles .= "border-radius: {$attributes['borderRadius']}px;";
        }


        $buttonText = $attributes['text'] !== '' ? $attributes['text'] : esc_html__('Get directions', 'citadela-directory');
        
        ob_start();
        ?>
        <div class="wp-block-citadela-blocks ctdl-item-get-directions <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
            <div class="button-wrapper">
                <a href="<?php echo esc_url($url); ?>" class="button-text" style="<?php echo esc_attr($styles); ?>" target="_blank"><?php echo wp_kses_data( $buttonText ); ?></a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

}