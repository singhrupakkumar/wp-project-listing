jQuery(document).ready(function(){
	"use strict";
	setClusterData();
	checkClusterWidth();
});

jQuery(window).resize(function(){
	checkClusterWidth();
});

function checkClusterWidth () {
	const width = window.innerWidth;
	jQuery('.citadela-block-custom-container.responsive-options').each(function(){
		const $cluster = jQuery( this );
		$cluster.trigger( 'screen-resized', [ width ] );
	});
}

function setClusterData(){
	jQuery('.citadela-block-custom-container.responsive-options').each(function(){
		const $cluster = jQuery( this );
		const $bgImage = $cluster.children('.bg-image-wrapper');
		// image background was moved to separated div, check to keep backward compatibility
		const $imageCssHolder = $bgImage.length ? $bgImage : $cluster;
		const sizeClasses = [ 'cover', 'full-horizontal', 'full-vertical', 'auto' ];
		var sizeClass = '';
		jQuery.each( sizeClasses, function( index, value ){
			if( $cluster.hasClass( `bg-size-${value}` ) ) sizeClass = `bg-size-${value}`;
		});
    
        const desktopAttrs = {
            backgroundImage:  	$imageCssHolder.css( 'backgroundImage' ),
            size: 				sizeClass,
            fixed: 				$cluster.hasClass( 'fixed-bg' ) ? true : false,
            position:			$imageCssHolder.css( 'backgroundPosition' ),
			repeat:				$imageCssHolder.css( 'backgroundRepeat' ),
			radius:				$imageCssHolder.css( 'borderRadius' ),
        }

		// if mobile attrs were not defined, are same as desktop settings
		const 	mobileAttrs = $cluster.data( 'block-attr' );   		
				mobileAttrs.backgroundImage = mobileAttrs.backgroundImage === undefined ? desktopAttrs.backgroundImage : mobileAttrs.backgroundImage; 
				mobileAttrs.size = mobileAttrs.size === undefined ? desktopAttrs.size : mobileAttrs.size; 
				mobileAttrs.fixed = mobileAttrs.fixed === undefined ? desktopAttrs.fixed : mobileAttrs.fixed; 
				mobileAttrs.position = mobileAttrs.position === undefined ? desktopAttrs.position : mobileAttrs.position; 
				mobileAttrs.repeat = desktopAttrs.repeat;
				mobileAttrs.radius = desktopAttrs.radius;

		const breakpoint = $cluster.data( 'block-mobile-breakpoint' );

		$cluster.on( 'screen-resized', function( event, width ){
			if( width < breakpoint ){
				//mobile
				if( mobileAttrs.disableBackgroundImage ){
					$imageCssHolder.css( 'backgroundImage', '' );
					$imageCssHolder.css( 'backgroundPosition', '' );
					$imageCssHolder.css( 'backgroundRepeat', '' );
					$imageCssHolder.css( 'backgroundRadius', '' );
					if( $imageCssHolder.hasClass('bg-image-wrapper') ){
						$imageCssHolder.hide();
					}
					
				}else{
					$imageCssHolder.css( 'backgroundImage', mobileAttrs.backgroundImage );
					$cluster.removeClass( desktopAttrs.size ).addClass( mobileAttrs.size );
					$imageCssHolder.css( 'backgroundPosition', mobileAttrs.position );
					$imageCssHolder.css( 'backgroundRepeat', mobileAttrs.repeat );
					$imageCssHolder.css( 'backgroundRadius', mobileAttrs.radius );
					
					if( mobileAttrs.fixed ){
						$cluster.addClass( 'fixed-bg' );
						$imageCssHolder.css( 'backgroundPosition', '' );
					}else{
						$cluster.removeClass( 'fixed-bg' );
					}
				}
			} else {
				//desktop
				if( $imageCssHolder.hasClass('bg-image-wrapper') ){
					$imageCssHolder.show();
				}
				$imageCssHolder.css( 'backgroundImage', desktopAttrs.backgroundImage );
				$cluster.removeClass( mobileAttrs.size ).addClass( desktopAttrs.size );
				$imageCssHolder.css( 'backgroundPosition', desktopAttrs.position );
				$imageCssHolder.css( 'backgroundRepeat', desktopAttrs.repeat );
				$imageCssHolder.css( 'backgroundRadius', desktopAttrs.radius );

				if( desktopAttrs.fixed ){
					$cluster.addClass( 'fixed-bg' );
					$imageCssHolder.css( 'backgroundPosition', '' );
				}else{
					$cluster.removeClass( 'fixed-bg' );
				}
			}
		});

		//show background
		$cluster.removeClass( "loading-image" );
	});

	
}
