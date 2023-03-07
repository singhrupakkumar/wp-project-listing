<?php
	$permalink = get_permalink( $post->ID );
	$featured_image = wp_get_attachment_image(get_post_thumbnail_id( $post->ID ), 'large');

	//get post categories
	$categories = get_the_terms($post->ID, 'category');
	$categories = apply_filters( 'the_category_list', $categories, $post->ID);

    //get post locations
    $locations = get_the_terms($post->ID, 'citadela-post-location');
    $locations = apply_filters( 'the_category_list', $locations, $post->ID);

	/* prepare classes based on features and meta */
	$hasThumbnail = get_the_post_thumbnail_url($post->ID, 'large') && $template_args['showFeaturedImage']
		? 'has-thumbnail'
		: false;
	$hasCategories = !empty($categories) && $template_args['showCategories']
		? 'has-categories'
		: false;
    $hasLocations = defined( 'CITADELA_DIRECTORY_PLUGIN') && !empty($locations) && $template_args['showLocations']
        ? 'has-locations'
        : false;
	$excerpt = has_excerpt() ?
		strip_tags( strip_shortcodes(get_the_excerpt()) ) :
		wp_trim_words( strip_shortcodes(get_the_content()), 50, "..." );
	$hasDescription = $excerpt && $template_args['showDescription']
		? 'has-description'
		: false;

        $hasDate = $template_args['showDate'] ? 'has-date' : false;
    $isSticky = is_sticky($post->ID)
        ? 'sticky'
        : false;
    $hasFeatureClasses = implode(' ', array_filter([$hasThumbnail, $hasDate, $hasDescription, $hasCategories, $hasLocations, $isSticky]));
?>

<article id="post-<?php the_ID(); ?>" class="<?php echo $hasFeatureClasses; ?>" <?php echo $styles['articleStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <header class="entry-header">
        <h2 class="entry-title">
            <?php 
                the_title( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a>' ); 
                if( $isSticky ):
                ?>
                    <span class="featured" <?php echo $styles['stickyStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>></span>
                <?php
                endif;
            ?>
        </h2>
        <div class="entry-meta">
            <?php if( $template_args['showDate'] ) { citadela_theme_posted_on( [ 'entryMetaLinksStyle' => $styles['entryMetaLinksStyle'] ] ); } ?>
            <?php citadela_theme_posted_by( [ 'entryMetaLinksStyle' => $styles['entryMetaLinksStyle'] ] ); ?>
        </div>
        <?php citadela_theme_leave_comment( [ 'commentsLinkStyle' => $styles['commentsLinkStyle'] ] ); ?>
    </header>

    <?php if( $hasThumbnail ) : ?>

	    <?php citadela_theme_post_thumbnail(); ?>

    <?php endif ?>

    <?php if($hasDescription ) : ?>

        <div class="entry-content">
            <?php if( has_excerpt() ) : ?>

                <?php echo strip_tags( strip_shortcodes(get_the_excerpt()) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

            <?php else : ?>

                <?php echo wp_trim_words( strip_shortcodes(get_the_content()), 50, "..." ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

            <?php endif; ?>

            <?php citadela_theme_edit_post_link(); ?>
        </div>

    <?php endif; ?>

    <?php if( $hasCategories || $hasLocations ): ?>

        <footer class="entry-footer">
            <?php 
                if( $hasLocations) citadela_theme_post_locations_list( ' ', [ 'itemDataLocationStyle' => $styles['itemDataLocationStyle'] ] ); 
                if( $hasCategories) citadela_theme_categories_list( ' ', [ 'itemDataCategoryStyle' => $styles['itemDataCategoryStyle'] ] ); 
            ?>
        </footer>

    <?php endif; ?>

</article>