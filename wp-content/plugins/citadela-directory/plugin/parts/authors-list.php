<?php

/* required template variables should be defined before including this file */
$query = isset($query) ? $query : null;
$users = $query ? $query->get_results() : [];
$template_args = isset($args) ? $args : [];

foreach ($users as $user) {
    
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
    $url = get_author_posts_url( $user->ID );

    $classes = [];
    if( $template_args['showCover'] && $cover_image_id && $src ) $classes[] = 'has-cover';
    if( $template_args['showIcon'] && $icon ) $classes[] = 'has-icon';
    if( $template_args['showDescription'] && $description ) $classes[] = 'has-description';
    if( $template_args['showPostsNumber'] && $posts_count ) $classes[] = 'has-posts';
    if( $template_args['useCarousel'] ) $classes[] = 'swiper-slide';
    
    //styles
    $borderColorStyle = "";
    $backgroundColorStyle = "";
    $textColorStyle = "";
    $decorColorStyle = "";
    $decorBackgroundColorStyle = "";
    $postsNumberColorStyle = "";
    
    if ( $activeProPlugin ) {
        $decorColorStyle = isset( $args['decorColor'] ) && $args['decorColor'] ? "color: " . esc_attr( $args['decorColor'] ) . ";" : "";
        $postsNumberColorStyle = isset( $args['postsNumberColor'] ) && $args['postsNumberColor'] ? "color: " . esc_attr( $args['postsNumberColor'] ) . ";" : "";
        $decorBackgroundColorStyle = isset( $args['decorColor'] ) && $args['decorColor'] ? "background-color: " . esc_attr( $args['decorColor'] ) . ";" : "";
        $textColorStyle = isset( $args['textColor'] ) && $args['textColor'] ? "color: " . esc_attr( $args['textColor'] ) . ";" : "";
        $backgroundColorStyle = isset( $args['backgroundColor'] ) && $args['backgroundColor'] ? "background-color: " . esc_attr( $args['backgroundColor'] ) . ";" : "";
        $borderColorStyle = isset( $args['borderColor'] ) && $args['borderColor'] ? "border-color: " . esc_attr( $args['borderColor'] ) . ";" : "";
    }

    $articleStyles = 'style="' . implode('', [ $textColorStyle ] ) . '"';
    $itemContentStyles = 'style="' . implode('', [ $backgroundColorStyle, $borderColorStyle ] ) . '"';
    $itemPostsLinkStyle = 'style="' . implode('', [ $decorColorStyle ] ) . '"';
    $authorPostsNumberStyle = 'style="' . implode('', [ $postsNumberColorStyle, $decorBackgroundColorStyle ] ) . '"';


    ?>

    <article class="citadela-author-item <?php echo esc_attr( implode( ' ', $classes ) ); ?>" <?php echo $articleStyles; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
        <div class="item-content" <?php echo $itemContentStyles; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
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
                <?php if( $template_args['showIcon'] && $icon ) : ?>
                    <div class="author-icon"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                <?php endif; ?>
                <?php if( $template_args['showPostsNumber'] && $posts_count ) : ?>
                    <div class="author-posts-number" <?php echo $authorPostsNumberStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                        <span class="posts-number"><?php esc_html_e( $posts_count ); ?></span>
                        <span class="posts-text"><?php echo _n( 'post', 'posts', $posts_count, 'citadela-directory' ); ?></span>
                    </div>
                    <?php endif; ?>
            </div>
            <div class="item-body">
                
                <div class="item-title"><?php esc_html_e( $user->data->display_name ); ?></div>
                
                <?php if( $template_args['showDescription'] && $description ) : ?>
                    <div class="item-description"><?php echo wp_kses_post( $description ); ?></div>
                <?php endif; ?>

                <?php if( $template_args['showLink'] ) : ?>
                    <div class="item-posts-link" <?php echo $itemPostsLinkStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( $template_args['linkText'] ? $template_args['linkText'] : __( 'View posts', 'citadela-directory' ) ); ?></a></div>
                <?php endif; ?>
            </div>
        </div>
    </article>

    <?php
}