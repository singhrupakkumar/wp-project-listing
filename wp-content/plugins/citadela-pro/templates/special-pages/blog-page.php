<?php

get_header();

$blog_page_id = get_option('page_for_posts');
$special_page_id = \Citadela\Pro\Special_Pages\Page::id( 'blog' );
$image_url = get_the_post_thumbnail( $blog_page_id, 'large' );

?>
	<?php do_action( 'ctdl_page_title' ); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
            <article id="post-<?php echo $special_page_id; ?>" class="special-page">
                <?php if( $image_url ) : ?>
                
                    <div class="post-thumbnail">
                        <a href="<?php echo esc_url( get_the_post_thumbnail_url( $blog_page_id ) ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment">
                            <?php echo esc_url( $image_url ) ?>
                        </a>
                    </div>
                
                <?php endif; ?>

                <div class="entry-content">
                    <?php do_action('ctdl_pro_special_page_content'); ?>
                </div>
            </article>
		</main>
    </div>

    <?php if ( 
            ( ! defined('CITADELA_DIRECTORY_PLUGIN') && is_active_sidebar( 'blog-sidebar' ) )
            || ( defined('CITADELA_DIRECTORY_PLUGIN')  && 'half-layout-template' !== get_post_meta( $special_page_id, '_wp_page_template', true ) ) 
        ) : ?>
        <aside id="secondary" class="blog-widget-area widget-area right-widget-area">
            <div class="widget-area-wrap">
                <?php dynamic_sidebar( 'blog-sidebar' ); ?>
            </div>
        </aside>
    <?php endif ?>

<?php
get_footer();
