jQuery(document).ready(function(){
	"use strict";
	setDirectoryItemsListImageData();
	checkDirectoryItemsListWidth();
});

jQuery(window).resize(function(){
	checkDirectoryItemsListWidth();
});

function checkDirectoryItemsListWidth () {
	
	const width = window.innerWidth;
	
	jQuery('.ctdl-directory-items-list.responsive-options').each(function(){
		const $block = jQuery( this );
		$block.trigger( 'screen-resized', [ width ] );
	});
}

function setDirectoryItemsListImageData(){
	jQuery('.ctdl-directory-items-list.responsive-options').each(function(){
		const $block = jQuery( this );
		const attrs = $block.data( 'block-mobile-attr' );   		
		const breakpoint = $block.data( 'block-mobile-breakpoint' );

		$block.on( 'screen-resized', function( event, width ){
			$screen = 'desktop';
			if( width < breakpoint ) $screen = 'mobile';

			

			if( attrs[$screen]['proportionalImageHeight'] ) {
				$block.addClass('proportional-image-height');
				$block.removeClass('default-image-height');
				$block.removeClass('custom-image-height');
				//remove image object position class
				if( attrs['desktop']['imageObjectPosition'] ){
					$block.removeClass(attrs['desktop']['imageObjectPosition']);
				}
				if( attrs['mobile']['imageObjectPosition'] ){
					$block.removeClass(attrs['mobile']['imageObjectPosition']);
				}
			}else{
				$block.removeClass('proportional-image-height');
			}
			
			if( ! attrs[$screen]['proportionalImageHeight'] ) {
				if( attrs[$screen]['imageHeightType'] == 'custom' ){
					$block.addClass('custom-image-height');
					$block.removeClass('default-image-height');
				}
				if( attrs[$screen]['imageHeightType'] == 'default' ){
					$block.addClass('default-image-height');
					$block.removeClass('custom-image-height');
				}
			}

			if( attrs[$screen]['imageHeight'] && ! attrs[$screen]['proportionalImageHeight'] && attrs[$screen]['imageHeightType'] == 'custom' ) {
				$block.find('.item-content').find('img.item-image').css( 'height', attrs[$screen]['imageHeight'] );
			}else{
				$block.find('.item-content').find('img.item-image').css( 'height', "" );
			}

			if( attrs[$screen]['imageObjectPosition'] ) {
				if( $screen == 'desktop'){
					$block.addClass(attrs['desktop']['imageObjectPosition']);
					$block.removeClass(attrs['mobile']['imageObjectPosition']);
				}
				if( $screen == 'mobile'){
					$block.addClass(attrs['mobile']['imageObjectPosition']);
					$block.removeClass(attrs['desktop']['imageObjectPosition']);
				}
				
			}

		});
		
		$block.removeClass( "loading-content" );
		
	});
}
