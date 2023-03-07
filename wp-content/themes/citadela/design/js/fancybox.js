jQuery(document).ready(function(){
	"use strict";
	citadelaApplyFancybox();

});



//PHOTOSWIPE fancybox

function citadelaApplyFancybox(){
	var	$a,
		aSelectors = [
			'a:not(.citadelaDisableFancybox)[href*=".jpg"]',
			'a:not(.citadelaDisableFancybox)[href*=".jpeg"]',
			'a:not(.citadelaDisableFancybox)[href*=".png"]',
			'a:not(.citadelaDisableFancybox)[href*=".gif"]'
		],
		gSelectors = [
			'.wp-block-gallery',
			'.entry-content .gallery',
			'.widget_media_gallery .gallery',
			'.widget_text .gallery',
			'.woocommerce-product-gallery'
		],
		g = 0;

	jQuery( aSelectors.join(",") ).each(function(){
		$a = jQuery(this);
		$a.addClass('citadelaFancyboxElement');
		$a.on('click', function(e){
			//do nothing until full image size isn't loaded
			e.preventDefault();
		});
	});

	//identify galleries
	jQuery( gSelectors.join(",") ).each(function(){
		var $gallery = jQuery(this);
		$gallery.addClass('citadelaFancyboxGallery');
		g++;
		$gallery.find( aSelectors.join(",") ).each(function(){
			$a = jQuery(this);
			$a.attr('data-gallery', 'gallery-' + g);
		});

	});

	jQuery('body').append(citadelaGetPswpHtml());

	citadelaLoadOriginalImageSizes();
}

function citadelaOpenFancybox( $clickedLink ){

	var $aClicked = $clickedLink,
		imgIndex = 0,
		items = [],
		$img,
		$a,
		altText;

	$aClicked.addClass('clicked');


	//check if clicked link is part of gallery
	var $gallery = $aClicked.parents('.citadelaFancyboxGallery');
	if( $gallery.length ){
		//part of gallery
		$gallery.find('.citadelaFancyboxElement').each(function(index){
			$a = jQuery(this);
			$img = $a.find('img');
			altText = citadelaGetCaption( $img );

			if( $a.hasClass('clicked') ){
				imgIndex = index;
				$a.removeClass('clicked');
			}
			var size = $a.attr('data-image-size').split("x");
			items.push({
		        src: $a.attr('href'),
		        title: altText,
		        w: size[0],
		        h: size[1],
		    });

		});
	}else{
		//single image
		$a = $aClicked;
		$img = $a.find('img');
		altText = citadelaGetCaption( $img );
		imgIndex = 0;
		$a.removeClass('clicked');
		var size = $a.attr('data-image-size').split("x");
		items.push({
	        src: $a.attr('href'),
	    	title: altText,
	        w: size[0],
	        h: size[1],
	    });
	}

	if( items.length > 0 ){
		var pswpElement = jQuery('.pswp')[0];

		var options = {
			index: parseInt(imgIndex),
		    history: false,
		    shareEl: false,
		    closeOnScroll: false,
		    showHideOpacity:true,
		    bgOpacity: 0.85,
		};

		var pswp = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
		pswp.init();
	}

}

function citadelaGetCaption( $img ) {
	if( ! $img.length ) return;
	var $figure = $img.closest('figure');
	if ( $figure.length && $figure.find('figcaption').length){
		return $figure.find('figcaption').html();
	}else{
		return $img.attr('alt') ? $img.attr('alt') : "";
	}
}

function citadelaGetPswpHtml() {
	//Photoswipe html structure for fancybox
	return '<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true"><div class="pswp__bg"></div><div class="pswp__scroll-wrap"><div class="pswp__container"><div class="pswp__item"></div><div class="pswp__item"></div><div class="pswp__item"></div></div><div class="pswp__ui pswp__ui--hidden"><div class="pswp__top-bar"><div class="pswp__counter"></div><button class="pswp__button pswp__button--close" ></button><button class="pswp__button pswp__button--share" ></button><button class="pswp__button pswp__button--fs" ></button><button class="pswp__button pswp__button--zoom"></button><div class="pswp__preloader"><div class="pswp__preloader__icn"><div class="pswp__preloader__cut"><div class="pswp__preloader__donut"></div></div></div></div></div><div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"><div class="pswp__share-tooltip"></div></div><button class="pswp__button pswp__button--arrow--left"></button><button class="pswp__button pswp__button--arrow--right" ></button><div class="pswp__caption"><div class="pswp__caption__center"></div></div></div></div></div>';
}



function citadelaLoadOriginalImageSizes(){
	jQuery('.citadelaFancyboxElement').each(function(){
		if( ! jQuery(this).attr('data-image-size') ){
			citadelaGetOriginalImageSize(jQuery(this),function(data){
			    var $a = data.a;
			    $a.attr('data-image-size', data.width + 'x' + data.height);
			    //data.height
			    $a.off();
			    $a.on('click', function(e){
					e.preventDefault();
			    	citadelaOpenFancybox(jQuery(this));
				});
			});

		}else{
			jQuery(this).on('click', function(e){
				e.preventDefault();
		    	citadelaOpenFancybox(jQuery(this));
			});
		}
	});
}


function citadelaGetOriginalImageSize( $a, callback){
	//helper function to get real image size, until the problem isn't solved by wordpress/gutenberg
    var img = new Image();
    var $a = $a;
    img.onload = function(){
        callback({
            width : img.width,
            height : img.height,
            a: $a
        });
    }
    img.src = $a.attr('href');
}