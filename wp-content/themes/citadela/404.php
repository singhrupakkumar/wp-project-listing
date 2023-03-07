<?php

get_header();
?>

	<?php get_template_part('template-parts/page_title'); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<section class="error-404 not-found">

				<div class="page-content">
					<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'citadela' ); ?></p>

					<?php
						get_search_form();
						the_widget( 'WP_Widget_Pages', array() );
					?>

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
	if ( is_active_sidebar( '404-sidebar' ) ) :
?>
	<aside id="secondary" class="404-widget-area widget-area right-widget-area">
		<div class="widget-area-wrap">
			<?php dynamic_sidebar( '404-sidebar' ); ?>
		</div>
	</aside>
<?php
	endif;
?>

<?php
get_footer();
