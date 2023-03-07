<?php

namespace Citadela\Directory\Blocks;

class ItemGpxDownload extends Block {

    protected static $slug = 'item-gpx-download';

    function __construct() {
        parent::__construct();
    }

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }
        
        global $post;
        if(!isset($post)) return '';
        
        $mediaId = get_post_meta( $post->ID, '_citadela_gpx_file_id', true );
        if( ! $mediaId ) return '';
        
        $url = wp_get_attachment_url( intval( $mediaId ) );
        if( ! $url ) return '';

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


        $buttonText = $attributes['text'] !== '' ? $attributes['text'] : esc_html__('Download GPX file', 'citadela-directory');
        
        ob_start();
        ?>
        <div class="wp-block-citadela-blocks ctdl-item-gpx-download <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
            <div class="download-button">
                <a href="<?php echo esc_url($url); ?>" class="button-text" style="<?php echo esc_attr($styles); ?>" download><?php echo wp_kses_data( $buttonText ); ?></a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

}