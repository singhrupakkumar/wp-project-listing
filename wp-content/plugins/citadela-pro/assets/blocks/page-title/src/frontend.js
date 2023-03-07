jQuery(document).ready(function(){
	"use strict";
	setPageTitleData();
	checkPageTitleTextWidth();
});

jQuery(window).resize(function(){
	checkPageTitleTextWidth();
});

function checkPageTitleTextWidth () {
	
	const width = window.innerWidth;
	
	jQuery('.citadela-block-page-title.responsive-options').each(function(){
		const $block = jQuery( this );
		const $tag = $block.find('.entry-title');
		$tag.trigger( 'screen-resized', [ width ] );
	});
}

function setPageTitleData(){
	jQuery('.citadela-block-page-title.responsive-options').each(function(){
		const $block = jQuery( this );
		const $tag = $block.find('.entry-title');
		
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
				$tag.parents('.citadela-block-page-title').removeClass(`align-${attrs['mobile']['align']}`).addClass(`align-${attrs['desktop']['align']}`);
			}else if( $screen == 'mobile' && attrs['mobile']['align'] ) {
				$tag.parents('.citadela-block-page-title').removeClass(`align-${attrs['desktop']['align']}`).addClass(`align-${attrs['mobile']['align']}`);
			}
		});
		
		$block.removeClass( "loading" );
		
	});
}
