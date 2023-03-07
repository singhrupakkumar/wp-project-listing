jQuery(document).ready(function(){
	"use strict";
	setSpacerData();
	checkSpacerWidth();
});

jQuery(window).resize(function(){
	checkSpacerWidth();
});

function checkSpacerWidth () {
	const slug = 'spacer';
	const screenWidth = window.innerWidth;
	jQuery( `.citadela-block-${slug}.responsive-options` ).each(function(){
		jQuery( this ).trigger( 'screen-resized', [ screenWidth ] );
	});
}

function setSpacerData(){
	const slug = 'spacer';
	jQuery( `.citadela-block-${slug}.responsive-options` ).each(function(){
		const $block = jQuery( this );
		const attrs = $block.data( 'block-attr' );   		
		const breakpoint = $block.data( 'block-mobile-breakpoint' );
		$block.on( 'screen-resized', function( event, screenWidth ){
			screen = 'desktop';
			$tag = jQuery( this ).find( ".inner-holder" );
			if( screenWidth < breakpoint ) screen = 'mobile';
			if( attrs[screen]['negative'] ) {
				$tag.css( 'padding-top', "" );				
				$tag.css( 'margin-top', attrs[screen]['height'] );
			}else{
				$tag.css( 'margin-top', "" );
				$tag.css( 'padding-top', attrs[screen]['height'] );
			}
		});
		
		$block.removeClass( "loading" );
		
	});
}
