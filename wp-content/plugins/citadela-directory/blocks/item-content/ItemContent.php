<?php

namespace Citadela\Directory\Blocks;

class ItemContent extends Block {

    protected static $slug = 'item-content';

    public static function renderCallback($attributes, $content ) {
        if ( is_admin() ) {
            return;
        }
        
        global $post;
        
        if( ! $post || $post->post_type !== 'citadela-item' || $post->post_content == '' ){
            return;
        }
        $plugin = \CitadelaDirectory::getInstance();
        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 

        $allowed_editor = $plugin->ItemPageLayout_instance->allowed_editor;
        $post_content = str_replace( ']]>', ']]&gt;', $post->post_content );
        ob_start();
        ?>
        <div class="wp-block-citadela-blocks ctdl-item-content <?php echo esc_attr( implode( " ", $classes ) );?>">
            <div class="item-content">
                <?php 
                    echo has_blocks($post) ? apply_filters( 'the_content', $post_content ) : wpautop( apply_filters( 'the_content', $post_content ) ) ;
                ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

}