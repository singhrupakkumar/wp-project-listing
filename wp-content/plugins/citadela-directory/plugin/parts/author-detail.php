<?php

/* required template variables should be defined before including this file */
// $user defined before including this file
$template_args = isset($args) ? $args : [];

$cover_image_id = get_the_author_meta( 'citadela_cover_image', $user->ID );
$cover_image_url = '';
if( $cover_image_id ){
    $src = wp_get_attachment_image_src( $cover_image_id, 'full' );
    if( $src ){
        $cover_image_url = $src[0];
        $cover_image_width = $src[1];
        $cover_image_height = $src[2];
        $cover_image_srcset = wp_get_attachment_image_srcset( $cover_image_id, 'full' );
        $cover_image_sizes = wp_get_attachment_image_sizes( $cover_image_id, 'full' );
        $cover_image_alt = trim( strip_tags( get_post_meta( $cover_image_id, '_wp_attachment_image_alt', true ) ) );
        $cover_image_alt = $cover_image_alt ? $cover_image_alt : $user->data->display_name;
    }
}
$icon = get_avatar($user);
$description = get_the_author_meta( 'description', $user->ID );
$posts_count = count_user_posts( $user->ID, 'post', true );

$classes = [];
if( $template_args['showCover'] && $cover_image_id && $src ) $classes[] = 'has-cover';
if( $template_args['showIcon'] && $icon ) $classes[] = 'has-icon';
if( $template_args['showDescription'] && $description ) $classes[] = 'has-description';
if( $template_args['showPostsNumber'] && $posts_count ) $classes[] = 'has-posts';

?>

<article class="citadela-author-detail <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
    <div class="item-content">
        <div class="item-thumbnail">
            
            <?php if( $template_args['showCover'] && $cover_image_id && $src ) : ?>
                <div class="author-cover">
                    <img 
                        src="<?php echo esc_url( $cover_image_url ); ?>"
                        width="<?php esc_attr_e( $cover_image_width ); ?>"
                        height="<?php esc_attr_e( $cover_image_height ); ?>"
                        srcset="<?php esc_attr_e( $cover_image_srcset ); ?>"
                        sizes="<?php esc_attr_e( $cover_image_sizes ); ?>"
                        alt="<?php echo esc_html( $cover_image_alt ); ?>"
                    />
                </div>
            <?php endif; ?>

            <?php if( $template_args['showPostsNumber'] && $posts_count ) : ?>
                <div class="author-posts-number">
                    <span class="posts-number"><?php esc_html_e( $posts_count ); ?></span>
                    <span class="posts-text"><?php echo _n( 'post', 'posts', $posts_count, 'citadela-directory' ); ?></span>
                </div>
            <?php endif; ?>
            
            <div class="author-info">
                <?php if( $template_args['showIcon'] && $icon ) : ?>
                    <div class="author-icon"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                <?php endif; ?>
                <div class="author-name"><?php esc_html_e( $user->data->display_name ); ?></div>
            </div>

        </div>
        <?php if( $template_args['showDescription'] && $description ) : ?>
            <div class="item-body">
                <div class="item-description"><?php echo wp_kses_post( $description ); ?></div>
            </div>
        <?php endif; ?>
    </div>
</article>