<?php

get_header();
?>

 	<?php get_template_part('template-parts/page_title'); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );

			the_post_navigation();

			// If comments are open or we have at least one comment, load up the comment template.
			if ( !is_attachment() ) :

				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endif;

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
	if ( is_active_sidebar( 'posts-sidebar' ) ) :
?>
	<aside id="secondary" class="posts-widget-area widget-area right-widget-area">
		<div class="widget-area-wrap">
			<?php dynamic_sidebar( 'posts-sidebar' ); ?>
		</div>
	</aside>
<?php
	endif;
?>

<?php
//get_sidebar('posts');
get_footer();
