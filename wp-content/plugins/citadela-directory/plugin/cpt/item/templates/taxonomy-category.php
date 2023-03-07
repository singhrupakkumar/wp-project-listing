<?php

	get_header();

	$qo = get_queried_object();
	$plugin = CitadelaDirectory::getInstance();
    $pageType = 'citadela-item-taxonomy';

    $special_page_id = CitadelaDirectoryLayouts::getSpecialPageId('item-category');
	$hideTitle = get_post_meta( $special_page_id, '_citadela_hide_page_title', true );
?>

    <?php
        if(!$hideTitle){
            include dirname( __FILE__ ) . '/parts/taxonomy-page-title.php';
        }
	?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<article id="post-<?php echo esc_attr( $special_page_id ); ?>" class="special-page">
	            <div class="entry-content">
		            <?php do_action('ctdl_special_page_content'); ?>
		        </div>
	        </article>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
	if ( is_active_sidebar( 'item-category-sidebar' ) && 'half-layout-template' !== get_post_meta( $special_page_id, '_wp_page_template', true ) ) :
?>
	<aside id="secondary" class="item-category-widget-area widget-area right-widget-area">
		<div class="widget-area-wrap">
			<?php dynamic_sidebar( 'item-category-sidebar' ); ?>
		</div>
	</aside>
<?php
	endif;
?>

<?php
get_footer();
