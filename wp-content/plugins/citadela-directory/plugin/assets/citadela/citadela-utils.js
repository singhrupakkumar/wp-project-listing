/**
 * Citadela JS Lib
 */

var citadela = citadela || {};
citadela.ajax = citadela.ajax || {};
citadela.headerMap = citadela.headerMap || {};

(function($, $document, undefined){

	"use strict";

	var _settings = typeof CitadelaDirectorySettings === 'undefined' ? {} : CitadelaDirectorySettings;
	citadela = jQuery.extend(citadela, _settings);

	function is_responsive(width){
		var w=window,
			d=document,
			e=d.documentElement,
			g=d.getElementsByTagName('body')[0],
			x=w.innerWidth||e.clientWidth||g.clientWidth;
		return x < parseInt(width);
	}


	// ================================
	// Citadela Ajax Utils
	// --------------------------------
	/**
	 * Load data from the server using a HTTP POST or GET request.
	 * Same as $.post, $.get jQuery methods, but instead of URL in the first parameter
	 * it is used wp ajax action hook
	 */
	jQuery.each(["get", "post"], function(i, method){
		citadela.ajax[method] = function(action, data){
			var _action;
			var a = action.split(":");
			if(a.length > 1 && (action in citadela.ajax.actions)){
				_action = citadela.ajax.actions[action];
			}else{
				_action = action;
			}

			if(typeof data === "object")
				data = jQuery.param(jQuery.extend(data, {action: _action}));
			else if(typeof data === "string")
				data = jQuery.param({action: _action}) + '&' + data;
			else
				data = jQuery.param({action: _action});

			return jQuery.ajax({
				url: citadela.ajax.url,
				type: method,
				data: data
			});
		};
	});

	/*
	 *	Advanced Filters
	 **/
	var advanced_filters = {
		container: null,

		init: function()
		{
			var self = this;
			var $container = jQuery( '.ctdl-directory-advanced-filters' );
			self.container = $container;

			$container.on( 'advancedFiltersInit', null, function(){
				self.applyClosedOnMobile( jQuery(this) );
				self.applyToggle( jQuery(this) );
				self.applyCheckboxClick( jQuery(this) );
				self.applySubmitButtonClick( jQuery(this) );
			});
			$container.trigger('advancedFiltersInit');

			// when filters are in the Search Form block, we can toggle whole advanced filters block
			$container.on( 'advancedFiltersBlockToggle', null, function(){
				self.blockToggle(jQuery(this));
			});
			
			$container.data('advancedFilters', self );

			return this;
		},
		applyClosedOnMobile: function( $container ){
			var self = this;
			if( $container.hasClass('closed-on-mobile') ){
				self.setDeviceState( $container );
				self.toggleOnMobile( $container );
				jQuery( window ).resize(function(){
					self.toggleDeviceState( $container );
				});
			}
		},
		toggleOnMobile: function( $container ){
			var self = this;
			if( $container.hasClass('mobile-size') ){
				self.toggleAdvancedHeader( $container, 'close' );
			}else if( $container.hasClass('desktop-size') ){
				self.toggleAdvancedHeader( $container, 'open' )
			}
		},
		setDeviceState: function( $container ){
			if( is_responsive(600) ){
				$container.addClass('mobile-size');
				$container.removeClass('desktop-size');
			}else{
				$container.addClass('desktop-size');
				$container.removeClass('mobile-size');
			}
		},
		toggleDeviceState: function( $container ){
			var self = this;
			if( is_responsive(600) ){
				//check if would be closed/opened filters
				if( $container.hasClass('desktop-size') ){
					self.toggleAdvancedHeader( $container, 'close' );
				}
				$container.addClass('mobile-size');
				$container.removeClass('desktop-size');
			}else{
				//check if would be closed/opened filters
				if( $container.hasClass('mobile-size') ){
					self.toggleAdvancedHeader( $container, 'open' );
				}
				$container.addClass('desktop-size');
				$container.removeClass('mobile-size');
			}
		},
		blockToggle: function( $container ){
			var self = this;
			if( $container.hasClass('hidden-block') ){
				$container.parents('.ctdl-directory-search-form').addClass('filters-opened');
				$container.slideDown(300, function(){
					$container.removeClass('hidden-block');
				});
			}else{
				$container.slideUp(300, function(){
					$container.addClass('hidden-block');
					$container.parents('.ctdl-directory-search-form').removeClass('filters-opened');
				});
			}
		},
		applyToggle: function( $container ){
			var self = this;
			if( $container.hasClass('advanced-header') ){		
				$container.find('.citadela-block-header').find('.header-toggle').on('click', function(){
					self.toggleAdvancedHeader( $container );
				});
			}
		},
		toggleAdvancedHeader: function( $container, $force_state = false ){
			var $content = $container.find('.citadela-block-articles');
			var $submit_wrapper = $container.find('.submit-button-wrapper');
			if( $force_state == false ){
				//toggle header by current state
				if( $container.hasClass('opened') ){
					$content.slideUp(300);
					$submit_wrapper.slideUp(300);
					$container.removeClass('opened');
				}else{
					$content.slideDown(300);
					$submit_wrapper.slideDown(300);
					$container.addClass('opened');
				}
			}else{
				//force header state
				if( $force_state == 'open' && ! $container.hasClass('opened') ){
					$content.slideDown(300);
					$submit_wrapper.slideDown(300);
					$container.addClass('opened');
				}else if( $force_state == 'close' && $container.hasClass('opened') ){
					$content.slideUp(300);
					$submit_wrapper.slideUp(300);
					$container.removeClass('opened');
				}
			}
		},
		applyCheckboxClick: function( $container ){
			var self = this;
			$container.find('.data-row.type-checkbox, .data-row.type-select, .data-row.type-citadela_multiselect').each(function(){
				jQuery(this).find('.filter-container').on('click', null, function(){
					var $filterContainer = jQuery(this);
					
					//toggle filter for all checkboxes with the same name and value, filter may be available on the page in more Advanced Filters blocks
					var filterName =  $filterContainer.find('input.filter-value').attr('name');
					var filterValue =  $filterContainer.find('input.filter-value').attr('value');
					self.toggleFilter( jQuery( '.ctdl-directory-advanced-filters' ).find(`input.filter-value[name=${filterName}][value=${filterValue}]`) );
				});


			});
		},
		toggleFilter: function( $inputs ){
			$inputs.each( function(){
				var $currentInput = jQuery(this);
				var $filterContainer = $currentInput.parent('.filter-container');
				
				if( $filterContainer.hasClass('selected') ){
					$filterContainer.removeClass('selected');
					$currentInput.attr( "checked", false );
				}else{
					$filterContainer.addClass('selected');
					$currentInput.attr( "checked", true );
				}

			});
		},
		clearAllFilters: function(){
			var $inputs = jQuery( '.ctdl-directory-advanced-filters' ).find('input.filter-value');
			if( $inputs.length ){
				$inputs.each(function(){
					if( jQuery(this).attr('type') === 'checkbox' ){
						jQuery(this).parents('.filter-container').removeClass('selected');
						jQuery(this).attr( "checked", false );
					}
				});
			}
		},
		areSelectedFilters: function(){
			var $inputs = jQuery( '.ctdl-directory-advanced-filters' ).find('input.filter-value');
			var $result = false;
			if( $inputs.length ){
				$inputs.each(function(){
					if( jQuery(this).attr('type') === 'checkbox' && jQuery(this).attr( "checked" ) == "checked" ){
						
						$result = true;
					}
				});
			}
			return $result;
		},
		applySubmitButtonClick: function( $container ){
			var self = this;
			var $submitButton = $container.find('.submit-button a');
			$submitButton.on('click', null, function(){
				self.applyFilters( jQuery(this).get(0) );
				
			});
		},
		applyFilters: function( submitRef, fromSearchForm = false, searchFormParams = {} ){
			var self = this;
			//get all available filters on current page
			var $inputs = jQuery( '.ctdl-directory-advanced-filters' ).find('input.filter-value');
			var filters = [];
			
			if( $inputs.length ){

				var reserved_filters_params_names = [ 'a_filters', 'filters' ];

				$inputs.each(function(){

					var filter_name = jQuery(this).attr('name');
					var filter_value = jQuery(this).attr('value');
					
					//store existing filter names, we'll remove them from url before next submit
					if( jQuery.inArray( filter_name, reserved_filters_params_names ) === -1 ) reserved_filters_params_names.push( filter_name );
					
					//checkboxes
					if( jQuery(this).attr('type') === 'checkbox' && jQuery(this).attr('checked') === 'checked' ){
						//check if it's single checkbox filter or set of checkboxes from select and multiselect inputs
						if( filter_value == filter_name ){
							//single checkbox filter
							if( filters['filters'] === undefined ) filters['filters'] = [];
							if( jQuery.inArray( filter_value, filters['filters'] ) === -1 ){
								filters['filters'].push( filter_value );
							}
						}else{
							//checkboxes of select or multiselect inputs
							if( filters[ filter_name ] === undefined ) filters[ filter_name ] = [];
							if( jQuery.inArray( filter_value, filters[ filter_name ] ) === -1 ){
								filters[ filter_name ].push( filter_value );
							}
						}
					}

				});
				var filters_params = [];
				//insert main filter parameter to recognize there will be filters in url
				if( Object.keys(filters).length ) filters_params['a_filters'] = true;

				for (var filter_name in filters) {
					if (filters.hasOwnProperty( filter_name )){
						filters_params[filter_name] = filters[filter_name].join(',');
					}
				}

							
				//get url data
				var pathName = window.location.pathname;
				var homeUrl = window.location.protocol+"//"+window.location.host;
				var baseUrl = window.location.protocol+"//"+window.location.host+pathName;
				var eParams = window.location.search != "" ? window.location.search.replace("?", "").split('&') : {};
				var nParams = {};
				
				jQuery.each(eParams, function(index, value){
					var val = value.split("=");
					//do not get parameters of filters when they were in url, we'll add new parameters with filters
					if( jQuery.inArray( val[0], reserved_filters_params_names ) === -1 ){
						if(typeof val[1] == "undefined"){
							nParams[val[0]] = "";
						} else {
							nParams[val[0]] = decodeURIComponent(val[1]);
						}
					}
				});


				//make sure to use parameters for item search results
				if( fromSearchForm ){
					// submitted search form, check current search params already defined in search form
					nParams['ctdl'] = "true";
					nParams['post_type'] = "citadela-item";
					nParams['s'] = "";
					nParams['category'] = "";
					nParams['location'] = "";


					if( searchFormParams.keyword ) nParams['s'] = searchFormParams.keyword;
					if( searchFormParams.category ) nParams['category'] = searchFormParams.category;
					if( searchFormParams.location ) nParams['location'] = searchFormParams.location;
					
					if( searchFormParams.geoEnabled ){
					
						nParams['lat'] = searchFormParams.lat ? searchFormParams.lat : "";
						nParams['lon'] = searchFormParams.lon ? searchFormParams.lon : "";
						nParams['rad'] = searchFormParams.rad;
						nParams['unit'] = searchFormParams.unit;
					}else{
						delete nParams['lat'];
						delete nParams['lon'];
						delete nParams['rad'];
						delete nParams['unit'];
					}

					//set base url for submit - in case of search form, it's always search page
					baseUrl = jQuery( submitRef ).parents('form.search-form').attr('action');

				}else{
					// submitted are only filters from Advanced Filters block
					var submitAction = jQuery( submitRef ).data('action');
					var submitSource = jQuery( submitRef ).data('source');
					
					// if submit action url not defined, navigate to search page and set default search parameters
					baseUrl = submitAction;

					// do not set default search params if filters were submitted from Item Category or Item Location page, we need here only advanced filters parameters to filter posts on the same term page
					if( submitSource !== 'taxonomy' ){
						// use default search parameters or use those from url parameters
						if( ! nParams.ctdl ) nParams['ctdl'] = "true";
						if( ! nParams.post_type ) nParams['post_type'] = "citadela-item";
						if( ! nParams.s ) nParams['s'] = "";
						if( ! nParams.category ) nParams['category'] = "";
						if( ! nParams.location ) nParams['location'] = "";
					}

				}

				var query = jQuery.extend( {}, nParams, filters_params);
				var queryString = decodeURIComponent ( jQuery.param(query) );
				window.location.href = baseUrl + ( queryString ? "?" + queryString : '');

			}
			//return ;			
		}


	}

	/*
	 *	Item Reviews
	 **/


	if( jQuery('.single-citadela-item .item-reviews').length ){
		var $reviews_node = jQuery('.single-citadela-item .item-reviews');

		// apply clickable rating stars in review form
		citadela_apply_review_rating( $reviews_node );
		
		// remove rating stars in comment reply form
		citadela_apply_comment_cancel_reply_link_events( $reviews_node );
		citadela_apply_comment_reply_link_events( $reviews_node );

		// review form validation
		citadela_reviews_form_validation( $reviews_node );
	}

	function citadela_apply_review_rating( $reviews_node ){
		var $rating_node = $reviews_node.find('.comment-respond').find('.rating');
		$rating_node.raty({ 
			starType: 'i',
			hints: [ "", "", "", "", "", ],
			scoreName: 'rating',
			click: function( number ){
				var $form = jQuery(this).parents('#commentform');
				$form.find('.rating-notification').hide(100);
			},
		});
	}

	function citadela_apply_comment_reply_link_events( $reviews_node ){
		var $comment_reply_link = $reviews_node.find('.item_review').find('.comment-reply-link');
		$comment_reply_link.each( function(){
			jQuery(this).off('click');
			jQuery(this).on('click', function(){
				citadela_close_all_notifications( $reviews_node );
				citadela_remove_rating_node( $reviews_node );
				var $submit_button = $reviews_node.find('#submit-review-button');
				$submit_button.attr( 'value', $submit_button.data('submit-reply-text') );
			});
		});
	}

	function citadela_apply_comment_cancel_reply_link_events( $reviews_node ){
		var $comment_cancel_reply_link = $reviews_node.find('#cancel-comment-reply-link');
		$comment_cancel_reply_link.off('click');
		$comment_cancel_reply_link.on('click', function(){
			citadela_close_all_notifications( $reviews_node );
			citadela_add_rating_node( $reviews_node );
			var $submit_button = $reviews_node.find('#submit-review-button');
			$submit_button.attr( 'value', $submit_button.data('submit-review-text') );
		});
	}

	var _$rating_node = null;

	function citadela_remove_rating_node( $reviews_node ){
		var $rating_node = $reviews_node.find('.rating-wrapper');
		
		_$rating_node = _$rating_node === null ? $rating_node.clone( true ) : _$rating_node;
		_$rating_node.find('div.rating').remove();
		$rating_node.remove();
	}

	function citadela_add_rating_node( $reviews_node ){
		var $form = $reviews_node.find('form.comment-form');
		$form.prepend( _$rating_node );
		_$rating_node.append('<div class="rating"></div>');
		citadela_apply_review_rating( $reviews_node );
	}
	
	function citadela_reviews_form_validation( $reviews_node ){
		$reviews_node.find('#commentform #submit-review-button').on('click', function(e){
			e.preventDefault();
			citadela_close_all_notifications( $reviews_node )

			var $form = jQuery(this).parents('#commentform');
			var validation_success = true;
			
			// rating input validation
			var $rating_input = $form.find('input[name=rating]');
			var $rating = $rating_input.val();
			if( $rating_input.length && ! $rating ){
				$form.find('.rating-notification').show(200);
				validation_success = false;
			}

			// comment input validation
			var $comment_input = $form.find('textarea#comment');
			if( $comment_input.attr('required') == 'required' ){
				if( $comment_input.val() == '' ){
					$form.find('.general-notification').show(200);
					validation_success = false;		
				}
			}

			// name input validation
			var $name_input = $form.find('input#author');
			if( $name_input.length ){
				//name input is present
				if( $name_input.attr('required') == 'required' ){
					if( $name_input.val() == '' ){
						$form.find('.general-notification').show(200);
						validation_success = false;		
					}
				}

			}

			// email input
			var $email_input = $form.find('input#email');
			if( $email_input.length ){
				//email input is present
				if( $email_input.attr('required') == 'required' ){
					if( $email_input.val() == '' ){
						$form.find('.general-notification').show(200);
						validation_success = false;		
					}else{
						var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	  					var email_check = regex.test( $email_input.val() );
	  					if( ! email_check ){
	  						$form.find('.email-notification').show(200);
							validation_success = false;			
	  					}

					}
				}

			}

			if( validation_success ) {
				$form.submit();
				citadela_close_all_notifications( $reviews_node );
			}else{
				$form.addClass('validation-failed');
			}
		})
	}

	function citadela_close_all_notifications( $context ){
		$context.find('#commentform').removeClass('validation-failed');
		$context.find('.citadela-notification').each(function(){
			jQuery(this).hide(100);
		});
	}




	$document.ready(function(){
		var filters = advanced_filters.init();
		citadela = jQuery.extend(citadela, { advanced_filters: filters } );
		
	});

})(jQuery, jQuery(document));
