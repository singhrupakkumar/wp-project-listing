<?php
get_header();
?>

 	<?php get_template_part('template-parts/page_title'); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
		<?php
		if ( have_posts() ) :

			while ( have_posts() ) :

				the_post();
				get_template_part( 'template-parts/content', get_post_type() );

			endwhile;

			Citadela_Theme::get_instance()->render_posts_pagination();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
	if ( is_active_sidebar( 'blog-sidebar' ) ) :
?>
	<aside id="secondary" class="blog-widget-area widget-area right-widget-area">
		<div class="widget-area-wrap">
			<?php dynamic_sidebar( 'blog-sidebar' ); ?>
		</div>
	</aside>
<?php
	endif;
?>

<?php
get_footer();
