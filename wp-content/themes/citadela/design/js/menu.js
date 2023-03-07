
jQuery(document).ready(function(){
	"use strict";
	citadelaResponsiveClass();
	citadelaMainMenuBurgerPrepare();
	citadelaMainMenuBurger();
	citadelaResponsiveMenuCloseBtn();
	citadelaSubmenuManagement();
	citadelaMainMenuScroll();
	citadelaTriggers();
});

/* Window Resize Hook */
jQuery(window).resize(function(){
	citadelaResponsiveClass();
	citadelaMainMenuBurger();
	citadelaSubmenuManagement();
	
});

function citadelaResponsiveClass(){
	var $body = jQuery( 'body' );
	if( citadela_isResponsive(600) ){
		if( ! $body.hasClass('mobile-screen-width') ){
			$body.addClass('mobile-screen-width');
			$body.trigger('ctdl_device_design_swap', 'mobile' );
		}
	}else{
		if( $body.hasClass('mobile-screen-width') ){
			$body.removeClass('mobile-screen-width');
			$body.trigger('ctdl_device_design_swap', 'desktop');
		}
	}
	$body.find('.site-header.loading').removeClass('loading');
}

function citadelaTriggers(){
	jQuery( 'body' ).on( 'ctdl_device_design_swap', function(e, device){
		citadelaMainMenuScroll();
	});

}

function citadelaResponsiveMenuCloseBtn() {
	var $navContainer = jQuery('nav#site-navigation');
	if( $navContainer.hasClass('no-main-menu') ) return;

	$navContainer.find('.citadela-menu-container').prepend('<span class="responsive-close-button"></span>');
	var $responsiveCloseButton = $navContainer.find('.responsive-close-button')

	$responsiveCloseButton.on('click', function(e){
		var $liWrapper = jQuery(this).siblings('ul.citadela-menu').find('li.menu-item-wrapper');
		citadelaSubmenuClickAction( $liWrapper );
	});

}

function citadelaMainMenuScroll(){
	var $header = jQuery('header#masthead');
	var $sticky_wrapper = jQuery('.sticky-header-wrapper');
	var withStickyHeader = jQuery('body').hasClass('sticky-header-enabled');
	var $element = withStickyHeader ? $sticky_wrapper : $header;
	if( ! $element.length ) return;
	var $body = jQuery('body');
	// waypoints are enabled with delay because of wait for css transitions in header elements
	setTimeout(function(){
		var waypoint = new Waypoint({
		  element: $element,
		  handler: function(direction) {
		    switch (direction) {
			  case 'up':
			    //scrolling from down to up
			    $body.removeClass('header-scrolled');
			    citadelaCloseAllTopItemsSubmenus();
			    citadelaCloseWoocommerceCart();
			    // do not manipulate with menu in desktop sticky header
			    if( ! ( ! $body.hasClass( 'mobile-screen-width' ) && $body.hasClass( 'sticky-header-desktop-full' ) ) ){
				    citadelaMainMenuBurger();
				    citadelaSubmenuManagement();
				}

				if( withStickyHeader ){
					citadelaHideStickyHeader( this.element );
				}
			  	break;
			  case 'down':
			    //scrolling from up to down
			    $body.addClass('header-scrolled');
			    citadelaCloseAllTopItemsSubmenus();
			    citadelaCloseWoocommerceCart();
			    
			    // do not manipulate with menu in desktop sticky header
			    if( ! ( ! $body.hasClass( 'mobile-screen-width' ) && $body.hasClass( 'sticky-header-desktop-full' ) ) ){
			    	citadelaMainMenuBurger();
			    	citadelaSubmenuManagement();
			    }

			    if( withStickyHeader ){
			    	citadelaShowStickyHeader( this.element );
			    }

			  	break;
			  default:
			  	return;
			}
		  },
		  offset: function() {
		  	var offset = 0;
		  	var $header = null;
		  	
		  	if( withStickyHeader ) {
				$header = this.element.find('#masthead');		  		
		  	}else{
				$header	= this.element;
		  	}

			if( $header.hasClass('is-sticky') ){
				offset = this.element.outerHeight();
			}else{
				offset = $header.outerHeight();
			}

		  	if( offset != 0 ){
				$header.attr('data-offset', offset);
				return -offset;
		  	}else{
		  		// if zero offset, return previously defined offset in header
		  		return -$header.attr('data-offset');
		  	}

		  }
		});
	}, 300 );
}

function citadelaSubmenuManagement(){
	
	var menuArrow = '<span class="submenu-arrow"></span>';
	var $navContainer = jQuery('nav#site-navigation');

	if( $navContainer.hasClass('no-main-menu') ) return;

	var $mainMenuUl = $navContainer.find('.citadela-menu-container').find('ul.citadela-menu');

	var $menuItemWrapper = $mainMenuUl.children('li.menu-item-wrapper');

	$menuItemWrapper.off();
	$menuItemWrapper.children('a').off();

	$mainMenuUl.find('li.menu-item-has-children').each(function(){
		var $li = jQuery(this);

		if( $li.hasClass('top-level-menu-item') ){
			$li.children('.sub-menu').on('mouseleave', function(){
				var $submenu = jQuery(this);
				if( $submenu.parent().hasClass('opened') ) return;
				$submenu.find('.opened').removeClass('opened');
				
			});
		}

		//close woocommerce when menu is opened on hover
		$li.on('mouseenter focusin', function(){
			citadelaCloseWoocommerceCart();
		});

		// do not apply following events for menu button
		if( $li.hasClass("menu-item-wrapper") ) return;
		//create submenu arrows
		if( ! $li.children('span.submenu-arrow').length ) $li.append(menuArrow);
		
		//apply click for submenu arrow
		$li.children('span.submenu-arrow').off();
		$li.children('span.submenu-arrow').on('click', function(e){
			citadelaCloseWoocommerceCart();
			var $arrow = jQuery(this);
			var $clickedLi = $arrow.parent('li');
			citadelaSubmenuClickAction( $clickedLi );
		});

		//apply click for custom menu item with empty link or link to #
		if($li.hasClass('menu-item-type-custom')){
			var $a = $li.children('a');
			if(!$a.attr('href') || $a.attr('href') == '#'){
				$a.off();
				$a.on('click', function(e){
					e.preventDefault();
					var $clickedLi = $a.parent('li');
					citadelaSubmenuClickAction( $clickedLi );
				});
				
			}
		}
	});
	
	var isResponsiveMenu = jQuery('body').hasClass('mobile-screen-width') || citadelaResponsiveMenuOnDesktop();

	if( isResponsiveMenu ){
		//apply events in responsive menu
		$menuItemWrapper.children('a').on( 'click', function(e){
			e.preventDefault();
		});
		$menuItemWrapper.children('a').on( 'mousedown', function(e){
			citadelaSubmenuClickAction( jQuery(this).parent('li') );
		});
		$menuItemWrapper.focusin(function(){
			jQuery('body').addClass('menu-opened');
			citadelaCloseWoocommerceCart();
		});
		$menuItemWrapper.focusout(function(){
			if( jQuery(this).hasClass('opened') ) return;
			jQuery('body').removeClass('menu-opened');
		});
	}else{
		$menuItemWrapper.children('a').on( 'click', function(e){
			e.preventDefault();
			citadelaSubmenuClickAction( jQuery(this).parent('li') );
		})
		
	}


}


function citadelaSubmenuClickAction( $li ){
	var $clickedLi = $li,
		$body = jQuery('body');

	if($clickedLi.hasClass('opened')){

		if( $clickedLi.hasClass("menu-item-wrapper") || $clickedLi.hasClass("menu-item-type-custom") ){
			//remove focus after click on opened wrapper menu
			$clickedLi.children('a').blur();
		}

		$clickedLi.removeClass('opened');
		$clickedLi.children('ul.sub-menu').removeClass('opened');
		//close all child nodes
		$clickedLi.find('li.opened').removeClass('opened');
		$clickedLi.find('ul.sub-menu.opened').removeClass('opened');

		//body class for opened responsive menu
		if( $clickedLi.hasClass('menu-item-wrapper') && $body.hasClass('responsive-menu') ){
			$body.removeClass('menu-opened');
		}
	}else{
		citadelaCloseWoocommerceCart();
		citadelaCloseAllSiblingTopLevelItems( $clickedLi );
		$clickedLi.addClass('opened');
		$clickedLi.children('ul.sub-menu').addClass('opened');

		//body class for opened responsive menu
		if( $clickedLi.hasClass('menu-item-wrapper') && $body.hasClass('responsive-menu') ){
			$body.addClass('menu-opened');
		}
	}

}
	
function citadelaCloseAllSiblingTopLevelItems( $li ){
	var $clickedLi = $li;
	var $sibling;
	$clickedLi.siblings('.top-level-menu-item.opened').each(function(){
		$sibling = jQuery(this);
		$sibling.removeClass('opened');
		$sibling.find('li.opened').removeClass('opened');
	});
}

function citadelaGetMenuAdditions(){
	//selectors of parts displayed in navigation place
	return [ 
		'.citadela-woocommerce-minicart' 
	];
}

function citadelaGetMenuAvailableWidth(){
	var $container = jQuery('nav#site-navigation');
	var $burgerMenuWrapper = $container.find('li.menu-item-wrapper');
	var headerAdditions = citadelaGetMenuAdditions();

	if( jQuery('body').hasClass('center-header-layout') && ! $container.parents('#masthead').hasClass('is-sticky') ){
		$container = $container.find('ul.citadela-menu');
		headerAdditions = [];
	}
	
	var availableWidth = $container.width() - 20;

	jQuery.each( headerAdditions , function( $key, $value ){
		if( jQuery( $value ).length ){
			var w = Math.ceil( jQuery( $value ).outerWidth(true) );
			availableWidth = availableWidth - w;
		}
	});

	return Math.floor( availableWidth );
}

function citadelaMainMenuBurgerPrepare(){
	var $navContainer = jQuery('nav#site-navigation');
	if( $navContainer.hasClass('no-main-menu') ) return;

	// menu items widths
	var $menuContainer = $navContainer.find('ul.citadela-menu');
	var widthLiAll = 0,
		widthLi = 0;
	$menuContainer.children('li').each(function(pos){
		jQuery(this).addClass('menu-item-position-' + pos);
		if( ! jQuery('body').hasClass('mobile-screen-width') ){
			//calculate sizes only for desktop design, in responsive design is li size = 0
			widthLi = Math.ceil( jQuery(this).outerWidth(true) );
			jQuery(this).attr('data-width', widthLi);
			widthLiAll += widthLi;
		}
	});
	$menuContainer.attr('data-liWidth', widthLiAll);

	// append burger li .. burger is always created
	var hamburgerHtml = '<i class="fa fa-bars"></i>';
	$menuContainer.append('<li class="menu-item-wrapper menu-item-has-children sub-menu-right-position top-level-menu-item"><a href="#">' + hamburgerHtml + '</a><ul class="sub-menu"></ul></li>');
	var $burgerMenuWrapper = $menuContainer.find('li.menu-item-wrapper');
	var $burgerMenuContainer = $burgerMenuWrapper.find('.sub-menu');
	
	// fill up burger menu with data
	var $menuContainerChildren = $menuContainer.children('li:not(.menu-item-wrapper)').clone(true);
	$menuContainerChildren.appendTo($burgerMenuContainer);
	
	$burgerMenuWrapper.find('li').each(function(){
		jQuery(this).addClass('menu-item-cloned');
	});
	
	// add new classes
	$burgerMenuContainer.find('li').each(function(){
		if(jQuery(this).children('ul').length > 0){
			jQuery(this).addClass('menu-item-has-children');
		}
	});

	var burgerMenuItemWidth = $burgerMenuWrapper.outerWidth();
	
	var availableWidth = citadelaGetMenuAvailableWidth();

	$navContainer.attr('data-availablespace', availableWidth );
	$navContainer.attr('data-burgerspace', burgerMenuItemWidth);
	
	availableWidth = availableWidth - burgerMenuItemWidth;	

	// will the burger be shown by default
	if(availableWidth < widthLiAll){
		$burgerMenuWrapper.css({'display': 'inline-block'});
	} else {
		$burgerMenuWrapper.css({'display': 'none'});
	}

	// reset all styles added by script
	$navContainer.removeClass('menu-hidden');
}

function citadelaMainMenuBurger(){
	var $navContainer = jQuery('nav#site-navigation');
	

	var $menuContainer = $navContainer.find('.citadela-menu');
	if( ! citadelaResponsiveMenuOnDesktop() && ! jQuery('body').hasClass('mobile-screen-width') ) {
		//desktop design
		//remove body class for responsive menu
		jQuery('body').removeClass('responsive-menu menu-opened');
		
		//do not continue if there is no menu defined
		if( $navContainer.hasClass('no-main-menu') ) return;
		
		citadelaCloseAllTopItemsSubmenus();
		
		// calculate real size of menu li items after screen change
		// necessary after move from responsive to desktop design
		var widthLiAll = 0,
			widthLi = 0;
		$menuContainer.children('li:not(.menu-item-wrapper)').each(function(pos){
			widthLi = Math.ceil( jQuery(this).outerWidth(true) );
			jQuery(this).attr('data-width', widthLi);
			widthLiAll += widthLi;
		});
		$menuContainer.attr('data-liWidth', widthLiAll);
		
		// update available space
		var headerAdditions = citadelaGetMenuAdditions();
		var widthNav = $navContainer.width();
		var availableSpace = widthNav;
		jQuery.each( headerAdditions , function($key, $value ){
			var w = jQuery( $value ).outerWidth(true);
			availableSpace = availableSpace - w;
		});
		var $wooCart = jQuery('.citadela-woocommerce-minicart');


		var availableWidth = citadelaGetMenuAvailableWidth();

		$navContainer.attr('data-availablespace', availableWidth );

		var $burgerMenuWrapper = $menuContainer.find('li.menu-item-wrapper');
		
		$burgerMenuWrapper.show();

		var burgerMenuItemWidth;
		burgerMenuItemWidth = parseInt($navContainer.attr('data-burgerspace'));
		
		availableWidth = availableWidth - burgerMenuItemWidth;
		
		if(parseInt($menuContainer.attr('data-liWidth')) > availableWidth ){
			// the menu is bigger than available width in menu container
			var fittingWidth = availableWidth;
			$menuContainer.children('li:not(.menu-item-wrapper)').each(function(pos){
				// for every li, get his width and try to fit it
				var liWidth = parseInt(jQuery(this).attr('data-width')) == 0 ? Math.ceil( jQuery(this).outerWidth(true) ) : parseInt(jQuery(this).attr('data-width'));
				fittingWidth = parseInt(fittingWidth - liWidth);
				if(fittingWidth > 0){
					// no problem .. li fits
					jQuery(this).show().css('white-space', '');;
					jQuery(this).addClass('top-level-menu-item');
					// hide in the wrapmenu
					$menuContainer.find('.menu-item-cloned.menu-item-position-' + pos).hide().removeClass('opened');
				} else {
					// problem .. li doesnt fit
					jQuery(this).hide().css('white-space', 'nowrap');
					jQuery(this).removeClass('top-level-menu-item opened');
					// show in the wrapmenu
					$menuContainer.find('.menu-item-cloned.menu-item-position-' + pos).show();
				}
			});
			$menuContainer.find('.menu-item-wrapper').css({'display': 'inline-block'});
		} else {
			var fittingWidth = availableWidth;
			$menuContainer.children('li:not(.menu-item-wrapper)').each(function(pos){
				var liWidth = parseInt(jQuery(this).attr('data-width')) == 0 ? jQuery(this).width() : parseInt(jQuery(this).attr('data-width'));
				fittingWidth = parseInt(fittingWidth - liWidth);
				if(fittingWidth > 0){
					// no problem .. li fits
					jQuery(this).show();
					jQuery(this).addClass('top-level-menu-item');
					// hide in the wrapmenu
					$menuContainer.find('.menu-item-cloned.menu-item-position-' + pos).hide();
				}
			});
			// hide wrapping menu
			$menuContainer.find('.menu-item-wrapper').css({'display': 'none'});
		}

	}else{
		//responsive design
		//add body class for responsive menu
		jQuery('body').addClass('responsive-menu');

		//do not continue if there is no menu defined
		if( $navContainer.hasClass('no-main-menu') ) return;

		$menuContainer.attr('data-liWidth', 0);
		$menuContainer.find('.menu-item-wrapper').css({'display': 'inline-block'});
		$menuContainer.children('li:not(.menu-item-wrapper)').each(function(pos){
			jQuery(this).hide().css('white-space', 'nowrap');
			$menuContainer.find('.menu-item-cloned.menu-item-position-' + pos).show();
		});
	}
}

function citadelaCloseAllTopItemsSubmenus(){
	$mainMenuUl = jQuery('#masthead').find('.citadela-menu-container').find('ul.citadela-menu');
	$mainMenuUl.find('li.top-level-menu-item.opened').each(function(){
		jQuery(this).removeClass('opened');
		
		// make sure that :focus-within from menu wrapper is removed too,
		// prevent problem with focused dropdown and page scroll down what cause immediate responsive menu opening in some cases
		if( jQuery(this).hasClass('menu-item-wrapper') && jQuery(document.activeElement).parent().hasClass('menu-item-wrapper') ){
			document.activeElement.blur();
		}
	});
}

function citadelaCloseWoocommerceCart(){
	$cart = jQuery('#masthead').find('.citadela-woocommerce-minicart');
	if( ! $cart.length ) return;

	$cart.removeClass('opened');
	jQuery('body').removeClass('cart-opened');
}

function citadelaResponsiveMenuOnDesktop() {
	return jQuery('body').hasClass('responsive-menu-on-desktop') || ( jQuery('body').hasClass('sticky-header-desktop-burger') && jQuery('body').hasClass('header-scrolled') );
}