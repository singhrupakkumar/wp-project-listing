<?php
	$meta = CitadelaDirectoryFunctions::getItemMeta($post->ID);

	$permalink = get_permalink( $post->ID );
	
	$image_size = isset($args['imageSize']) ? $args['imageSize'] : 'citadela_item_thumbnail';
	$image_id = get_post_thumbnail_id( $post );
	$image_data = [];
    $image_data['src'] = wp_get_attachment_image_src( $image_id, $image_size );
    $image_data['url'] = $image_data['fullurl'] = $image_data['src'][0];
    $image_data['image_width'] = $image_data['fullwidth'] = $image_data['src'][1];
    $image_data['image_height'] = $image_data['fullheight'] = $image_data['src'][2];
    $image_data['srcset'] = wp_get_attachment_image_srcset( $image_id, $image_size );
    $image_data['sizes'] = wp_get_attachment_image_sizes( $image_id, $image_size );
    $image_data['alt'] = $post->post_title;

    $imgStylesText = isset( $imgStylesText ) ? $imgStylesText : '';

	//get post categories
	$featured_category = get_post_meta($post->ID, '_citadela_featured_category', true);
	if( isset( $args['onlyFeaturedCategory'] ) && $args['onlyFeaturedCategory'] && $featured_category ){
		$term = get_term( intval($featured_category) );
        $categories = $term ? [ $term ] : [];
	}else{
		$categories = get_the_terms($post->ID, 'citadela-item-category');
		$categories = apply_filters( 'the_category_list', $categories, $post->ID);
	}
	
	//get post locations
	$locations = get_the_terms($post->ID, 'citadela-item-location');
	$locations = apply_filters( 'the_category_list', $locations, $post->ID);
	

	/* prepare classes based on features and meta */
	$isFeatured = $meta->featured ? 'featured' : false;
	$hasThumbnail = get_the_post_thumbnail_url($post->ID, 'large') && $args['showItemFeaturedImage'] 
		? 'has-thumbnail' 
		: false;
	$hasSubtitle = $meta->subtitle && $args['showItemSubtitle']
		? 'has-subtitle' : 
		false;
	$hasAddress = $meta->address && $args['showItemAddress']
		? 'has-address' 
		: false;
	$hasWeb = $meta->web_url && $args['showItemWeb']
		? 'has-web' 
		: false;
	$hasCategories = !empty($categories) && $args['showItemCategories']
		? 'has-categories' 
		: false;
	$hasLocations = !empty($locations) && $args['showItemLocations']
		? 'has-locations' 
		: false;
	$excerpt = has_excerpt() ?
		strip_tags( strip_shortcodes(get_the_excerpt()) ) :
		wp_trim_words( strip_shortcodes(get_the_content()), 50, "..." );
	$hasDescription = $excerpt && $args['showItemDescription'] 
		? 'has-description'
		: false;
	
	$useCarousel = isset( $args['useCarousel'] ) && $args['useCarousel']
		? 'swiper-slide'
		: false;

	if ($hasWeb) {
		$webUrlLabel = $meta->web_url_label ?
			esc_html( $meta->web_url_label ) :
			esc_url( $meta->web_url );
	} else {
		$webUrlLabel = '';
    }

	
	$item_rating = Citadela\Directory\ItemReviews::get_post_rating( $post->ID );
	$show_item_rating = $item_rating && $show_ratings_generally; // $show_ratings_generally defined in block
	$showRating = $show_item_rating
		? 'has-rating'
		: false;
	
	$hasFeatureClasses = implode(' ', array_filter([$isFeatured, $hasThumbnail, $hasSubtitle, $hasDescription, $hasAddress, $hasWeb, $hasCategories, $hasLocations, $useCarousel, $showRating]));


	//styles
	$borderColorStyle = "";
	$backgroundColorStyle = "";
	$textColorStyle = "";
	$decorColorStyle = "";
	$decorBorderColorStyle = "";
	
	if ( $activeProPlugin ) {
		$borderColorStyle = isset( $args['borderColor'] ) && $args['borderColor'] ? "border-color: " . esc_attr( $args['borderColor'] ) . ";" : "";
		$backgroundColorStyle = isset( $args['backgroundColor'] ) && $args['backgroundColor'] ? "background-color: " . esc_attr( $args['backgroundColor'] ) . ";" : "";
		$textColorStyle = isset( $args['textColor'] ) && $args['textColor'] ? "color: " . esc_attr( $args['textColor'] ) . ";" : "";
		$decorColorStyle = isset( $args['decorColor'] ) && $args['decorColor'] ? "color: " . esc_attr( $args['decorColor'] ) . ";" : "";
		$decorBorderColorStyle = isset( $args['decorColor'] ) && $args['decorColor'] ? "border-color: " . esc_attr( $args['decorColor'] ) . ";" : "";
	}
	
	$bordersStyle = "style=\"{$borderColorStyle}\"";
	$itemContentStyle = 'style="' . implode('', [ $borderColorStyle, $backgroundColorStyle, $textColorStyle ] ) . '"';
	$itemThumbnailStyle = 'style="' . implode('', [ $decorColorStyle ] ) . '"';
	$itemSubtitleStyle = 'style="' . implode('', [ $decorColorStyle ] ) . '"';
	$itemLocationValueStyle = 'style="' . implode('', [ $decorColorStyle ] ) . '"';
	$itemWebValueStyle = 'style="' . implode('', [ $decorColorStyle ] ) . '"';
	$itemCategoriesValueStyle = 'style="' . implode('', [ $decorBorderColorStyle ] ) . '"';
?>

<article class="citadela-article <?php echo esc_attr( $hasFeatureClasses ); ?>">

	<div class="item-content" <?php echo $itemContentStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php if($hasThumbnail) : ?>
		<div class="item-thumbnail" <?php echo $itemThumbnailStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<a href="<?php echo esc_url( $permalink ); ?>">
				<img 
					class="item-image"
                    src="<?php echo esc_url( $image_data['url'] ); ?>"
                    width="<?php esc_attr_e( $image_data['image_width'] ); ?>"
                    height="<?php esc_attr_e( $image_data['image_height'] ); ?>"
                    srcset="<?php esc_attr_e( $image_data['srcset'] ); ?>"
                    sizes="<?php esc_attr_e( $image_data['sizes'] ); ?>"
                    alt="<?php echo esc_html( $image_data['alt'] ); ?>"
                    <?php echo "style=\"{$imgStylesText}\""; ?>
                />
			</a>
		</div>
		<?php endif; ?>

		<div class="item-body">
			<div class="item-title">
				<a href="<?php echo esc_url( $permalink ); ?>">
					<div class="item-title-wrap">
						<div class="post-title"><?php echo esc_html( $post->post_title ); ?></div>
						<?php if($hasSubtitle) : ?>
						<div class="post-subtitle" <?php echo $itemSubtitleStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo wp_kses_data( $meta->subtitle ); ?></div>
						<?php endif; ?>
					</div>
				</a>
			</div>
			
			<?php if($show_item_rating) : ?>
				<div class="item-rating">
					<?php 
						echo Citadela\Directory\ItemReviews::render_post_rating( $post->ID, [ 'rating_color' => $args['ratingColor'] ], false ); // false = do not show item ratings number, just rating stars 
					?>

				</div>
			<?php endif; ?>

			<?php if($hasDescription ) : ?>
			<div class="item-text">
				<div class="item-description">
					<p><?php echo esc_html( $excerpt ); ?></p>
					<a href="<?php echo esc_url( $permalink ); ?>" class="more"><?php esc_html_e('View more', 'citadela-directory'); ?></a>
				</div>
			</div>
			<?php endif; ?>
			<div class="item-footer" <?php echo $bordersStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php if( $hasLocations ): ?>
				<div class="item-data location" <?php echo $bordersStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span class="label"><?php esc_html_e('Location', 'citadela-directory'); ?></span>
					<span class="values">
						<?php foreach ($locations as $loc) : ?>
						<a href="<?php echo esc_url( get_term_link($loc) ); ?>" class="value" <?php echo $itemLocationValueStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $loc->name ); ?></a>
						<?php endforeach; ?>
					</span>
				</div>
				<?php endif; ?>
				<?php if($hasAddress) : ?>
				<div class="item-data address" <?php echo $bordersStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span class="label"><?php esc_html_e('Address', 'citadela-directory'); ?></span>
					<span class="values"><?php echo esc_html( $meta->address ); ?></span>
				</div>
				<?php endif; ?>
				<?php if($hasWeb) : ?>
				<div class="item-data web" <?php echo $bordersStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span class="label"><?php esc_html_e('Web', 'citadela-directory'); ?></span>
					<span class="values">
						<a href="<?php echo esc_url( $meta->web_url );?>" class="value" <?php echo $itemWebValueStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $webUrlLabel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					</span>
				</div>
				<?php endif; ?>
				<?php if( $hasCategories ): ?>
				<div class="item-data categories" <?php echo $bordersStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span class="label"><?php esc_html_e('Categories', 'citadela-directory'); ?></span>
					<span class="values">
						<?php foreach ($categories as $cat) : ?>
						<a href="<?php echo esc_url( get_term_link($cat) ); ?>" class="value" <?php echo $itemCategoriesValueStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $cat->name ); ?></a>
						<?php endforeach; ?>
					</span>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

</article>


<?php
