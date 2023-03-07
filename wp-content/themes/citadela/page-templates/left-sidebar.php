<?php
/**
 * Template Name: Left sidebar page
 *
 */

get_header();
?>
	<?php get_template_part('template-parts/page_title'); ?>

<?php
	$sidebar_name = is_front_page() ? 'home-sidebar' : 'pages-sidebar';
	if ( is_active_sidebar( $sidebar_name ) ) :
?>
	<aside id="secondary" class="pages-widget-area widget-area left-widget-area">
		<div class="widget-area-wrap">
			<?php dynamic_sidebar( $sidebar_name ); ?>
		</div>
	</aside>
<?php
	endif;
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				if ( ! ( function_exists('tribe_is_event_query') && tribe_is_event_query() && !tribe_get_option( 'showComments', false ) ) ) :
					comments_template();
				endif;
			endif;

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
