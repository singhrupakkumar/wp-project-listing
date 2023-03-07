<?php

	$titleText = single_term_title('', false);
	$description = term_description();
	$qo = get_queried_object();
	$taxonomy = $qo->taxonomy;
	$term_id = $qo->term_id;
	$icon = '';
	$iconHtml = '';
	if( $taxonomy == 'citadela-item-category' ){
		$mainText = esc_html__('Category: ', 'citatela-directory');
		$term_meta = get_term_meta( $term_id, $taxonomy.'-meta', true );
		$icon = (isset($term_meta['category_icon']) && $term_meta['category_icon'] != '') ? $term_meta['category_icon'] : '';
		$color = (isset($term_meta['category_color']) && $term_meta['category_color'] != '') ? $term_meta['category_color'] : '';
		
		$bgStyles = $color ? 'style="background-color: '. esc_attr( $color ).';"' : '';
		$iconStyles = $color ? 'style="color: '.$color.'; border-color: '. esc_attr( $color ) .';"' : '';

		if($icon){
			$iconHtml .= '<div class="entry-icon">';
			$iconHtml .= 	'<span class="entry-icon-wrap">';
			$iconHtml .=		'<span class="icon-bg" '. $bgStyles.'></span>';
			$iconHtml .=		'<i class="'. esc_attr( $icon ).'" '. $iconStyles.'></i>';
			$iconHtml .=	'</span>';
			$iconHtml .= '</div>';
		}

	}elseif ( $taxonomy == 'citadela-item-location' ) {
		$mainText = esc_html__('Location: ', 'citatela-directory');

		$iconHtml = '<div class="entry-icon">';
		$iconHtml .= 	'<span class="entry-icon-wrap">';
		$iconHtml .=		'<span class="icon-bg"></span>';
		$iconHtml .=		'<i class="fas fa-map-marker-alt"></i>';
		$iconHtml .=	'</span>';
		$iconHtml .= '</div>';
		
	}
	$mainData = single_term_title('', false);
	$title = '<span class="main-text">' . esc_html( $mainText ) . '</span>';
	$title .= '<span class="main-data">' . esc_html( $mainData ) . '</span>';

	$allowedHtml = array(
		'a' => array(
				'href' => array(),
        		'title' => array(),
        		'target' => array(),
        		'follow' => array()
        	),
		'br' => array(),
		'em' => array(),
		'strong' => array(),
		'i' => array(),
	);
?>

<div class="page-title standard">
	<header class="entry-header">
		<div class="entry-header-wrap">
			
			<?php echo $iconHtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			
			<h1 class="entry-title"><?php echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
			
			<?php if( $description ) : ?>
				<div class="entry-subtitle">
					<p class="ctdl-subtitle">
						<?php echo wp_kses($description, $allowedHtml); ?>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</header>
</div>
