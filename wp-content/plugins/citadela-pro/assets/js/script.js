/*
*	Citadela Pro plugin javascripts
*
*/

jQuery(document).ready(function(){
	"use strict";
	// functionality for widget titles deprecated from WP 5.8
	//citadelaWidgetTitles();
	//citadelaCollapsibleSidebarsWidgets();
	citadelaCollapsibleFooterWidgets();
	citadelaWoocommerceCart();
	citadelaMobileSettings();
	citadelaProTriggers();
});

/* Window Resize Hook */
jQuery(window).resize(function(){
	// functionality for widget titles deprecated from WP 5.8
	//citadelaCollapsibleSidebarsWidgets();
	citadelaCollapsibleFooterWidgets();
	citadelaClearStickyHeaderClasses();

});

function citadelaProTriggers(){
	jQuery( 'body.sticky-header-enabled header#masthead' ).on( 'ctdl_sticky_header_toggled', function(e){
		citadelaCloseAllTopItemsSubmenus();
		citadelaMainMenuBurger();
	});

	jQuery( 'body' ).on( 'ctdl_device_design_swap', function(e, device){
		var $body = jQuery( 'body' );

		citadelaMobileSettings();

		if( $body.find('header#masthead').hasClass('is-sticky') || ( $body.hasClass('header-scrolled') && $body.hasClass(`sticky-header-${device}-full`) ) ){
			citadelaRestartStickyHeader();
		}

		if(  $body.hasClass('sticky-header-enabled') ){
			var $header = jQuery('body').find('header#masthead');
			var $custom_header_logo = $header.find('.custom-header-logo');
			var $default_logo = $header.find('.default-logo');

			// check if device may have sticky header
			if(  $body.hasClass(`sticky-header-${device}-full`) ){
				if( $header.hasClass('is-sticky') ){
					citadelaSwitchLogo( $custom_header_logo, $default_logo );
				}else{
					citadelaSwitchLogo( $default_logo, $custom_header_logo );
				}
			}else{
				//set logo for non sticky header
				citadelaSwitchLogo( $default_logo, $custom_header_logo );
			}
		}

	});

}
function citadelaRestartStickyHeader(){
	var $body = jQuery( 'body' );
	$body.find('header#masthead').removeClass('is-sticky');
	$body.find('.sticky-header-wrapper').css('height', '');
	// show sticky header with delay because of css transitions in header elements
	setTimeout(function(){
		citadelaShowStickyHeader($body.find('.sticky-header-wrapper'));
	},300 );

}
function citadelaMobileSettings(){
	var $body = jQuery( 'body' );
	var $logo_wrapper = jQuery( 'header#masthead .logo-wrapper' );
	var $site_title = jQuery( 'header#masthead .site-title' );
	var $logo_wrapper_maxWidth = $logo_wrapper.data( 'mobile-max-width' );
	var $site_title_fontSize = $site_title.data( 'mobile-font-size' );
	var isResponsive = $body.hasClass( 'mobile-screen-width' );
	
	if( isResponsive ){
		if( $logo_wrapper_maxWidth ){
			$logo_wrapper.css('max-width', $logo_wrapper_maxWidth['mobile']);
		}
		if( $site_title_fontSize ){
			$site_title.css('font-size', $site_title_fontSize['mobile']);
		}
		
	}else{
		if( $logo_wrapper_maxWidth ){
			$logo_wrapper.css('max-width', $logo_wrapper_maxWidth['desktop']);
		}
		if( $site_title_fontSize ){
			$site_title.css('font-size', $site_title_fontSize['desktop']);
		}
	}
}

function citadelaClearStickyHeaderClasses(){
	var $body = jQuery('body');
	var isResponsive = $body.hasClass( 'mobile-screen-width' );
	if( isResponsive ){
		if( ! $body.hasClass( 'sticky-header-mobile-full' ) ){
			$body.find('header.is-sticky').removeClass('is-sticky');
			$body.find('.sticky-header-wrapper').css('height', '');
		}
	}else{
		if( ! $body.hasClass( 'sticky-header-desktop-full' ) ){
			$body.find('header.is-sticky').removeClass('is-sticky');
			$body.find('.sticky-header-wrapper').css('height', '');
		}
	}
}

function citadelaHideStickyHeader( $wrapper ){
	var $body = jQuery('body');
	var $header = $wrapper.find('#masthead');
	if( 
		( $body.hasClass( 'mobile-screen-width' ) && $body.hasClass( 'sticky-header-mobile-full' ) )
		|| ( ! $body.hasClass( 'mobile-screen-width' ) && $body.hasClass( 'sticky-header-desktop-full' ) )
	){
		$wrapper.css( 'height', '');
		$wrapper.find('#masthead').removeClass('is-sticky');
		citadelaSwitchLogo( $header.find('.default-logo'), $header.find('.custom-header-logo') );
		$wrapper.find('#masthead').trigger('ctdl_sticky_header_toggled');
	}
}
function citadelaShowStickyHeader( $wrapper ){
	var $body = jQuery('body');
	var $header = $wrapper.find('#masthead');
	if( 
		( $body.hasClass( 'mobile-screen-width' ) && $body.hasClass( 'sticky-header-mobile-full' ) )
		|| ( ! $body.hasClass( 'mobile-screen-width' ) && $body.hasClass( 'sticky-header-desktop-full' ) )
	){
		if( ! $body.hasClass('custom-header-over-content') ){
			$wrapper.css( 'height', $header.attr('data-offset') + "px" );
		}
		if( ! $header.hasClass('is-sticky') ){
			// set sticky header parameters while it's hidden
			$header.css('visibility', 'hidden').addClass('is-sticky');
			citadelaSwitchLogo( $header.find('.custom-header-logo'), $header.find('.default-logo') );
			$header.trigger('ctdl_sticky_header_toggled');
			// finally show sticky header
			$header.hide().css('visibility', '').delay(1).slideDown();
		}
	}
}

function citadelaSwitchLogo( from, to, cb = false ){
	if( from.length && to.length ){
		from.fadeOut( 0, () => {
			to.fadeIn( 0, () => { if( cb ) cb() } )
		} );
	}
}
function citadelaWoocommerceCart(){
	if( ! jQuery('.citadela-woocommerce-minicart').length ) return;
	var $cart = jQuery('.citadela-woocommerce-minicart');
	$cart.find('.cart-header').on('click', function(e){
		//close all submenus
		$mainMenuUl = jQuery('#masthead').find('.citadela-menu-container').find('ul.citadela-menu');
		$mainMenuUl.find('.menu-item.opened, .menu-item-wrapper.opened, .sub-menu.opened').each(function(){
			jQuery(this).removeClass('opened');
		});

		var $body = jQuery('body');
		var isResponsive = $body.hasClass('responsive-menu');
		if( $cart.hasClass('opened') ){
			$cart.removeClass('opened');
			$body.removeClass('cart-opened');
		}else{
			$cart.addClass('opened');
			if( isResponsive ){
				$body.addClass('cart-opened');
			}
		}
	});

	jQuery(document.body).on('added_to_cart removed_from_cart wc_fragments_refreshed', function(e){
		//update class in main holder
		var $count = parseInt( jQuery('.citadela-woocommerce-minicart .cart-count span').html() );
		if( typeof $count === 'number'){
			var $cart = jQuery('.citadela-woocommerce-minicart');
			if( $count === 0 ){
				$cart.addClass('is-empty');
			}else{
				$cart.removeClass('is-empty');
			}
			citadelaMainMenuBurger();
		}
	});

}

function citadelaWidgetTitles(){
	var $sidebarWidgets = jQuery('aside#secondary').find('.widget');
	var $footerWidgets = jQuery('#footer-widgets').find('.widget');
	var $widgets = jQuery.merge($sidebarWidgets, $footerWidgets);
	var $widget;

	if($widgets.length){
		$widgets.each(function(){
			$widget = jQuery(this);
			if(citadelaIsEmptyWidgetTitle($widget)){
				$widget.find('div.widget-title').addClass('no-title');
			}
		});
	}
}

function citadelaCollapsibleFooterWidgets(){
	if( ! jQuery('body').hasClass('footer-collapsible-widgets-enabled') ){
		return;
	}
	var $footer = jQuery('footer#colophon');
	var $footerWidgetsButton = $footer.find('.footer-widgets-button');
	var $footerWidgetsArea = $footer.find('#footer-widgets');

	if( citadela_isResponsive(600) ){
		//before first load
		if( ! jQuery('body').hasClass('footer-collapsible-widgets') && jQuery('body').hasClass('footer-widgets-default-opened')){
			$footerWidgetsButton.addClass('opened').removeClass('hidden');
			$footerWidgetsArea.addClass('collapsible-widgets opened');
		}else{
			$footerWidgetsButton.removeClass('hidden');
			$footerWidgetsArea.addClass('collapsible-widgets');
		}
		jQuery('body').addClass('footer-collapsible-widgets');
	}else{
		$footerWidgetsButton.addClass('hidden').removeClass('opened');
		$footerWidgetsArea.removeClass('collapsible-widgets opened');
		jQuery('body').removeClass('footer-collapsible-widgets');
	}

	$footerWidgetsButton.off();
	$footerWidgetsButton.on('click', function(e){
		if($footerWidgetsButton.hasClass('opened')){
			$footerWidgetsButton.removeClass('opened');
			$footerWidgetsArea.removeClass('opened');
		}else{
			$footerWidgetsButton.addClass('opened');
			$footerWidgetsArea.addClass('opened');
		}
	});

}


function citadelaCollapsibleSidebarsWidgets(){
	var $sidebar = jQuery('aside#secondary');
	var $widgets = $sidebar.find('.widget');
	if($widgets.length){
		$widgets.each(function(){
			$widget = jQuery(this);
			if( citadela_isResponsive(1024) ){
				jQuery('body').addClass('sidebar-collapsible-widgets');
				citadelaApplyCollapsibleSidebarWidget($widget, $sidebar);
			}else{
				jQuery('body').removeClass('sidebar-collapsible-widgets');
				citadelaDisableCollapsibleSidebarWidget($widget, $sidebar);
			}
		});
	}
}

function citadelaApplyCollapsibleSidebarWidget($widget, $sidebar){
	if( !$widget.hasClass('collapsible-widget')){
		$widgetTitle = $widget.find('div.widget-title');
		if( ! $widgetTitle.hasClass('no-title') ){
			
			if( ( $sidebar.hasClass('left-widget-area') && jQuery('body').hasClass('left-widgets-default-opened') )
			|| ( $sidebar.hasClass('right-widget-area') && jQuery('body').hasClass('right-widgets-default-opened') ) ){
				$widget.addClass('opened');
			}else{
				$widget.find('div.widget-container').hide();
			}
			
			$widgetTitle.on('click', function(e){
				e.preventDefault();
				var $clickedWidget = jQuery(this).parent('.widget');
				var $clickedWidgetContainer = $clickedWidget.find('.widget-container');
				$clickedWidget.toggleClass('opened');
				if($clickedWidget.hasClass('opened')){
					$clickedWidgetContainer.slideDown();
				}else{
					$clickedWidgetContainer.slideUp();
				}

			});
		}
	}
	$widget.addClass('collapsible-widget');
};

function citadelaDisableCollapsibleSidebarWidget($widget, $sidebar){
	if( $widget.hasClass('collapsible-widget')){
		$widget.removeClass('collapsible-widget opened');
		$widget.find('div.widget-container').show();
		$widgetTitle = $widget.find('div.widget-title');
		$widgetTitle.off();
	}
}


function citadelaIsEmptyWidgetTitle($widget){
	var titleContent = $widget.find('div.widget-title').html();
	if( titleContent === "" || titleContent === "<!-- citadela-no-widget-title -->" ){
		return true;
	}
	return false;
}
