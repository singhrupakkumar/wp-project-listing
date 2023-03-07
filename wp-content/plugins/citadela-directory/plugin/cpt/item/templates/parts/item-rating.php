<?php 
	$percentage_rating = $rating_data['rating'] * 20;
	$ratings_count = $rating_data['total_count'];
	$color_style = $rating_data['rating_stars_color'] ? "color: {$rating_data['rating_stars_color']};" : '';
	$rating_class = [];
	if( $rating_data['rating_stars_color'] ) $rating_class[] = 'custom-color';

?>

<div class="item-rating-wrapper">
		<div class="rating-stars <?php echo esc_attr( implode(' ', $rating_class) ); ?>" <?php if( $color_style ) echo 'style="'. esc_attr( $color_style ) . '"'; ?>>
			<span class="rating-stars-value" style="width:<?php echo esc_attr( $percentage_rating ); ?>%; <?php echo esc_attr( $color_style ); ?>"></span>
		</div>
		<?php if( $show_reviews_count ) : ?>
			<a href="#item-reviews" class="citadela-reviews-link" rel="nofollow">(<span class="count"><?php /* translators: %s: reviews count. */ printf( _n( '%s review', '%s reviews', esc_html( $ratings_count ), 'citadela-directory' ), esc_html( $ratings_count ) ); ?></span>)</a>
		<?php endif; ?>
</div>
