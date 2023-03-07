<?php
/**
 * The template for displaying posts search results page
 *
 */

get_header();

$special_page_id = CitadelaDirectoryLayouts::getSpecialPageId('posts-search-results');

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
get_footer();
