jQuery(document).ready(function(){
	"use strict";
	setItemFeaturedImageData();
	checkItemFeaturedImageTextWidth();
});

jQuery(window).resize(function(){
	checkItemFeaturedImageTextWidth();
});

function checkItemFeaturedImageTextWidth () {
	
	const width = window.innerWidth;
	
	jQuery('.ctdl-item-featured-image.responsive-options').each(function(){
		const $block = jQuery( this );
		const $tag = $block.find('.ft-image img');
		$tag.trigger( 'screen-resized', [ width ] );
	});
}

function setItemFeaturedImageData(){
	jQuery('.ctdl-item-featured-image.responsive-options').each(function(){
		const $block = jQuery( this );
		const $tag = $block.find('.ft-image img');
		
		const attrs = $block.data( 'block-mobile-attr' );   		
		const breakpoint = $block.data( 'block-mobile-breakpoint' );

		$tag.on( 'screen-resized', function( event, width ){
			$screen = 'desktop';
			if( width < breakpoint ) $screen = 'mobile';

			/*if( attrs[$screen]['objectPosition'] ){
				$tag.parents('.ctdl-item-featured-image').addClass( attrs[$screen]['objectPosition'] );
			}*/
			if( attrs[$screen]['minHeight'] ) {
				$tag.parents('.ctdl-item-featured-image').css( 'min-height', attrs[$screen]['minHeight'] );
			}else{
				$tag.parents('.ctdl-item-featured-image').css( 'min-height', "" );
			}


			if( attrs[$screen]['height'] ) {
				$tag.css( 'height', attrs[$screen]['height'] );
				$tag.parents('.ctdl-item-featured-image').addClass('custom-height');
			}else{
				$tag.css( 'height', "" );
				$tag.parents('.ctdl-item-featured-image').removeClass('custom-height');
			}

			if( $screen == 'desktop' && attrs['desktop']['coverHeight'] ) {
				$tag.parents('.ctdl-item-featured-image').addClass('cover-height');
			}else if( $screen == 'mobile' && attrs['mobile']['coverHeight'] ) {
				$tag.parents('.ctdl-item-featured-image').addClass('cover-height');
			}else{
				$tag.parents('.ctdl-item-featured-image').removeClass('cover-height');
			}
		});
		
		$block.removeClass( "loading" );
		
	});
}
