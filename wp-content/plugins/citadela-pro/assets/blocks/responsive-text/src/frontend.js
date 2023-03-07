jQuery(document).ready(function(){
	"use strict";
	setResponsiveTextData();
	checkResponsiveTextWidth();
});

jQuery(window).resize(function(){
	checkResponsiveTextWidth();
});

function checkResponsiveTextWidth () {
	
	const width = window.innerWidth;
	
	jQuery('.citadela-block-responsive-text.responsive-options').each(function(){
		const $block = jQuery( this );
		const $tag = $block.find('.inner-tag');
		$tag.trigger( 'screen-resized', [ width ] );
	});
}

function setResponsiveTextData(){
	jQuery('.citadela-block-responsive-text.responsive-options').each(function(){
		const $block = jQuery( this );
		const $tag = $block.find('.inner-tag');
		
		const attrs = $block.data( 'block-mobile-attr' );   		
		const breakpoint = $block.data( 'block-mobile-breakpoint' );

		$tag.on( 'screen-resized', function( event, width ){
			$screen = 'desktop';
			if( width < breakpoint ) $screen = 'mobile';

			if( attrs[$screen]['fontSize'] ) {
				$tag.css( 'fontSize', attrs[$screen]['fontSize'] );
			}else{
				$tag.css( 'fontSize', "" );
			}
			
			if( attrs[$screen]['lineHeight'] ) {
				$tag.css( 'lineHeight', attrs[$screen]['lineHeight'] );
			}else{
				$tag.css( 'lineHeight', "" );
			}

			if( $screen == 'desktop' && attrs['desktop']['align'] ) {
				$tag.parent().removeClass(`align-${attrs['mobile']['align']}`).addClass(`align-${attrs['desktop']['align']}`);
			}else if( $screen == 'mobile' && attrs['mobile']['align'] ) {
				$tag.parent().removeClass(`align-${attrs['desktop']['align']}`).addClass(`align-${attrs['mobile']['align']}`);
			}
		});
		
		$block.removeClass( "loading" );
		
	});
}
