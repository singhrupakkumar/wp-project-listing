<?php
/**
 * The template for displaying posts category search results page
 *
 */

get_header();

$special_page_id = CitadelaDirectoryLayouts::getSpecialPageId('posts-category');

?>
	<?php do_action( 'ctdl_page_title' ); ?>

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
	if ( is_active_sidebar( 'archives-sidebar' ) && 'half-layout-template' !== get_post_meta( $special_page_id, '_wp_page_template', true ) ) :
?>
	<aside id="secondary" class="archive-widget-area widget-area right-widget-area">
		<div class="widget-area-wrap">
			<?php dynamic_sidebar( 'archives-sidebar' ); ?>
		</div>
	</aside>
<?php
	endif;
?>

<?php
get_footer();
