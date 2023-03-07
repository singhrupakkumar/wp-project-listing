var citadela = citadela || {};
citadela.controls = citadela.controls || {};

function gm_authFailure(){
	var apiBanner = document.createElement('div');
	var a = document.createElement('a');
	var linkText = document.createTextNode("Read more");
	a.appendChild(linkText);
	a.title = "Read more";
	a.href = "https://www.ait-themes.club/how-to-get-google-maps-api-key/";
	a.target = "_blank";

	apiBanner.className = "alert alert-info";
	var bannerText = document.createTextNode("Please check Google API key settings");
	apiBanner.appendChild(bannerText);
	apiBanner.appendChild(document.createElement('br'));
	apiBanner.appendChild(a);

	jQuery(".citadela-google-map.google-map-container").html(apiBanner);
};


(function($, $window, $document, undefined){

	"use strict";

	var _settings = typeof CitadelaDirectorySettings === 'undefined' ? {} : CitadelaDirectorySettings;
	citadela = jQuery.extend(citadela, _settings);

	var $context = jQuery('#wpbody');


	


	/**
	 * citadela.admin.controls.Ui
	 *
	 */
	var ui = citadela.controls.ui = {
		
		gpxMapObject: {
			provider: '',
			map: {},
			markers: [],
			polylines: [],
		},

		init: function()
		{
			ui.enhancedInputs(
				$context
					.find('.citadela-control, .citadela-user-settings, form#edittag, form#addtag')
			);
		},

		enhancedInputs: function($currentContext)
		{
			ui.map($currentContext);
			ui.gpxUploader($currentContext);
			if(jQuery.fn.colorpicker !== undefined){
				ui.colorpicker($currentContext);
			}

			ui.image($currentContext);
			ui.fontawesomeSelect($currentContext);
			ui.itemExtension($currentContext);
			ui.repeaterControl($currentContext);
			ui.featuredCategorySelectControl($currentContext);
			ui.galleryControl($currentContext);
		},
		galleryControl: function($currentContext){
			var $container = jQuery('.citadela_gallery-control', $currentContext);
			$container.on('citadelaGalleryInputInit', null, function(){
				var $thisContainer = jQuery(this);

				var Control = {
					container: null,
					galleryWrapper: null,
					addButton: null,
					mediaFrame: null,
					init() {
						let self = this;
						self.container = $thisContainer;
						self.galleryWrapper = $thisContainer.find('.gallery-images')
						self.addButton = $thisContainer.find('input.add-media');
						self.addButton.on('click', function(){
							var $btn = jQuery(this);
							self.addImages();
						});
						self.applySortable();
						self.applyDeletion();
					},
					addImages(){
						let self = this;
						
						if ( self.mediaFrame ) {
							self.mediaFrame.open();
							return;
						}

						// Create a new media frame
						self.mediaFrame = wp.media({
							multiple: 'add',
							library: {
								type: 'image',
							},
						});

						// on media selected
						self.mediaFrame.on( 'select', function() {
							var attachments = self.mediaFrame.state().get('selection').toJSON();
							attachments.forEach( (attachment) => {
								const id = attachment.id;
								let url = '';
								if( attachment.sizes && attachment.sizes.thumbnail ){
									url = attachment.sizes.thumbnail.url;
								}else{
									url = attachment.url;
								}

								if( url && attachment.type && attachment.type == 'image' ){
									const order_no = self.galleryWrapper.find('.image-wrapper').length + 1;
									const caption_label = self.galleryWrapper.data('caption-label');
									let $newImageWrapper = jQuery(
										`<div class="image-wrapper ui-sortable-handle">
											<div class="image">
												<input type="hidden" name="butterbean_citadela_item_options_setting__citadela_gallery_images[${order_no}][id]" value="${id}">
												<img src="${url}">
												<div class="caption">
													<label>${caption_label}</label>
													<input type="text" name="butterbean_citadela_item_options_setting__citadela_gallery_images[${order_no}][caption]" value="">
												</div>
											</div>
											<div class="delete"></div>
										</div>`
										);
									self.galleryWrapper.append( $newImageWrapper );
									self.applyDeletion( $newImageWrapper );
								}
							});
						});
						

						// preselect already chosen images
						/*self.mediaFrame.on('open', function(){
							var selection = self.mediaFrame.state().get('selection');
							[5834, 5833].forEach(function(media_id){
								var attachment = wp.media.attachment(media_id);
								attachment.fetch();
								selection.add(attachment ? [attachment] : []);
							});
						});*/

						self.mediaFrame.open();
					},
					applyDeletion( $newImageWrapper = null ){
						let self = this;
						if( $newImageWrapper ){
							$newImageWrapper.find('.delete').on('click', function(){
								$newImageWrapper.remove();
							});
						}else{
							self.container.find('.image-wrapper').each(function(){
								let $wrapper = jQuery(this);
								$wrapper.find('.delete').on('click', function(){
									$wrapper.remove();
								});
							});
						}
					},

					applySortable(){
						let self = this;
						self.galleryWrapper.sortable({
							items: '.image-wrapper',
							tolerance: 'pointer',
							handle: '.image',
						});
					},
				}
				Control.init();
			});

			$container.trigger('citadelaGalleryInputInit');
		},
		featuredCategorySelectControl: function($currentContext){
			var $container = jQuery('.citadela_featured_category_select-control', $currentContext);
			$container.on('citadelaFeaturedCategorySelectInit', null, function(){
			var $thisContainer = jQuery(this);

			var Control = {
				
				container: null,
				valueInput: null,

				init() {
					let self = this;

					self.container = $thisContainer;
					self.valueInput = $thisContainer.find('.selected-term');
					self.applyTermsClick();
					self.applyCategoryCheckboxClick();

					// manage categories added by ajax
					jQuery( document ).ajaxSuccess(function( event, xhr, settings ) {
						if(settings.action == "add-citadela-item-category" ) {
							self.addedNewCategory();
						}
					});

					self.checkNotification();

				},
				applyTermsClick(){
					let self = this;
					self.container.find('.term-wrapper').each(function(){
						self.applyTermWrapperClick( jQuery(this) );
					});
				},
				applyTermWrapperClick( $termWrapper ){
					var self = this;
					$termWrapper.off('click');
					$termWrapper.on('click', function(){
						self.toggleSelect( jQuery(this) );
					});
				},
				toggleSelect( $termWrapper ){
					let self = this;
					if( $termWrapper.hasClass('selected') ){
						self.clearSelected();
						self.setTerm( $termWrapper );
					}else{
						self.clearSelected();
						$termWrapper.addClass('selected');
						self.setTerm( $termWrapper );
					}
				},
				setTerm( $termWrapper ){
					let self = this;
					if( $termWrapper.hasClass('selected') ){
						self.valueInput.val( $termWrapper.data('term_id') );
					}else{
						self.valueInput.val('');
					}
				},
				clearSelected(){
					let self = this;
					self.container.find( '.term-wrapper' ).removeClass('selected');
				},
				applyCategoryCheckboxClick(){
					let self = this;
					var $categories_list = jQuery('#citadela-item-categorychecklist');
					$categories_list.find('input[name="tax_input[citadela-item-category][]"]').each(function(){
						self.categoryCheckboxClickEvent( jQuery(this) );
					});
				},
				categoryCheckboxClickEvent( $checkbox ){
					var self = this;
					$checkbox.on('click', function(){
						
						var term_id = jQuery(this).val();
						
						if( jQuery(this).prop('checked') ){
							//category is selected
							
							//if category isnt in the list, add it
							if( ! self.container.find(`.term-wrapper.term-${term_id}`).length ){
								var term_name = "", term_meta = [];
								var term_data = citadela.itemCategoryTerms[term_id];
								
								if( term_data == undefined ){
									term_name = jQuery(this).parent().text();
								}else{
									term_name = term_data.term_name;
									term_meta['category_icon'] = term_data.term_meta['category_icon'];
									term_meta['category_color'] = term_data.term_meta['category_color'];
								}
								

								self.addNewTermWrapper(term_id, term_name, term_meta);
							}
						}else{
							//category is deselected
							self.removeTermWrapper(term_id);
						}
					});
				},
				addedNewCategory(){
					let self = this;
					var $categories_list = jQuery('#citadela-item-categorychecklist');
					var $newCategoryCheckbox = $categories_list.find('input[name="tax_input[citadela-item-category][]"]').first();
					var term_id = $newCategoryCheckbox.val();
					var term_name = $newCategoryCheckbox.parent().text();
					// check if new category was really added, so check already existing terms in our options
					if( ! self.container.find(`.term-wrapper.term-${term_id}`).length ){
						self.categoryCheckboxClickEvent( $newCategoryCheckbox );
						self.addNewTermWrapper(term_id, term_name);

					}

				},
				addNewTermWrapper( term_id, term_name, meta = [] ){
					var self = this;
					var html_template = self.getWrapperTemplate();
					html_template = html_template.replace(/{term_id}/g, term_id);
					html_template = html_template.replace('{term_name}', term_name.trim());
					html_template = html_template.replace('{category_icon}', meta['category_icon'] != undefined ? meta['category_icon'] : 'fas fa-circle');
					html_template = html_template.replace('{category_color}', meta['category_color'] != undefined ? meta['category_color'] : '#0085ba');
					html_template = html_template.replace('{selected_class}', term_id == self.valueInput.val() ? 'selected' : '' );
					var $new_container = jQuery( html_template );
					self.container.find('.input-wrapper').append( $new_container );
					//set click event for term wrapper
					self.applyTermWrapperClick( $new_container );
					self.checkNotification();
				},
				removeTermWrapper( term_id ){
					var self = this;
					self.container.find(`.term-wrapper.term-${term_id}`).remove();
					self.checkNotification();
				},
				checkNotification(){
					var self = this;
					if( self.container.find('.term-wrapper').length ){
						self.hideNotification();
					}else{
						self.showNotification();
					}
				},
				showNotification(){
					var self = this;
					// remove notification message if was there
					self.container.find('.no-posts-notification').show();
				},
				hideNotification(){
					var self = this;
					// remove notification message if was there
					self.container.find('.no-posts-notification').hide();
				},
				getWrapperTemplate(){
					var self = this;
					return self.container.find('script.featured-category-select-control-template').html();
				}
			};
			Control.init();
			});
			$container.trigger('citadelaFeaturedCategorySelectInit');
		},
		itemExtension: function($currentContext)
		{

			var $container = jQuery('.citadela-item-extension-inputs', $currentContext);
			var $repeaterControl = $container; // the same container is also repeaterControl
			
			$container.on('itemExtensionInputTypeChange', null, function(){
				var $thisContainer = jQuery(this);
				var $wrapper = $thisContainer.find('.input-settings-wrapper');
				$thisContainer.find('select.type-input').each(function(e){
					var $select = jQuery(this);
					
					// show / hide settings parts when input type changed
					$select.off('change');
					$select.on('change', function(e){

						var $settings = jQuery(this).parents('.input-settings-wrapper');
						var $row = $settings;
						var $input_type = jQuery(this).val();
						
						switch( $input_type ) {
							case "select":
								//show
								[ 	
									'.input-choices',
								  	'.choices-label',
								  	'.filter-settings',
								].map( ( className, i ) => { $settings.find( className ).slideDown(300) } );

								//hide
								[ 	
									'.input-filters-group-name',
								  	'.url-settings',
								  	'.number-settings',
								].map( ( className, i ) => { $settings.find( className ).slideUp(300) } );

							break;

							case "citadela_multiselect":
								//show
								[ 
									'.input-choices',
								  	'.filter-settings',
								].map( ( className, i ) => { $settings.find( className ).slideDown(300) } );

								//hide
								[ 
									'.choices-label',
								  	'.input-filters-group-name',
								  	'.url-settings',
								  	'.number-settings',
								].map( ( className, i ) => { $settings.find( className ).slideUp(300) } );

							break;

							case "citadela_number":
								//show
								[ 
									'.number-settings',
								].map( ( className, i ) => { $settings.find( className ).slideDown(300) } );

								//hide
								[ 
									'.choices-label',
								  	'.input-filters-group-name',
								  	'.url-settings',
								  	'.input-choices',
								  	'.filter-settings',
								].map( ( className, i ) => { $settings.find( className ).slideUp(300) } );
							break;

							case "citadela_url":
								//show
								[ 
									'.url-settings',
								].map( ( className, i ) => { $settings.find( className ).slideDown(300) } );

								//hide
								[ 
									'.choices-label',
								  	'.input-filters-group-name',
								  	'.input-choices',
								  	'.filter-settings',
								  	'.number-settings',
								].map( ( className, i ) => { $settings.find( className ).slideUp(300) } );
							break;

							case "checkbox":
								//show
								[ 
								  	'.filter-settings',
								].map( ( className, i ) => { $settings.find( className ).slideDown(300) } );
								
								//hide
								[ 
								  	'.input-choices',
								  	'.choices-label',
									'.url-settings',
								  	'.number-settings',
								].map( ( className, i ) => { $settings.find( className ).slideUp(300) } );
								
								if( $settings.find('input.use-as-filter-input').prop('checked') ){
									$settings.find('.input-filters-group-name').slideDown(300);
										$settings.find('.input-filters-group-name').find('.filters-group-name-input').attr('required', true);
								}else{
									$settings.find('.input-filters-group-name').slideUp(300);
									$settings.find('.input-filters-group-name').find('.filters-group-name-input').attr('required', false);
								}

							break;

							default:
								// simple input type, use only default inputs, close all specific inputs
								[ 
									'.input-choices',
									'.choices-label',
									'.url-settings',
									'.number-settings',
									'.filter-settings',
									'.input-filters-group-name',
								].map( ( className, i ) => { $settings.find( className ).slideUp(300) } );

								//disable required status for hidden inputs
								$settings.find('.input-filters-group-name').find('.filters-group-name-input').attr('required', false);
							
						}

						var repeaterControl = $repeaterControl.data('repeaterControl');	
						repeaterControl.applyRequiredFieldsValidation( $row );
						repeaterControl.checkForRequiredFieldsValidationErrors( $row );

					});
				});
				
			});
			$container.trigger('itemExtensionInputTypeChange');

			//we need to update events for some Item Extension options
			$repeaterControl.on( 'citadelaRepeaterUpdateEvents', function( e, $row ){
				
				if( $row === undefined ) $row = $container.find('.repeater-row');

				$row.each( function(){

					var $checkbox_use_as_filter = jQuery(this).find('input.use-as-filter-input');

					$checkbox_use_as_filter.off('change');
					$checkbox_use_as_filter.on('change', function(){
						var $currentRow = jQuery(this).parents('.input-settings-wrapper');
						var $setting_filters_group_name = $currentRow.find('.input-filters-group-name');
						var input_type = $currentRow.find('select.type-input').val();
						var checked = jQuery(this).prop('checked');
						if( checked && input_type == 'checkbox' ){
							$setting_filters_group_name.slideDown(300);
							$setting_filters_group_name.find('.filters-group-name-input').attr('required', true);
						}else{
							$setting_filters_group_name.slideUp(300);
							$setting_filters_group_name.find('.filters-group-name-input').attr('required', false);
						}
						var repeaterControl = $repeaterControl.data('repeaterControl');	
						repeaterControl.checkForRequiredFieldsValidationErrors( $row );
					});
				});

			});


			// triggered when new Repeater row is added
			jQuery( document.body ).on('citadelaRepeaterItemAdded', function( e, $added_container ){

				//check Item Extension inputs
				$container.trigger('itemExtensionInputTypeChange');

				//after added Repeater row in Item Extension settings, check also which additional options would be visible and would have some value

				// Select and Radio types
				if( jQuery.inArray( $added_container.find('select.type-input').val(), [ 'select', 'citadela_multiselect' ] ) !== -1 ){
					// show Choises options
					$added_container.find('.setting.input-choices').show();
				}else{
					// hide Choises options and reset input value
					$added_container.find('.setting.input-choices').hide();
					$added_container.find('.setting.input-choices').find('textarea').val('');
				}

				// Number type
				if( $added_container.find('select.type-input').val() == 'citadela_number' ){
					$added_container.find('.settings-group.number-settings').show();
				}else{
					$added_container.find('.settings-group.number-settings').hide();
					$added_container.find('.settings-group.number-settings').find( '.min-input' ).val('');
					$added_container.find('.settings-group.number-settings').find( '.max-input' ).val('');
					$added_container.find('.settings-group.number-settings').find( '.unit-input' ).val('');
					$added_container.find('.settings-group.number-settings').find( '.unit-position-input' ).val('right');
				}


				// Url type
				if( $added_container.find('select.type-input').val() == 'citadela_url' ){
					$added_container.find('.settings-group.url-settings').show();
				}else{
					$added_container.find('.settings-group.url-settings').hide();
					$added_container.find('.settings-group.url-settings').find( '.use-url-label-input' ).prop( 'checked', false );
				}


				// Advanced Filters checkbox
				if( jQuery.inArray( $added_container.find('select.type-input').val(), [ 'citadela_multiselect', 'citadela_number', 'checkbox', 'select' ] ) !== -1 ){
					$added_container.find('.settings-group.filter-settings').show();
				}else{
					$added_container.find('.settings-group.filter-settings').hide();
					$added_container.find('.settings-group.filter-settings').find( '.filter-input' ).prop( 'checked', false );
				}

			});


		},
		repeaterControl: function($currentContext)
		{
			var $container = jQuery('.citadela-repeater-control', $currentContext);
			$container.on('citadelaRepeaterInit', null, function(){
				var $thisContainer = jQuery(this);

				$thisContainer.on('citadelaRepeaterNameUpdated', function( e, $row, $nameInput ){
					$row.find('.heading .part-title').html( $nameInput.val() );
				});
				var Control = {
					init() {
						let self = this;
						
						self.applyAddNewRowAction();
						self.applySortable();
						
						$thisContainer.on('citadelaRepeaterUpdateEvents', function( e, $row ){
							//update events only for defined row, or for all rows in repeater
							self.applyDuplicateRowAction( $row ? $row : null );
							self.applyRemoveActions( $row ? $row : null );
							self.applyNameKeyPairs( $row ? $row : null );
							self.applyCollapse( $row ? $row : null );
							self.applyRequiredFieldsValidation( $row ? $row : null );
						});
						
						$thisContainer.trigger('citadelaRepeaterUpdateEvents');
						
						$thisContainer.data('repeaterControl', self );

					},
					applyDuplicateRowAction( $row = null ){
						let self = this;
						$container = $row ? $row : $thisContainer;
						$container.find(".repeater-duplicate-button").each(function(){
							var $duplicate_button = jQuery(this);
							$duplicate_button.on('click', function(){
								self.duplicateInput( jQuery(this).parents('.repeater-row') );
							});
						});
						
					},
					applyAddNewRowAction(){
						let self = this;
						var $addNewButton = $thisContainer.find(".repeater-add-button");
						
						$addNewButton.on( 'click', function(e){
							var html_template = $thisContainer.find('.citadela-repeater-template').html();
							var $new_container = jQuery( html_template );
							self.addNewInput( $new_container );
						});
					},
					applyRemoveActions( $row = null ){
						let self = this;
						$container = $row ? $row : $thisContainer;
						$container.find(".remove-button").each( function(e){
							jQuery(this).show();
							jQuery(this).on( 'click', function(){
								ui.removeNode( jQuery(this).parents( '.repeater-row' ) );
							});
						});

					},
					addNewInput( $new_container ) {
						let self = this;
						$thisContainer.find(".repeater-inputs").append($new_container);
						
						$new_container.find('.repeater-input').each(function(e, input){
							var $input = jQuery(input);
							var tagname =  $input.prop('tagName').toLowerCase();
							
							//set new name attributes
							const newInputName = self.getNewInputName( jQuery(this) );
							jQuery(this).attr( 'name', newInputName );
							jQuery(this).attr( 'id', newInputName );
			
							// clear inputs data for new row
							if( tagname === 'select' ){
								//select first value
								$input.find('option').first().prop('selected', true);
							}else if( tagname === 'input' ){
								//reset text inputs
								$input.val('');
							}else if( tagname === 'textarea' ){
								//reset textarea inputs
								$input.val('');
							}

						});
						
						self.openRow( $new_container );
						$thisContainer.trigger('citadelaRepeaterUpdateEvents', [ $new_container ] );
						jQuery( document.body ).trigger('citadelaRepeaterItemAdded', [ $new_container ] );
					},

					duplicateInput( $source_container ) {
						let self = this;
						var $new_container = $source_container.clone();

						$source_container.after($new_container);

						// loop through repeater inputs: input, select, checkbox....
						$new_container.find('.repeater-input').each(function(e, input){
							var $input = jQuery(input);
							var tagname =  $input.prop('tagName').toLowerCase();
							
							//set new name attributes
							const newInputName = self.getNewInputName( jQuery(this) );
							jQuery(this).attr( 'name', newInputName );
							jQuery(this).attr( 'id', newInputName );
						
							//reset only key input if duplicated row
							if( $input.hasClass('pair-key-input') && $input.val() != '' ){
								var reservedIdentifiers = self.getReservedIdentifiers( $input.parents('.citadela-repeater-control') );
								$input.val( ui.sanitizeIdentifier( $input.val()+"_2", reservedIdentifiers ) );
							}
							
							// fix for known jQuery bug where selected value in <select> is not cloned using clone() function
							$source_container.find('select').each(function(e){
								$new_container.find('select').eq(e).val( jQuery(this).val() );
							});
						

						});

						
						$thisContainer.trigger('citadelaRepeaterUpdateEvents', [ $new_container ] );
						jQuery( document.body ).trigger('citadelaRepeaterItemAdded', [ $new_container ] );
					},
					getNewInputName( $input ) {
						var count = parseInt( $thisContainer.find('.repeater-row').length );
						var schema = $input.data( 'id-schema' );
						return schema.replace( '{citadela_input_key}', `input_${count + 1 }` );
					},
					checkMinimalRows(){
						let self = this;
						var rows = $thisContainer.find('.repeater-row').length;
						if( rows - 1 == 1 ){
							$thisContainer.find('.repeater-row .remove-button').hide();
						}else{
							$thisContainer.find('.repeater-row .remove-button').show();
						}
					},
					/*
					* generate codename into input from text in another input
					*/
					applyNameKeyPairs( $row = null ){
						let self = this;
						$container = $row ? $row : $thisContainer.find('.repeater-row');
						$container.each(function(e){
							var $row = jQuery(this);
							var $nameInput = jQuery(this).find('.pair-name-input');
							var $keyInput = jQuery(this).find('.pair-key-input');

							$nameInput.on('blur', function(e){
								var $nameInput = jQuery(this);

								//trigger event
								jQuery( $thisContainer ).trigger('citadelaRepeaterNameUpdated', [ $row, $nameInput ] );
								
								if( $keyInput.val() == '' ){
									var reservedIdentifiers = self.getReservedIdentifiers( $nameInput.parents('.citadela-repeater-control') );
									var text = $nameInput.val();
									$keyInput.val( ui.sanitizeIdentifier( text, reservedIdentifiers ) );
								}
							});

							$keyInput.on('blur', function(e){
								var $keyInput = jQuery(this);
								var reservedIdentifiers = self.getReservedIdentifiers( $nameInput.parents('.citadela-repeater-control') );
								var text = $nameInput.val();
								if( $keyInput.val() == '' ){
									$keyInput.val( ui.sanitizeIdentifier( text, reservedIdentifiers ) );
								}else{
									//if key input isn't empty, check if the key is used more times a and run sanitization for the key 
									if( reservedIdentifiers.filter( ( v ) => ( v === $keyInput.val() ) ).length > 1 ){
										$keyInput.val( ui.sanitizeIdentifier( $keyInput.val(), reservedIdentifiers ) );
									}else{
										$keyInput.val( ui.sanitizeIdentifier( $keyInput.val() ) );
									}
									
								}
							});

						});
					},
					getReservedIdentifiers( $container ){
						var reservedIdentifiers = [];
						$container.find('input.pair-key-input').each(function(){
							var val = jQuery(this).val();
							if( val !== '' ) reservedIdentifiers.push( val );
						});
						return reservedIdentifiers;
					},
					applyRequiredFieldsValidation( $row = null ){
						let self = this;
						$container = $row ? $row : $thisContainer.find('.repeater-row');
						$container.each(function(){
							var $row = jQuery(this);
							$row.find('.repeater-input').each(function(){
								var $input = jQuery(this);
								if( $input.attr('required') ){
									$input.on('blur', function(){
										self.checkForRequiredFieldsValidationErrors( $row );
									});
								}
							});
						});
					},
					validateRequiredInputs( $row = null ){
						let self = this;
						$container = $row ? $row : $thisContainer.find('.repeater-row');
						$container.each(function(){
							var $row = jQuery(this);
							$row.find('.repeater-input').each(function(){
								var $input = jQuery(this);
								if( $input.attr('required') && $input.val() == '' ){
									jQuery(this).addClass('validation-failed');
								}else{
									jQuery(this).removeClass('validation-failed');
								}
							});
						});	
					},
					checkForRequiredFieldsValidationErrors( $row = null ){
						let self = this;
						$container = $row ? $row : $thisContainer.find('.repeater-row');
						$container.each(function(){
							var $row = jQuery(this);

							self.validateRequiredInputs( $row );
							
							var invalide_inputs = $row.find('.validation-failed');
							if( invalide_inputs.length ){
								$row.find('.part-validation-message').show();
							}else{
								$row.find('.part-validation-message').hide();
							}
							
						});
					},
					applyCollapse( $row = null ) {
						let self = this;
						$container = $row ? $row : $thisContainer.find('.repeater-row');
						$container.each(function(){
							var $row = jQuery(this);
							var $handle = jQuery(this).find('.handle');
							var $content = jQuery(this).find('.content');
							$handle.on('click', function(e){
								if( $row.hasClass('closed') ){
									self.openRow( $row );
								}else{
									self.closeRow( $row );
								}
							});
						});
					},
					openRow( $row ){
						$row.find('.content').slideDown(300);
						$row.removeClass('closed');
					},
					closeRow( $row ){
						let self = this;
						$row.find('.content').slideUp(300);
						$row.addClass('closed');
						self.checkForRequiredFieldsValidationErrors( $row );
					},
					applySortable() {
						let self = this;
						$thisContainer.sortable({
							items: '.repeater-row',
							handle: '.handle',
							delay: 150,
						});
					},
				}
				Control.init();

			});
			
			$container.trigger('citadelaRepeaterInit');
		},

		fontawesomeSelect: function($currentContext)
		{
			var $container = jQuery('.citadela-fontawesome-select-container', $currentContext);
			$container.on('fontawesomeSelectinit', null, function(){
				var $thisContainer = jQuery(this);
				var $iconPicker = $thisContainer.find('.selected-icon');
				var $valueInput = $thisContainer.find('input.iconpicker');
				var $iconPickerHolder = $thisContainer.find('.iconpicker-holder');
				var selectedValue = $valueInput.val();
				$valueInput.ctdlIconpicker({
					selected: selectedValue,
				});

				$thisContainer.find('.iconpicker-item').on('click', function(e){
					e.preventDefault();
					$iconPicker.find('i').attr('class', $valueInput.val());
					$iconPickerHolder.removeClass('opened').slideUp();
				});

				$iconPicker.on('click', function(e){
					e.preventDefault();
					if($iconPickerHolder.hasClass('opened')){
						$iconPickerHolder.removeClass('opened').slideUp();
					}else{
						$iconPickerHolder.addClass('opened').slideDown();
						$iconPickerHolder.find('.iconpicker-search').focus();
					}

				});
			});

			$container.trigger('fontawesomeSelectinit');
		},

		image: function($currentContext)
		{
			var $container = jQuery('.citadela-control-image', $currentContext);
			$container.on('imageinit', null, function(){
				if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
					var citadela_image_uploader;
					var $thisContainer = jQuery(this);
					var $imageUrlInput = $thisContainer.find('input.image-url');
					var $imagePreviewHolder = $thisContainer.find('.citadela-image-preview-container');
					var $deleteButton = $thisContainer.find('.citadela-delete-image-button');

					$deleteButton.on('click', function(e) {
						e.preventDefault();

						$imageUrlInput.val("");
						$imagePreviewHolder.html('');
						$deleteButton.hide();

				        return false;
					});

					$thisContainer.find('.citadela-select-image-button').on('click', function(e) {
						e.preventDefault();
						var $button = jQuery(this);
				        //customize wp.media object
				        citadela_image_uploader = wp.media.frames.file_frame = wp.media({
				            multiple: false
				        });

				        citadela_image_uploader.on('select', function() {
				        	$button.blur();
				            var attachment = citadela_image_uploader.state().get('selection').first().toJSON();
				            var value = attachment.url;
				            if( $imageUrlInput.data('saveas') == 'id' ){
				            	value = attachment.id;
				            }
				            $imageUrlInput.val( value );
				            $imagePreviewHolder.html('<img src="' + attachment.url + '">');
				            $deleteButton.show();
				        });

				        //open uploader dialog
				        citadela_image_uploader.open();

				        return false;
					});
				}
			});

			$container.trigger('imageinit');
		},
		colorpicker: function($currentContext)
		{
			var $container = jQuery('.citadela-control-colorpicker', $currentContext);
			$container.on('colorpickerinit', null, function(){
				var $thisContainer = jQuery(this);
				var $colorpickerContainer = $thisContainer.find('.citadela-colorpicker-container');
				$colorpickerContainer.colorpicker();
			});

			$container.trigger('colorpickerinit');
		},

		map: function($currentContext)
		{
			var $container = jQuery('.citadela_map-control', $currentContext);

			var mapProvider = $container.data('map-provider');

			if (mapProvider == 'openstreetmap') {
				ui.openstreetMap($container);
			} else {
				ui.googleMap($container);
			}
		},

		googleMap: function($container)
		{
			$container.on('mapinit', null, function(){
				var $thisContainer = jQuery(this);
				var $map = $thisContainer.find('.google-map-container');

				var $addressField = $thisContainer.find('input.address-input');
				var $addressSearchBtn = $thisContainer.find('input.search-button');
				var $latitudeField = $thisContainer.find('input.latitude-input');
				var $longitudeField = $thisContainer.find('input.longitude-input');
				var $streetviewControl = $thisContainer.find('input.streetview-input');
				var $swHeadingControl = $thisContainer.find('input.swheading-input');
				var $swPitchControl = $thisContainer.find('input.swpitch-input');
				var $swZoomControl = $thisContainer.find('input.swzoom-input');

				var $messageContainer = $thisContainer.find('.citadela-google-map-message');

				var mapdata = {
					address: $addressField.val(),
					latitude: $latitudeField.val() ? parseFloat($latitudeField.val()) : 1,
					longitude: $longitudeField.val() ? parseFloat($longitudeField.val()) : 1,
					streetview: $streetviewControl.prop("checked"),
					swheading: $swHeadingControl.val() ? parseFloat($swHeadingControl.val()) : 0,
					swpitch: $swPitchControl.val() ? parseFloat($swPitchControl.val()) : 0,
					swzoom: $swZoomControl.val() ? parseFloat($swZoomControl.val()) : 0,
				}

				// init map

				var position = new google.maps.LatLng(mapdata.latitude, mapdata.longitude);

				$map.height(300);
				$map.css('width', '100%');

				var map = new google.maps.Map( $map.get(0), {
								center: position,
								zoom: 17,
								mapTypeId: google.maps.MapTypeId.ROADMAP,
								streetViewControl: false,
							});

				var streetview = new google.maps.StreetViewPanorama(  $map.get(0), {
								position: position,
								pov: {
									heading: mapdata.swheading,
									pitch: mapdata.swpitch
								},
								zoom: mapdata.swzoom
							});

				var marker = new google.maps.Marker({
								map: map,
								position: position,
								draggable: true,
							});

				var geocoder = new google.maps.Geocoder();

				//google map events
				map.addListener('click', function(event) {
					map.panTo({lat: event.latLng.lat(), lng: event.latLng.lng() });
					marker.setPosition(event.latLng);
					$latitudeField.val(event.latLng.lat());
					$longitudeField.val(event.latLng.lng());

				});

				//marker events
				marker.addListener('drag', function() {
					var pos = marker.getPosition();
					$latitudeField.val(pos.lat());
					$longitudeField.val(pos.lng());
				});

				marker.addListener('dragend', function() {
					var pos = marker.getPosition();
					map.panTo({lat: pos.lat(), lng: pos.lng() });
				});

				//streetview events

				streetview.addListener('position_changed', function() {
					$latitudeField.val(streetview.getPosition().lat());
					$longitudeField.val(streetview.getPosition().lng());
				});

				streetview.addListener('pov_changed', function() {
					$swHeadingControl.val(streetview.getPov().heading);
					$swPitchControl.val(streetview.getPov().pitch);
					$swZoomControl.val(streetview.getPov().zoom);
				});


				//find address button
				$addressSearchBtn.click(function(e){
					e.preventDefault();
					$messageContainer.slideUp();
					var address = $addressField.val();
					if ( !address || !address.length ) return;

					geocoder.geocode({ 'address': address }, function(results, status) {
						if (status === 'OK') {
							map.setCenter(results[0].geometry.location);
							marker.setPosition(results[0].geometry.location);
							streetview.setPosition(results[0].geometry.location);
							$latitudeField.val(results[0].geometry.location.lat());
							$longitudeField.val(results[0].geometry.location.lng());
							$latitudeField.trigger('keyup');
							$longitudeField.trigger('keyup');
						} else {
							$messageContainer.slideDown();
						}
					});
				});

				//latitude and longitude input events
				$latitudeField.on('keyup', function(e){
					jQuery(this).css({'border-color': ''});
					jQuery(this).parent().find('i').remove();
					if(jQuery(this).val() !== '' && jQuery(this).val().match(/^-?\d*(\.\d+)?$/) && jQuery(this).val() >= -90 && jQuery(this).val() <= 90){
						jQuery(this).parent().append('<i class="fa fa-check-circle" style="color: #88B44E"></i>');
					} else {
						jQuery(this).css({'border-color': '#BE6565'});
						jQuery(this).parent().append('<i class="fa fa-times-circle" style="color: #BE6565"></i>');
					}
				}).trigger('keyup');

				$longitudeField.on('keyup', function(e){
					jQuery(this).css({'border-color': ''});
					jQuery(this).parent().find('i').remove();
					if(jQuery(this).val() !== '' && jQuery(this).val().match(/^-?\d*(\.\d+)?$/) && jQuery(this).val() >= -180 && jQuery(this).val() <= 180){
						jQuery(this).parent().append('<i class="fa fa-check-circle" style="color: #88B44E"></i>');
					} else {
						jQuery(this).css({'border-color': '#BE6565'});
						jQuery(this).parent().append('<i class="fa fa-times-circle" style="color: #BE6565"></i>');
					}
				}).trigger('keyup');


				// initial display of the container
				if(mapdata.streetview){
					if(typeof streetview.setVisible == "function"){
						streetview.setVisible(true);
					}
				} else {
					if(typeof streetview.setVisible == "function"){
						streetview.setVisible(false);
					}
				}

				//handle switch between map and streetview
				$streetviewControl.change(function(){
					if($streetviewControl.prop('checked')){
						if(typeof streetview.setVisible == "function"){
							streetview.setVisible(true);
						}
					}else{
						if(typeof streetview.setVisible == "function"){
							streetview.setVisible(false);
						}
					}

				});




			});

			$container.trigger('mapinit');

			//HOTFIX for butterbean navigation click event - google maps streetview not displayed if initialized in hidden div of butterbean tab
			jQuery('.butterbean-nav').find('li').on('click', function(){
				jQuery('.citadela_map-control').find('.google-map-container').each(function(){
					var $map = jQuery(this);
					var map = $map.get(0);
					google.maps.event.trigger(map, 'resize');
				});
			});
		},

		openstreetMap: function($container)
		{
			var map = null;
			$container.on('mapinit', null, function(){
				var $thisContainer = jQuery(this);
				var $map = $thisContainer.find('.google-map-container');

				var $addressField = $thisContainer.find('input.address-input');
				var $addressSearchBtn = $thisContainer.find('input.search-button');
				var $latitudeField = $thisContainer.find('input.latitude-input');
				var $longitudeField = $thisContainer.find('input.longitude-input');
				var $streetviewControl = $thisContainer.find('input.streetview-input');

				var $messageContainer = $thisContainer.find('.citadela-google-map-message');

				var mapdata = {
					address: $addressField.val(),
					latitude: $latitudeField.val() ? parseFloat($latitudeField.val()) : 1,
					longitude: $longitudeField.val() ? parseFloat($longitudeField.val()) : 1,
				}

				var position = L.latLng(mapdata.latitude, mapdata.longitude);

				$map.height(300);
				$map.css('width', '100%');

				$streetviewControl.attr("disabled", true);

				map = L.map($map.get(0));

				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png?', {
					attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
				}).addTo(map);

				// hotfix to reload map when it is immediately opened in first butterbean tab
				map.on('load', function(e) {
					setTimeout(function() {
						map.invalidateSize();
					}, 300);
				});

				map.setView(position, 17)

				var marker = L.marker(position, {
					draggable: true,
					autoPan: true
				}).addTo(map);

				map.on('click', function(e) {
					map.panTo(e.latlng);
					marker.setLatLng(e.latlng);
					$latitudeField.val(e.latlng.lat);
					$longitudeField.val(e.latlng.lng);
				});

				marker.on('move', function(e) {
					$latitudeField.val(e.latlng.lat);
					$longitudeField.val(e.latlng.lng);
				});

				marker.on('moveend', function(e) {
					map.panTo(e.target.getLatLng());
				});

				$addressSearchBtn.click(function(e) {
					e.preventDefault();
					$messageContainer.slideUp();

					var address = $addressField.val();
					if ( !address || !address.length ) return;

					$.getJSON('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + address, function(data) {
						if (data.length == 0) {
							$messageContainer.slideDown();
							return;
						}
						var item = data[0];

						var foundPosition = L.latLng(item.lat, item.lon);
						map.panTo(foundPosition);
						marker.setLatLng(foundPosition);
						$latitudeField.val(item.lat);
						$longitudeField.val(item.lon);
					});
				});

				//latitude and longitude input events
				$latitudeField.on('keyup', function(e){
					jQuery(this).css({'border-color': ''});
					jQuery(this).parent().find('i').remove();
					if(jQuery(this).val() !== '' && jQuery(this).val().match(/^-?\d*(\.\d+)?$/) && jQuery(this).val() >= -90 && jQuery(this).val() <= 90){
						jQuery(this).parent().append('<i class="fa fa-check-circle" style="color: #88B44E"></i>');
					} else {
						jQuery(this).css({'border-color': '#BE6565'});
						jQuery(this).parent().append('<i class="fa fa-times-circle" style="color: #BE6565"></i>');
					}
				}).trigger('keyup');

				$longitudeField.on('keyup', function(e){
					jQuery(this).css({'border-color': ''});
					jQuery(this).parent().find('i').remove();
					if(jQuery(this).val() !== '' && jQuery(this).val().match(/^-?\d*(\.\d+)?$/) && jQuery(this).val() >= -180 && jQuery(this).val() <= 180){
						jQuery(this).parent().append('<i class="fa fa-check-circle" style="color: #88B44E"></i>');
					} else {
						jQuery(this).css({'border-color': '#BE6565'});
						jQuery(this).parent().append('<i class="fa fa-times-circle" style="color: #BE6565"></i>');
					}
				}).trigger('keyup');

				//HOTFIX for butterbean navigation click event - google maps streetview not displayed if initialized in hidden div of butterbean tab
				jQuery('.butterbean-nav').find('li').on('click', function(){
					jQuery('.citadela_map-control').find('.google-map-container').each(function(){
						map.invalidateSize();
					});
				});

			});

			$container.trigger('mapinit');

		},

		gpxOpenstreetMap: function($container)
		{
			var map = null;
			$container.on('gpxmapinit', null, function(){
				var $thisContainer = $(this);
				ui.gpxMapObject.provider = 'openstreetmap';

				var $map = $thisContainer.find('.map-container');
				$map.height(300);
				$map.css('width', '100%');

				map = L.map($map.get(0));

				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png?', {
					attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
				}).addTo(map);

				// hotfix to reload map when it is immediately opened in first butterbean tab
				map.on('load', function(e) {
					setTimeout(function() {
						map.invalidateSize();
					}, 300);
				});
				ui.gpxMapObject.map = map;

				ui.drawGpxTrack( ui.getGpxData(), map, 'openstreetmap');


				//HOTFIX for butterbean navigation click event
				$('.butterbean-nav').find('li').on('click', function(){
					$('.gpx-map-view').find('.map-container').each(function(){
						
						ui.fitGpxMap();
					});
				});

			});

			$container.trigger('gpxmapinit');

		},

		removeGpxMarkers: function(){
			const markers = ui.gpxMapObject.markers;
			const map = ui.gpxMapObject.map;
			const provider = ui.gpxMapObject.provider;

			$.each( markers, function( key, marker ){ 
				if( provider == 'openstreetmap' ){
					marker.remove();
				}
			});

			ui.gpxMapObject.markers = [];
		},
		
		removeGpxTracks: function(){
			const polylines = ui.gpxMapObject.polylines;
			const map = ui.gpxMapObject.map;
			const provider = ui.gpxMapObject.provider;

			$.each( polylines, function( key, polyline ){ 
				if( provider == 'openstreetmap' ){
					polyline.remove();
				}
			});

			ui.gpxMapObject.polylines = [];
		},

		resetGpxMapData: function() {
			ui.removeGpxMarkers();
			ui.removeGpxTracks();
		},

		fitGpxMap: function() {
			const provider = ui.gpxMapObject.provider;
			const map = ui.gpxMapObject.map;

			if( provider == 'openstreetmap'){
				map.invalidateSize();
				let group = new L.featureGroup( ui.gpxMapObject.markers.concat(ui.gpxMapObject.polylines) );
				map.fitBounds( group.getBounds() );
			}
		},

		drawGpxTrack: function( data, map, provider ){
			
			//remove previous markers and tracks
			ui.resetGpxMapData();

			// get data related to track, stored in array
			const gpxData = data;

			$.each( gpxData, ( key, track ) => {
				let segments = [];

				// compatibility with track.points data where were stored all points from one gpx file one by one, 
	        	// now are points stored separately to segments in case the one track consists from more parts, thus store also points of one single track as one segment 
				if( track.points && ! track.data ){
					track.data = [];
					track.data.push( track.points );
				}

	            if( track.data ){
	                track.data.forEach( segment => {
	                    let segment_points = [];        
	                    segment.forEach( trackPoint => {
	                        segment_points.push( [ trackPoint.lat, trackPoint.lng ] )
	                    });
	                    segments.push( segment_points );
	                });
					
					
					$.each( segments, ( ( key, segment ) => {
						let points = [];
						$.each( segment, ( ( key, point ) => {
							points.push( [ point[0], point[1] ] );
						}) );
						if( provider == 'openstreetmap'){
							var polyline = L.polyline(points, {color: 'red'}).addTo(map);
							ui.gpxMapObject.polylines.push( polyline );
						}
					}) );
					
					ui.addTrackMarkers( track );

				}
			});

			//focus map to drawn tracks
			ui.fitGpxMap();
		},

		addTrackMarkers: function( track ){
			const map = ui.gpxMapObject.map;
			const provider = ui.gpxMapObject.provider;
			const track_data = track.data;
			const endpoints_type = track.endpoints_type ? track.endpoints_type : 'track';
			
			let edge_markers = [];
			let start = [];
			let end = [];
			if( endpoints_type == 'track' ){
		        start = track_data[0][0];
		        end = track_data[track_data.length - 1][track_data[track_data.length - 1].length - 1];
		        edge_markers.push( [ 
		            [ start['lat'], start['lng'] ],
		            [ end['lat'], end['lng'] ]
		        ] );
			}
			
			if( endpoints_type == 'segments' ){
				track_data.forEach( track => {
					start = track[0];
					end = track[track.length - 1];      
					edge_markers.push( [ 
						[ start['lat'], start['lng'] ],
						[ end['lat'], end['lng'] ]
					] );
				});
			}

			if( endpoints_type == 'none' || ! edge_markers ){
				return false;
			}

			if( provider == 'openstreetmap'){
				
				const backgroundStyle = `background-color: #007cba`;
				const startMarkerIconHtml = `<div class="fa-map-label"><div style="${backgroundStyle}" class="fa-map-label-marker"></div><i class="fas fa-flag"></i></div>`;
				const endMarkerIconHtml = `<div class="fa-map-label"><div style="${backgroundStyle}" class="fa-map-label-marker"></div><i class="fas fa-flag-checkered"></i></div>`;
				const startEndMarkerIconHtml = `<div class="fa-map-label"><div style="${backgroundStyle}" class="fa-map-label-marker"></div><i class="far fa-flag"></i></div>`;

				let markers_group = [];
				edge_markers.forEach( edge_points => {
					const start_coords = edge_points[0];
					const end_coords = edge_points[1];

					// check if start and end coordinates are different
					if( ( start_coords[0] !== end_coords[0] ) || ( start_coords[1] !== end_coords[1] ) ){
						let startMarkerIcon = L.divIcon( {
							className: 'citadela-marker-icon track-endpoint start-point',
							html: startMarkerIconHtml,
							iconSize: [ 50, 50 ],
							iconAnchor: [ 0, 0 ] 
						} );
						const startMarker = L.marker( start_coords, { icon: startMarkerIcon });
						startMarker.addTo( map );
						let endMarkerIcon = L.divIcon( {
							className: 'citadela-marker-icon track-endpoint end-point',
							html: endMarkerIconHtml,
							iconSize: [ 50, 50 ],
							iconAnchor: [ 0, 0 ] 
						} );				
						const endMarker = L.marker( end_coords, { icon: endMarkerIcon });
						endMarker.addTo( map );

						ui.gpxMapObject.markers.push( startMarker, endMarker );

					}else{
						let startMarkerIcon = L.divIcon( {
							className: 'citadela-marker-icon track-endpoint start-end-icon',
							html: startEndMarkerIconHtml,
							iconSize: [ 50, 50 ],
							iconAnchor: [ 0, 0 ] 
						} );
						const startMarker = L.marker( start_coords, { icon: startMarkerIcon });
						startMarker.addTo( map );
						ui.gpxMapObject.markers.push( startMarker );
					}


				} );

				return new L.featureGroup( markers_group );
				
			}
			


		},

		getGpxData: function(){
			const data = $('.gpx-upload-control .gpx-track-input').val();
			return data ? JSON.parse( data ) : [];
		},

		getGpxFileId: function(){
			return $('.gpx-upload-control .gpx-file-id-input').val();
		},

		gpxUploader: function( $currentContext ){
			var $container = $('.gpx-upload-control', $currentContext);
			$container.on('gpxUploaderInit', null, function(){
				var $thisContainer = $(this);
				var $mapWrapper = $thisContainer.find('.gpx-map-view');
				var Uploader = {
					init() {
						let self = this;
						
						if( $mapWrapper.hasClass('load-map') ){
							ui.gpxOpenstreetMap($mapWrapper);
						}else{
							$mapWrapper.hide();
							fadeFromTo( '.gpx-map-view', '.citadela-file-uploader', () => self.reset() );
						}
						
						const $uploader = $thisContainer.find('.citadela-file-uploader');
						const type = $uploader.data('type');
						const _citadelaPluploadOptions = type == 'gpx' ? _citadelaGpxUploadData : [];

						let $changeTrackButton = $thisContainer.find('.button-change-gpx-track');
						$changeTrackButton.on( 'click', (e) => {
							e.preventDefault();
							$thisContainer.find('.uploader-cancel').show();
							fadeFromTo( '.gpx-map-view', '.citadela-file-uploader', () => {} );
						});

						let $confirmSuccessButton = $thisContainer.find('.button-confirm-success');
						$confirmSuccessButton.on( 'click', (e) => {
							e.preventDefault();
							fadeFromTo( '.citadela-file-uploader', '.gpx-map-view', () => { 
								self.reset()
								ui.fitGpxMap();
							} );
						});

						let $removeTrackButton = $thisContainer.find('.button-remove-gpx-track');
						$removeTrackButton.on( 'click', (e) => {
							e.preventDefault();
							$('.gpx-upload-control .gpx-track-input').val( '' );
							$('.gpx-upload-control .gpx-file-id-input').val( '' );

							ui.gpxMapObject.polylines = [];
							fadeFromTo( '.gpx-map-view', '.citadela-file-uploader', () => self.reset() );

						});

						let $cancelUploadButton = $thisContainer.find('.button-cancel');
						$cancelUploadButton.on( 'click', (e) => {
							e.preventDefault();
							$(this).hide();
							fadeFromTo( '.citadela-file-uploader', '.gpx-map-view', () => { 
								self.reset()
								ui.fitGpxMap();
							} );

						});
						
						let mediaUploader;
						$thisContainer.find('.uploader-media-button').on('click', function(e) {
							e.preventDefault();
					        mediaUploader = wp.media.frames.file_frame = wp.media({
					            multiple: false,
					            library: {type: 'text/xml'},
					        });

					        mediaUploader.on('select', function() {
					            var attachment = mediaUploader.state().get('selection').first().toJSON();
					            
					            $.post( 
					            	CitadelaDirectorySettings.ajax.url + '?action=citadela_check_gpx_from_media', 
					            	{ attachment: attachment }, 
					            	( response ) => {
										if( response.success ){
											self.poltergeistButton('hide');
											self.processGpxData( response.data.data, response.data.file_id );
											fadeFromTo( '.uploader', '.import-complete');
										}else{
											$('.uploader-error-title').text( response.data.title )
											$('.uploader-error-msg').html( response.data.message )
											fadeFromTo( '.uploader-selector', '.uploader-error' )
										}
								} )
					        });

					        mediaUploader.open();
					        return false;
						});

						// handle change of endpoints type
						let $endpointsTypeSelect = $thisContainer.find('.gpx-endpoints-type');						
						$endpointsTypeSelect.change( function(){
							const $select_input = jQuery(this);
							const value = $select_input.find('option:selected').val();
							let $selected_description = $thisContainer.find('.gpx-endpoints-type-description.selected');
							let $target_description = $thisContainer.find(`.gpx-endpoints-type-description.${value}-description`);

							fadeFromTo( $selected_description, $target_description, () => {
								$selected_description.removeClass('selected');
								$target_description.addClass('selected');
							} );

							let gpxData = ui.getGpxData();
							//update additional tracks settings
							gpxData.forEach( ( track, index ) => {
								gpxData[index]['endpoints_type'] = value;
					        });
							const dataString = JSON.stringify(gpxData);
							$thisContainer.find('.gpx-track-input').val( dataString );
							ui.resetGpxMapData();
							ui.drawGpxTrack( gpxData, ui.gpxMapObject.map, 'openstreetmap');
						});

						this.wpUploaderOptions = _.extend( {
							init() {
								if ( ! this.supports.dragdrop ) {
									$('.uploader').removeClass('drag-drop')
								}
								$('.uploader-error button').on('click', () => {
									fadeFromTo( '.uploader-error', '.uploader-selector' )
								} )
							},

							added( attachment ) {
								fadeFromTo( '.uploader-selector', '.uploader-progress', () => self.onProgress( attachment ) )
							},

							progress( attachment ) {
								self.onProgress( attachment )
							},

							success( attachment ) {
								self.onSuccess( attachment );
							},

							error( message, data, file ) {
								self.onError( data )
							},

							browser: $('.uploader-browse-button'),
							dropzone: $('.uploader'),
						}, _citadelaPluploadOptions )

						let Uploader = new wp.Uploader(this.wpUploaderOptions)

					},

					onSuccess( attachment ) {
						this.poltergeistButton('hide');
						this.processGpxData( attachment.get('data'), attachment.get('file_id') );
						fadeFromTo( '.uploader', '.import-complete');
					},

					onProgress( attachment ) {
						$('.uploader-cancel').hide();
						$('.uploader-progress code').text( attachment.attributes.filename )
						$('.uploader-progress progress').val( attachment.attributes.percent )
					},

					onError( data ) {
						$('.uploader-error-title').text( data.title )
						$('.uploader-error-msg').html( data.message )
						fadeFromTo( '.uploader-progress', '.uploader-error' )
					},
					reset() {
						this.poltergeistButton('show')
						$('.uploader').show()
						$('.uploader-selector').show()

						$('.uploader-progress code').empty()
						$('.uploader-progress progress').val(0)
						$('.uploader-progress').hide()

						$('.uploader-error').hide()
						$('.uploader-error-title').empty()
						$('.uploader-error-msg').empty()

						$('.import-complete').hide()

						$('.uploader-cancel').hide();
					},
					poltergeistButton( op ) {
						// Plupload's shim to convert input[type=button] into input[type=file] and position it absolute over the button
						$('.moxie-shim.moxie-shim-html5')[ op ]()
					},
					processGpxData( data, file_id ){

						//update data with currently defined settings for GPX track, these data are not passed with gpx parser functions, they are additional options under map
						data.forEach( ( track, index ) => {
							data[index]['endpoints_type'] = $('.gpx-upload-control .gpx-endpoints-type').find('option:selected').val();
				        });

						const dataString = JSON.stringify(data);

						$('.gpx-upload-control .gpx-track-input').val( dataString );
						$('.gpx-upload-control .gpx-file-id-input').val( file_id );
						
						//check if map exists, or initialize new map
						if( $.isEmptyObject( ui.gpxMapObject.map ) ){
							ui.gpxOpenstreetMap($mapWrapper);
						}else{
							ui.gpxMapObject.polyline = [];
							ui.drawGpxTrack( ui.getGpxData(), ui.gpxMapObject.map, ui.gpxMapObject.provider );
						}
					},
				}
				Uploader.init();
			});
			$container.trigger('gpxUploaderInit');
		},
		
		sanitizeIdentifier: function( str, reservedIdentifiers = [] ){
		
			//map special chars to simple chars
			var map = {
		            "À": "A",
		            "Á": "A",
		            "Â": "A",
		            "Ã": "A",
		            "Ä": "A",
		            "Å": "A",
		            "Æ": "AE",
		            "Ç": "C",
		            "È": "E",
		            "É": "E",
		            "Ê": "E",
		            "Ë": "E",
		            "Ì": "I",
		            "Í": "I",
		            "Î": "I",
		            "Ï": "I",
		            "Ð": "D",
		            "Ñ": "N",
		            "Ò": "O",
		            "Ó": "O",
		            "Ô": "O",
		            "Õ": "O",
		            "Ö": "O",
		            "Ø": "O",
		            "Ù": "U",
		            "Ú": "U",
		            "Û": "U",
		            "Ü": "U",
		            "Ý": "Y",
		            "ß": "s",
		            "à": "a",
		            "á": "a",
		            "â": "a",
		            "ã": "a",
		            "ä": "a",
		            "å": "a",
		            "æ": "ae",
		            "ç": "c",
		            "è": "e",
		            "é": "e",
		            "ê": "e",
		            "ë": "e",
		            "ì": "i",
		            "í": "i",
		            "î": "i",
		            "ï": "i",
		            "ñ": "n",
		            "ò": "o",
		            "ó": "o",
		            "ô": "o",
		            "õ": "o",
		            "ö": "o",
		            "ø": "o",
		            "ù": "u",
		            "ú": "u",
		            "û": "u",
		            "ü": "u",
		            "ý": "y",
		            "ÿ": "y",
		            "Ā": "A",
		            "ā": "a",
		            "Ă": "A",
		            "ă": "a",
		            "Ą": "A",
		            "ą": "a",
		            "Ć": "C",
		            "ć": "c",
		            "Ĉ": "C",
		            "ĉ": "c",
		            "Ċ": "C",
		            "ċ": "c",
		            "Č": "C",
		            "č": "c",
		            "Ď": "D",
		            "ď": "d",
		            "Đ": "D",
		            "đ": "d",
		            "Ē": "E",
		            "ē": "e",
		            "Ĕ": "E",
		            "ĕ": "e",
		            "Ė": "E",
		            "ė": "e",
		            "Ę": "E",
		            "ę": "e",
		            "Ě": "E",
		            "ě": "e",
		            "Ĝ": "G",
		            "ĝ": "g",
		            "Ğ": "G",
		            "ğ": "g",
		            "Ġ": "G",
		            "ġ": "g",
		            "Ģ": "G",
		            "ģ": "g",
		            "Ĥ": "H",
		            "ĥ": "h",
		            "Ħ": "H",
		            "ħ": "h",
		            "Ĩ": "I",
		            "ĩ": "i",
		            "Ī": "I",
		            "ī": "i",
		            "Ĭ": "I",
		            "ĭ": "i",
		            "Į": "I",
		            "į": "i",
		            "İ": "I",
		            "ı": "i",
		            "Ĳ": "IJ",
		            "ĳ": "ij",
		            "Ĵ": "J",
		            "ĵ": "j",
		            "Ķ": "K",
		            "ķ": "k",
		            "Ĺ": "L",
		            "ĺ": "l",
		            "Ļ": "L",
		            "ļ": "l",
		            "Ľ": "L",
		            "ľ": "l",
		            "Ŀ": "L",
		            "ŀ": "l",
		            "Ł": "l",
		            "ł": "l",
		            "Ń": "N",
		            "ń": "n",
		            "Ņ": "N",
		            "ņ": "n",
		            "Ň": "N",
		            "ň": "n",
		            "ŉ": "n",
		            "Ō": "O",
		            "ō": "o",
		            "Ŏ": "O",
		            "ŏ": "o",
		            "Ő": "O",
		            "ő": "o",
		            "Œ": "OE",
		            "œ": "oe",
		            "Ŕ": "R",
		            "ŕ": "r",
		            "Ŗ": "R",
		            "ŗ": "r",
		            "Ř": "R",
		            "ř": "r",
		            "Ś": "S",
		            "ś": "s",
		            "Ŝ": "S",
		            "ŝ": "s",
		            "Ş": "S",
		            "ş": "s",
		            "Š": "S",
		            "š": "s",
		            "Ţ": "T",
		            "ţ": "t",
		            "Ť": "T",
		            "ť": "t",
		            "Ŧ": "T",
		            "ŧ": "t",
		            "Ũ": "U",
		            "ũ": "u",
		            "Ū": "U",
		            "ū": "u",
		            "Ŭ": "U",
		            "ŭ": "u",
		            "Ů": "U",
		            "ů": "u",
		            "Ű": "U",
		            "ű": "u",
		            "Ų": "U",
		            "ų": "u",
		            "Ŵ": "W",
		            "ŵ": "w",
		            "Ŷ": "Y",
		            "ŷ": "y",
		            "Ÿ": "Y",
		            "Ź": "Z",
		            "ź": "z",
		            "Ż": "Z",
		            "ż": "z",
		            "Ž": "Z",
		            "ž": "z",
		            "ſ": "s",
		            "ƒ": "f",
		            "Ơ": "O",
		            "ơ": "o",
		            "Ư": "U",
		            "ư": "u",
		            "Ǎ": "A",
		            "ǎ": "a",
		            "Ǐ": "I",
		            "ǐ": "i",
		            "Ǒ": "O",
		            "ǒ": "o",
		            "Ǔ": "U",
		            "ǔ": "u",
		            "Ǖ": "U",
		            "ǖ": "u",
		            "Ǘ": "U",
		            "ǘ": "u",
		            "Ǚ": "U",
		            "ǚ": "u",
		            "Ǜ": "U",
		            "ǜ": "u",
		            "Ǻ": "A",
		            "ǻ": "a",
		            "Ǽ": "AE",
		            "ǽ": "ae",
		            "Ǿ": "O",
		            "ǿ": "o",
		            
		            // extra
		            ' ': '_',
		            '+': '_',
					"'": '',
					'?': '',
					'!': '',
					'/': '',
					'\\': '',
					'.': '',
					',': '',
					'`': '',
					'>': '',
					'<': '',
					'"': '',
					'[': '',
					']': '',
					'|': '',
					'{': '',
					'}': '',
					'(': '',
					')': ''
	        };
			
			// vars
			var nonWord = /\W/g;
	        var mapping = function (c) {
	            return (map[c] !== undefined) ? map[c] : c;
	        };
	        str = str.replace(nonWord, mapping);
		    str = str.toLowerCase();
			
			if( reservedIdentifiers.length !== 0 ){
				//check for duplicity
				while ( jQuery.inArray( str, reservedIdentifiers ) !== -1 ) {
				  str = str + '_2';
				}
			}

		    return str;	
		},
		removeNode( $node ){
			$node.slideUp( 300 , function(){ $node.remove(); } );
		},
		isTouch: (('ontouchstart' in window) ||
			(navigator.maxTouchPoints > 0) ||
			(navigator.msMaxTouchPoints > 0))
	};


	// ===============================================
	// Init the UI
	// -----------------------------------------------

	$document.ready(function(){
		citadela.controls.ui.init();

	});

	function fadeFromTo( from, to, cb ) {
		$( from ).fadeOut( 100, () => {
			$( to ).fadeIn( 200, () => { if( cb ) cb() } )
		} )
	}

})(jQuery, jQuery(window), jQuery(document));