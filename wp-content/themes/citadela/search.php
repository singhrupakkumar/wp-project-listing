<?php
/**
 * The template for displaying search results pages
 *
 */

get_header();
?>
	<?php get_template_part('template-parts/page_title'); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

		<?php if ( have_posts() ) : ?>

			<?php

			while ( have_posts() ) :

				the_post();
				get_template_part( 'template-parts/content', 'search' );

			endwhile;

			Citadela_Theme::get_instance()->render_posts_pagination();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
	if ( is_active_sidebar( 'search-sidebar' ) ) :
?>
	<aside id="secondary" class="search-widget-area widget-area right-widget-area">
		<div class="widget-area-wrap">
			<?php dynamic_sidebar( 'search-sidebar' ); ?>
		</div>
	</aside>
<?php
	endif;
?>

<?php
get_footer();
