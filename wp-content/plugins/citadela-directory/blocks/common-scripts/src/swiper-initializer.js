import Swiper from 'swiper';

const containersSelector = '.wp-block-citadela-blocks.use-carousel';

const swiperContainers = document.querySelectorAll( containersSelector );

for ( const node of swiperContainers ) {
	maybeInitSwiper( node );
}

window.addEventListener('resize', function(event){
    for ( const node of swiperContainers ) {
		maybeInitSwiper( node );
	}
});

function maybeInitSwiper( node ){
	var swiperData = JSON.parse( node.getAttribute( 'data-carousel' ) );
	var swiperContainer = node.querySelector('.citadela-block-articles');
	var swiperSlides = swiperContainer.querySelectorAll('.swiper-slide');
	
	//check width of swiper container and swiper slides to make sure the swiper needs to be initialized
	var slidesWidth = 0;
	for ( const slide of swiperSlides ){
		if( ! slide.classList.contains('swiper-slide-duplicate') ){
			slidesWidth += slide.offsetWidth;
		}
	}

	//check for swiper previously initialized
	var swiper = jQuery(swiperContainer).data('swiper');
	
	const screenZoomReserve = 10; // increase container width with reserve in pixels to make sure the space for spacer is calculated correctly even on zoomed screen

	const spaceForSwiper = slidesWidth > ( swiperContainer.offsetWidth + screenZoomReserve );
	
	const applySwiper = spaceForSwiper && typeof swiper != 'object';

	// calculate number of visible slides on the base of defined max-width css for one slide
	const slide = swiperContainer.querySelector('.swiper-slide');
	const slideMaxWidthPercent = parseInt( window.getComputedStyle(slide).maxWidth );
	const slideWidthFromCssPercentage = swiperContainer.offsetWidth / 100 * slideMaxWidthPercent;
	const visibleSlides = Math.round( swiperContainer.offsetWidth / slideWidthFromCssPercentage );

	if( applySwiper ){

		var options = {
			speed: 400,
		    spaceBetween: 0,
			slidesPerView: 'auto',
			on: {
				init: function () {
					node.classList.remove("loading-content");
					node.classList.add("swiper-initialized");
				},
			},
		};
		
		if( swiperData['loop'] ){
			options.loop = true;
			options.loopedSlides = 6;
		}else{
			options.loop = false;
		}

		if( swiperData['autoHeight'] ){
			options.autoHeight = true;
			options.slidesPerView = visibleSlides;
		}

		if( swiperData['autoplay'] ){
			options.autoplay = {
				delay: swiperData['autoplayDelay'] * 1000,
				disableOnInteraction: false,
			  };
		}
		
		if( swiperData['navigation'] ){

			options.navigation = {
				nextEl: node.querySelector( '.carousel-button-next' ),
				prevEl: node.querySelector( '.carousel-button-prev' ),
			};
		}

		if( swiperData['pagination'] ){
			options.pagination = {
				el: node.querySelector( '.carousel-pagination-wrapper' ),
				type: 'bullets',
				clickable: true,
				bulletClass: 'carousel-bullet',
				bulletActiveClass: 'active',
			};
			
			options.renderBullet = function (index, className) {
				return '<span class="' + className + '">' + (index + 1) + '</span>';
			};
		}
		
		swiper = new Swiper( swiperContainer, options );

		//save swiper with container as hidden data attribute, use jQuery for this
		jQuery(swiperContainer).data('swiper', swiper);

	}else{
		if( spaceForSwiper ){
			// customize swiper parameters if needed
			if( swiper ){
				if( swiperData['autoHeight'] ){
					swiper.params.slidesPerView = visibleSlides;
				}
			}
		}else{
			//check if there was swiper initialized before
			if( swiper ){
				swiper.destroy();
				jQuery(swiperContainer).data('swiper', false);
				node.classList.remove("swiper-initialized");
			}
			node.classList.remove("loading-content");
		}
	}
}