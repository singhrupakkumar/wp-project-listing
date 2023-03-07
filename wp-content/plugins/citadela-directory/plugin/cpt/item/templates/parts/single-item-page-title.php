<?php
	$header_classes = [];
	if( $item_reviews_enabled && $item_rating ){
		$header_classes[] = 'has-rating';
	}
	if( $meta->subtitle ){
		$header_classes[] = 'has-subtitle';	
	}
?>
<div class="page-title standard">
	<header class="entry-header <?php echo esc_attr( implode( ' ', $header_classes ) ); ?>">
		<div class="entry-header-wrap">
			
			<h1 class="entry-title"><?php echo esc_html( $post->post_title ); ?></h1>

			<?php if( $meta->subtitle ): ?>
				<div class="entry-subtitle">
					<p class="ctdl-subtitle"><?php echo wp_kses_data( $meta->subtitle ); ?></p>
				</div>
			<?php endif; ?>	

			<?php 
			if( $item_reviews_enabled && $item_rating ){
				?>
				<div class="entry-item-rating"><?php echo Citadela\Directory\ItemReviews::render_post_rating( $post->ID ); ?></div>
				<?php
			}
			?>

		</div>
	</header>
</div>
