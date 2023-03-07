<?php

namespace Citadela\Directory\Blocks;

class AuthorDetail extends Block {

    protected static $slug = 'author-detail';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() || ! is_author() ) {
            return;
        }
        
        $args = [
            'showCover' => $attributes['showCover'],
            'showIcon' => $attributes['showIcon'],
            'showDescription' => $attributes['showDescription'],
            'showPostsNumber' => $attributes['showPostsNumber'],
        ];

        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        if( $args['showCover'] ) $classes[] = "show-author-cover";
        if( $args['showIcon'] ) $classes[] = "show-author-icon";
        if( $args['showDescription'] ) $classes[] = "show-author-description";
        if( $args['showPostsNumber'] ) $classes[] = "show-posts-number";

        $user = get_user_by( 'id', get_query_var( 'author' ) );

        ob_start();
        ?>

        <div class="wp-block-citadela-blocks ctdl-author-detail <?php echo esc_attr( implode( " ", $classes ) );?>">
            <?php include dirname( __FILE__ ) . '/../../plugin/parts/author-detail.php'; ?>
        </div>
        <?php

        return ob_get_clean();
    }

}