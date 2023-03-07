(function($, $document){

	"use strict";

	var isTouch = (('ontouchstart' in window) ||
   		(navigator.maxTouchPoints > 0) ||
    	(navigator.msMaxTouchPoints > 0));

	var menu = $('#easyadmin-main-menu'),
		menuAnchor = $('#easyadmin-main-menu > li > a.wp-has-submenu'),
		menuItem = $('#easyadmin-main-menu > li');

	var admin = {

		init: function() {
			admin.bind();
			admin.menuScrollToActive();
		},

		bind: function() {
			menuAnchor.on('click', admin.touchMenuClick);
			menuItem.hover(admin.hover);
		},

		/* Prevent menu click on touch if has submenu */
		touchMenuClick: function() {
			$('html, body').scrollTop(0);

			if (isTouch) {
				event.preventDefault();
			}
		},

		/* Scroll menu to active item */
		menuScrollToActive: function() {
			var active = menu.find('li.wp-has-current-submenu');
			var active = active.length > 0 ? active : menu.find('li.current');

			menu.scrollLeft(active.position().left);
		},

		/* Toggles submenu */
		hover: function() {
			$(this).toggleClass('hover');
		}

	}

	$(function(){

		if ($('body').hasClass('citadela-easy-admin-enabled')) {
			admin.init();
		}

	});

})(jQuery, jQuery(document));