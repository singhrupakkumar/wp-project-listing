<?php

$galleryImages = array();

if($featuredImageId && $featuredImage){
	$imageData = array(
					'alt'	=> $post->post_title,
					'url' 	=> $featuredImage[0],
					'width' => $featuredImage[1],
					'height'=> $featuredImage[2],
					);
	array_push($galleryImages, $imageData  );
}

?>

<?php if( !empty($galleryImages)): ?>

	<div class="item-gallery citadelaFancyboxGallery">
	
	<?php foreach ($galleryImages as $image) { ?>
		<a href="<?php echo esc_url( $image['url'] ); ?>" class="citadelaFancyboxElement" data-gallery="item-gallery" data-image-size="<?php echo esc_attr( $image['width'].'x'.$image['height'] ); ?>">
			<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_html( $image['alt'] ); ?>" >
		</a>

	<?php } ?>
	</div>

<?php endif; ?>
