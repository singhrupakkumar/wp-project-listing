<?php

// ===============================================
// Citadela Listing Item CPT Config file
// -----------------------------------------------


class CitadelaItemConfig {

	protected static $plugin;

	public function __construct(){
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}

	public static function getData(){
		static $_citadelaConfiguration;

		self::$plugin = CitadelaDirectory::getInstance();
		$item_detail_options = get_option('citadela_directory_item_detail');
		$item_slug = $item_detail_options && isset( $item_detail_options['item_slug'] ) ? $item_detail_options['item_slug'] : 'item';

		if(is_null($_citadelaConfiguration)){
			$_citadelaConfiguration = new stdClass;

			$_citadelaConfiguration->cpt = (object) array(
				'labels' => array(
					'name'               => esc_html_x('Items', 'post type general name', 'citadela-directory'),
					'singular_name'      => esc_html_x('Item', 'post type singular name', 'citadela-directory'),
					'menu_name'          => esc_html_x('Items', 'post type menu name', 'citadela-directory'),
					'add_new'            => esc_html_x('Add New', 'Item', 'citadela-directory'),
					'add_new_item'       => esc_html__('Add New Item', 'citadela-directory'),
					'edit_item'          => esc_html__('Edit Item', 'citadela-directory'),
					'new_item'           => esc_html__('New Item', 'citadela-directory'),
					'view_item'          => esc_html__('View Item', 'citadela-directory'),
					'search_items'       => esc_html__('Search Items', 'citadela-directory'),
					'not_found'          => esc_html__('No Items found', 'citadela-directory'),
					'not_found_in_trash' => esc_html__('No Items found in Trash', 'citadela-directory'),
					'all_items'          => esc_html__('All Items', 'citadela-directory'),
				),

				'args' => array(
					'public'              => true,
					'exclude_from_search' => false, // 'true' - makes related taxonomies malfunction, refer to note in https://developer.wordpress.org/reference/functions/register_post_type/#exclude_from_search
					'publicly_queryable'  => true,
					'show_ui'             => true,
					'show_in_nav_menus'   => true,
					'show_in_menu'        => true, // 'true' - display as a top level menu
					'show_in_admin_bar'   => true,
					'has_archive'         => false,
					'show_in_rest' 		  => true,
					'rewrite' => array(
							'slug' => $item_slug,
						),
					'supports' => array(
						'title',
						'thumbnail',
						'editor',
						'page-attributes',
						'excerpt',
						'comments',
						'revisions'
					),
					'capability_type' => array('citadela-item', 'citadela-items'),
					'map_meta_cap' => true,
					'capabilities' => array(
						'edit_post'              => 'edit_citadela-item',
						'read_post'              => 'read_citadela-item',
						'delete_post'            => 'delete_citadela-item',
						'edit_posts'             => 'edit_citadela-items',
						'edit_others_posts'      => 'edit_others_citadela-items',
						'publish_posts'          => 'publish_citadela-items',
						'read_private_posts'     => 'read_private_citadela-items',
						'read'                   => 'read_citadela-items',
						'delete_posts'           => 'delete_citadela-items',
						'delete_private_posts'   => 'delete_private_citadela-items',
						'delete_published_posts' => 'delete_published_citadela-items',
						'delete_others_posts'    => 'delete_others_citadela-items',
						'edit_private_posts'     => 'edit_private_citadela-items',
						'edit_published_posts'   => 'edit_published_citadela-items',
					),
				),
			);

			$_citadelaConfiguration->taxonomies = (object) array(
				'category' => array(
					'labels' => array(
						'name'              => esc_html_x('Item Categories', 'taxonomy general name', 'citadela-directory'),
						'menu_name'         => esc_html_x('Item Categories', 'taxonomy menu name', 'citadela-directory'),
						'singular_name'     => esc_html_x('Item Category', 'taxonomy singular name', 'citadela-directory'),
						'search_items'      => esc_html__('Search Categories', 'citadela-directory'),
						'all_items'         => esc_html__('All Categories', 'citadela-directory'),
						'parent_item'       => esc_html__('Parent Category', 'citadela-directory'),
						'parent_item_colon' => esc_html__('Parent Category:', 'citadela-directory'),
						'edit_item'         => esc_html__('Edit Category', 'citadela-directory'),
						'view_item'         => esc_html__('View Category', 'citadela-directory'),
						'update_item'       => esc_html__('Update Category', 'citadela-directory'),
						'add_new_item'      => esc_html__('Add New Category', 'citadela-directory'),
						'new_item_name'     => esc_html__('New Category Name', 'citadela-directory'),
					),
					'args' => array(
						'hierarchical' 		=> true,
						'public' 			=> true,
						'show_in_nav_menus' => true,
						'show_ui' 			=> true,
						'show_admin_column' => true,
						'show_in_rest' 		=> true,
						'rewrite' => array(
							'slug' => 'cat',
						),
						'capabilities' => array(
							'manage_terms' => 'manage_citadela-item-categories',
							'edit_terms'   => 'edit_citadela-item-categories',
							'delete_terms' => 'delete_citadela-item-categories',
							'assign_terms' => 'assign_citadela-item-categories',
						),
					),
				),

				'location' => array(
					'labels' => array(
						'name'              => esc_html_x('Item Locations', 'taxonomy general name', 'citadela-directory'),
						'menu_name'         => esc_html_x('Item Locations', 'taxonomy menu name', 'citadela-directory'),
						'singular_name'     => esc_html_x('Item Location', 'taxonomy singular name', 'citadela-directory'),
						'search_items'      => esc_html__('Search Locations', 'citadela-directory'),
						'all_items'         => esc_html__('All Locations', 'citadela-directory'),
						'parent_item'       => esc_html__('Parent Location', 'citadela-directory'),
						'parent_item_colon' => esc_html__('Parent Location:', 'citadela-directory'),
						'edit_item'         => esc_html__('Edit Location', 'citadela-directory'),
						'view_item'         => esc_html__('View Location', 'citadela-directory'),
						'update_item'       => esc_html__('Update Location', 'citadela-directory'),
						'add_new_item'      => esc_html__('Add New Location', 'citadela-directory'),
						'new_item_name'     => esc_html__('New Location Name', 'citadela-directory'),
					),
					'args' => array(
						'hierarchical' 		=> true,
						'public' 			=> true,
						'show_in_nav_menus' => true,
						'show_ui' 			=> true,
						'show_admin_column' => true,
						'show_in_rest' 		=> true,
						'rewrite' => array(
							'slug' => 'loc',
						),
						'capabilities' => array(
							'manage_terms' => 'manage_citadela-item-locations',
							'edit_terms'   => 'edit_citadela-item-locations',
							'delete_terms' => 'delete_citadela-item-locations',
							'assign_terms' => 'assign_citadela-item-locations',
						),
					),
				),
			);


			/*
			*	categoryInputs => array of sections with inputs rendered on Item Category Add/Edit page
			*	inputs divided into separated sections may be useful for future to divide inputs into groups with some title etc....
			*/
			$_citadelaConfiguration->categoryOptions = array(
				'citadela-item-category-data' => array(
					'category_icon' => array(
							'type'		  => 'fontawesome-select',
							'title' 	  => esc_html__('Category icon', 'citadela-directory'),
							'description' => '',
							'default'	  => 'fas fa-circle',
							'single-meta' => false
						),
					'category_color' => array(
							'type'		  => 'colorpicker',
							'title' 	  => esc_html__('Category color', 'citadela-directory'),
							'description' => esc_html__('Category decoration color', 'citadela-directory'),
							'default'	  => "#0085ba",
							'opacity' 	  => false,
							'single-meta' => false
						),
					'featured' => array(
							'type'		  => 'checkbox',
							'title' 	  => esc_html__('Featured category', 'citadela-directory'),
							'description' => esc_html__('Mark category as featured to prioritize it in displayed results.', 'citadela-directory'),
							'default'	  => 0,
							'single-meta' => true //save as single meta to access value in wp tax query
						),
				),
			);

			$_citadelaConfiguration->locationOptions = array(
				'citadela-item-location-data' => array(
					'featured' => array(
							'type'		  => 'checkbox',
							'title' 	  => esc_html__('Featured location', 'citadela-directory'),
							'description' => esc_html__('Mark location as featured to prioritize it in displayed results.', 'citadela-directory'),
							'default'	  => 0,
							'single-meta' => true //save as single meta to access value in wp tax query
						),
				),
			);

			$_citadelaConfiguration->sections = (object) array(
				'citadela_item_options' => array(
					
					'general' => array(
						'label' => esc_html__( 'General', 'citadela-directory' ),
        				'icon'  => 'dashicons-text-page',
					),
					'address-location' => array(
						'label' => esc_html__( 'Address and Location', 'citadela-directory' ),
            			'icon'  => 'dashicons-location-alt'
					),
					'contact' => array(
						'label' => esc_html__( 'Contact', 'citadela-directory' ),
						'icon'  => 'dashicons-list-view'
					),
					'opening_hours' => array(
						'label' => esc_html__( 'Opening Hours', 'citadela-directory' ),
			            'description' => esc_html__( 'We recommend to use 24:00 time format for SEO purposes and satisfy Google Structured Data requirements. Use time like 08:00-15:00.', 'citadela-directory' ),
			            'icon'  => 'dashicons-clock'
					),
					'gallery' => array(
						'label' => esc_html__( 'Images gallery', 'citadela-directory' ),
			            'icon'  => 'dashicons-format-gallery'
					),
					'gpx_file_upload' => array(
						'label' => esc_html__( 'GPX Track', 'citadela-directory' ),
			            'description' => '',
			            'icon'  => 'dashicons-randomize'
					),
					'featured_category' => array(
						'label' => esc_html__( 'Featured Category', 'citadela-directory' ),
			            'description' => '',
			            'icon'  => 'dashicons-star-empty'
					),
				),
			);
			
			$_citadelaConfiguration->fields = (object) array(
				// metabox id
				'citadela_item_options' => array(
					
					// general section inputs
					'subtitle' => array(
						'type'    => 'citadela_text',
			            'section' => 'general',
			            'label'   => esc_html__( 'Subtitle', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),
					'featured' => array(
						'type'    => 'checkbox',
			            'section' => 'general',
			            'label'   => esc_html__( 'Featured', 'citadela-directory' ),
			            'description' => esc_html__( 'Mark Item post as featured', 'citadela-directory' ),
			            'sanitize_callback' => 'butterbean_validate_boolean',
					),

					// address section inputs
					'map' => array(
						'type'        => 'citadela_map',
		                'section'     => 'address-location',
		                'attr'        => array( 
		                					'class' => 'widefat', 
		                				),
		                'settings'    => array(
					                        'address'    => '_citadela_address',
					                        'latitude'   => '_citadela_latitude',
					                        'longitude'  => '_citadela_longitude',
					                        'streetview' => '_citadela_streetview',
					                        'swheading'  => '_citadela_swheading',
					                        'swpitch'    => '_citadela_swpitch',
					                        'swzoom'     => '_citadela_swzoom',
					                     ),
					),
					

					// contact section inputs
					'telephone' => array(
						'type'    => 'citadela_text',
			            'section' => 'contact',
			            'label'   => esc_html__( 'Telephone', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),
					'email' => array(
						'type'    => 'email',
			            'section' => 'contact',
			            'label'   => esc_html__( 'Email', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),
					'show_email' => array(
						'type'    => 'checkbox',
			            'section' => 'contact',
			            'label'   => esc_html__( 'Show Email Address', 'citadela-directory' ),
			            'description' => esc_html__( 'Decide if email address is visible for visitors', 'citadela-directory' ),
			            'sanitize_callback' => 'butterbean_validate_boolean'
					),
					'use_contact_form' => array(
						'type'    => 'checkbox',
			            'section' => 'contact',
			            'label'   => esc_html__( 'Use Contact Form', 'citadela-directory' ),
			            'description' => esc_html__( 'Show contact form on page which send an email to defined email address', 'citadela-directory' ),
			            'sanitize_callback' => 'butterbean_validate_boolean',
					),
					'web_url' => array(
						'type'    => 'url',
			            'section' => 'contact',
			            'label'   => esc_html__( 'Website Link', 'citadela-directory' ),
			            'description' => esc_html__( 'Use correct full website url', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),
					'web_url_label' => array(
						'type'    => 'citadela_text',
			            'section' => 'contact',
			            'label'   => esc_html__( 'Website Link Label', 'citadela-directory' ),
			            'description' => esc_html__( 'Text displayed instead of full website url', 'citadela-directory'),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),

					// opening hours section inputs
					'show_opening_hours' => array(
						'type'    => 'checkbox',
			            'section' => 'opening_hours',
			            'label'   => esc_html__( 'Show Opening Hours', 'citadela-directory' ),
			            'attr'    => array(),
			            'sanitize_callback' => 'butterbean_validate_boolean'
					),
					'opening_hours_monday' => array(
						'type'    => 'citadela_text',
			            'section' => 'opening_hours',
			            'label'   => esc_html__( 'Monday', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),
					'opening_hours_tuesday' => array(
						'type'    => 'citadela_text',
			            'section' => 'opening_hours',
			            'label'   => esc_html__( 'Tuesday', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),
					'opening_hours_wednesday' => array(
						'type'    => 'citadela_text',
			            'section' => 'opening_hours',
			            'label'   => esc_html__( 'Wednesday', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),
					'opening_hours_thursday' => array(
						'type'    => 'citadela_text',
			            'section' => 'opening_hours',
			            'label'   => esc_html__( 'Thursday', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),
					'opening_hours_friday' => array(
						'type'    => 'citadela_text',
			            'section' => 'opening_hours',
			            'label'   => esc_html__( 'Friday', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),
					'opening_hours_saturday' => array(
						'type'    => 'citadela_text',
			            'section' => 'opening_hours',
			            'label'   => esc_html__( 'Saturday', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),
					'opening_hours_sunday' => array(
						'type'    => 'citadela_text',
			            'section' => 'opening_hours',
			            'label'   => esc_html__( 'Sunday', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),
					'opening_hours_note' => array(
						'type'    => 'citadela_textarea',
			            'section' => 'opening_hours',
			            'label'   => esc_html__( 'Note', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
					),



					'gallery_images' => array(
						'type'    => 'citadela_gallery',
			            'section' => 'gallery',
			            'label'   => esc_html__( 'Images gallery', 'citadela-directory' ),
			            'attr'    => array( 'class' => 'widefat' ),
					),



					'gpx_track' => array(
						'type'    => 'citadela_gpx_upload',
			            'section' => 'gpx_file_upload',
			            'attr'    => array( 'class' => 'widefat' ),
			            'sanitize_callback' => 'wp_filter_nohtml_kses',
			            'settings'    => array(
			            					// data: setting name => meta name
					                        'track'    => '_citadela_gpx_track',
					                        'file_id'    => '_citadela_gpx_file_id',
					                    ),
					),



					'featured_category' => array(
						'type'    			=> 'citadela_featured_category_select',
						'taxonomy'			=> 'citadela-item-category',
			            'section' 			=> 'featured_category',
			            'label'   			=> esc_html__( 'Select featured category', 'citadela-directory' ),
			            'description' 		=> esc_html__( 'Selected category will be prioritized and will present Item post on the map.', 'citadela-directory'),
					),
					
				),
			);


			return $_citadelaConfiguration;
		}else{
			return $_citadelaConfiguration;
		}



	}

}

