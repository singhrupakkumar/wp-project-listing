<?php

get_header();
?>

	<?php get_template_part('template-parts/page_title'); ?>

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

		endwhile;
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
