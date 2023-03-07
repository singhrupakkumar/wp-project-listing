<?php
/**
 * Citadela Theme
 *
 */

class Citadela_Theme {

	public $theme_codename = 'citadela-theme';
	public $theme_class_prefix = 'CitadelaTheme';
	public $theme_config;
	public $theme_paths;
	public $basedir;
	public $baseurl;
	public $basename;


	private static $instance;

	public function run( $theme_file ){

		$this->basedir = dirname($theme_file);
		$this->baseurl = plugins_url('', $theme_file);
		$this->basename = plugin_basename($theme_file);

		$this->theme_config = $this->get_config();
		$this->theme_paths = $this->get_paths();

		spl_autoload_register( array( $this, 'autoload' ) );

		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
		add_action( 'init', array( $this, 'on_init' ) );
		add_action( 'wp_head', array( $this, 'wp_head' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'early_styles' ), 9 );
		add_action( 'wp_enqueue_scripts', array( $this, 'theme_assets' ), 10 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_theme_assets' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_assets' ) );

        add_action( 'citadela_render_header_logo', array( $this, 'render_header_logo' ) );

		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );

		add_filter( 'nav_menu_item_id', array( $this, 'menu_items_id' ), 10, 3 );
		add_filter( 'wp_nav_menu_args', array( $this, 'menu_args' ) );
		add_filter( 'wp_link_pages_args', array( $this, 'wp_link_pages_args' ) );

		if( $this->is_active_woocommerce() ){
			add_filter( 'woocommerce_breadcrumb_defaults',  array( $this, 'woocommerce_breadcrumb_defaults' ) );
		}

		if( is_admin() ){

			Citadela_Theme_Settings::run();

			// Change the name of Default Template in page templates selection
			add_filter( 'default_page_template_title', array( $this, 'change_default_page_template_title' ) );

		}

	}


	public function wp_head(){
		$this->preload_fontawesome_webfonst();
		if( $this->is_active_woocommerce() ){
			// 	remove product taxonomy description from template.
			//	description is displayed with title via page_title.php
			remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);

			//	remove breadcrumbs from default woocommerce hook, breadcrumbs are moved in woocommerce template
			//	except Product page
			if( ! is_product() ){
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
			}

		}
	}

	public function on_init(){

		$this->update_functions();

		load_theme_textdomain( 'citadela', "{$this->basedir}/languages" );

        foreach ( [ 'page', 'post', 'special_page', 'citadela-item' ] as $post_type ) {

        	if( $post_type !== 'post' ){
	            register_meta( 'post', '_citadela_hide_page_title', [
	                'object_subtype' => $post_type,
	                'show_in_rest' => true,
	                'type' => 'string',
	                'single' => true,
	                'auth_callback' => function() {
	                    return current_user_can( 'edit_posts' );
	                }
	            ] );

	            register_meta( 'post', '_citadela_remove_header_space', [
	                'object_subtype' => $post_type,
	                'show_in_rest' => true,
	                'type' => 'string',
	                'single' => true,
	                'auth_callback' => function() {
	                    return current_user_can( 'edit_posts' );
	                }
	            ] );
	        }

	        if( $this->is_active_pro_plugin() ){
	            register_meta( 'post', '_citadela_custom_class', [
	                'object_subtype' => $post_type,
	                'show_in_rest' => true,
	                'type' => 'string',
	                'single' => true,
	                'auth_callback' => function() {
	                    return current_user_can( 'edit_posts' );
	                }
	            ] );
	        }

        }

    }

    public function block_editor_assets() {
    	$current_screen = get_current_screen();

	    $deps = [
	    	'wp-plugins',
	    	'wp-edit-post',
	    	'wp-i18n',
	    	'wp-element'
	    ];

	    // register page settings only for editor on pages or posts
	    if( $current_screen && $current_screen->id != 'widgets' ){
	        $theme_editor_asset_file = include(  $this->basedir . '/design/js/build/editor.asset.php' );
	        wp_register_script(
	            'citadela-theme-editor-js',
	            $this->theme_paths->url->js . '/build/editor.js',
	            array_merge( $deps, $theme_editor_asset_file[ 'dependencies' ] ),
	            filemtime( $this->theme_paths->dir->js . '/build/editor.js' ),
	            true
	        );


	    	$settings = [
	            'activeProPlugin' => $this->is_active_pro_plugin(),
	            'activeDirectoryPlugin' => $this->is_active_directory_plugin(),
	        ];
			wp_add_inline_script( 'citadela-theme-editor-js', 'const CitadelaSettings = '. json_encode( $settings ), true );

	    	wp_set_script_translations( "citadela-theme-editor-js", 'citadela', "{$this->basedir}/languages" );
	        wp_enqueue_script( 'citadela-theme-editor-js' );
	    }


    }


	/**
	 * Enqueue scripts and styles.
	 */

	public function early_styles() {
		wp_enqueue_style( 'citadela-reset', $this->theme_paths->url->css . '/reset.css', array(), filemtime( $this->theme_paths->dir->css . '/reset.css' ) );
		wp_enqueue_style( 'citadela-base', $this->theme_paths->url->css . '/base.css', array(), filemtime( $this->theme_paths->dir->css . '/base.css' ) );
	}

	public function theme_assets() {
		// embed Google fonts in case the Citadela Pro Plugin is not active
		if( ! $this->is_active_pro_plugin() ){
			wp_enqueue_style( $this->theme_codename . '-google-fonts', 'https://fonts.googleapis.com/css?family=Open+Sans:400,700,800&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese&display=swap' );
        }

        $compile_theme_default = isset($_REQUEST['compile-theme-default']);
        if ($compile_theme_default || !$this->is_active_pro_plugin() ) {

            wp_enqueue_style( 'citadela-theme-default-style', $this->theme_paths->url->css . '/theme-default-style.css', array(), file_exists($this->theme_paths->dir->css . '/theme-default-style.css' ) ? filemtime($this->theme_paths->dir->css . '/theme-default-style.css' ) : '' );
        } else {
            $tmp_file_name = is_customize_preview() ? '/citadela-theme-tmp-preview-style.css' : '/citadela-theme-tmp-style.css';
            wp_enqueue_style( 'citadela-theme-general-styles', $this->theme_paths->url->tmp . $tmp_file_name, array(), file_exists($this->theme_paths->dir->tmp . $tmp_file_name) ? filemtime($this->theme_paths->dir->tmp . $tmp_file_name) : '' );
        }




		$citadela_styles = $this->theme_config->styles->frontend;
		if (is_array($citadela_styles) ){
			foreach ($citadela_styles as $file_handle => $file_data) {
				$this->enqueue_theme_file( 'css', $file_handle, $file_data);
			}
		}

		$citadela_assets_css = $this->theme_config->assets->frontend->css;
		if (is_array($citadela_assets_css) ){
			foreach ($citadela_assets_css as $file_handle => $file_data) {
				$this->enqueue_asset_file( 'css', $file_handle, $file_data);
			}
		}

		$citadela_assets_js = $this->theme_config->assets->frontend->js;
		if (is_array($citadela_assets_js) ){
			foreach ($citadela_assets_js as $file_handle => $file_data) {
				$this->enqueue_asset_file( 'js', $file_handle, $file_data);
			}
		}

		$citadela_scripts = $this->theme_config->scripts->frontend;
		if (is_array($citadela_scripts) ){
			foreach ($citadela_scripts as $file_handle => $file_data) {
				$this->enqueue_theme_file( 'js', $file_handle, $file_data);
			}
		}

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}


	}


	public function admin_theme_assets() {

		$current_screen = get_current_screen();
		$current_screen_id = ( $current_screen && isset($current_screen->id) ) ? $current_screen->id : '';

		$citadela_styles = $this->theme_config->styles->admin;
		if (is_array($citadela_styles) ){
			foreach ($citadela_styles as $file_handle => $file_data) {
				$this->enqueue_theme_file( 'css', $file_handle, $file_data);
			}
		}

		$citadela_scripts = $this->theme_config->scripts->admin;
		if (is_array($citadela_scripts) ){
			foreach ($citadela_scripts as $file_handle => $file_data) {
				if( $this->enqueue_on_admin_screen( $file_data, $current_screen_id ) )
				  $this->enqueue_theme_file( 'js', $file_handle, $file_data);
			}
		}

		$citadela_assets_js = $this->theme_config->assets->admin->js;
		if (is_array($citadela_assets_js) ){
			foreach ($citadela_assets_js as $file_handle => $file_data) {
				if( $this->enqueue_on_admin_screen( $file_data, $current_screen_id ) )
				  $this->enqueue_asset_file( 'js', $file_handle, $file_data);
			}
		}

		$citadela_assets_css = $this->theme_config->assets->admin->css;
		if (is_array($citadela_assets_css) ){
			foreach ($citadela_assets_css as $file_handle => $file_data) {
				$this->enqueue_asset_file( 'css', $file_handle, $file_data);
			}
		}

	}

	public function enqueue_on_admin_screen( $file_data, $current_screen_id ){
		if( isset( $file_data['pages'] ) && !in_array($current_screen_id, $file_data['pages']) ){
			return false;
		}else{
			return true;
		}
	}

	public function enqueue_theme_file( $file_type, $file_handle, $file_data ){

		switch ( $file_type ) {
			case 'css':
				wp_enqueue_style( 	$file_handle,
									$this->theme_paths->url->css . '/' . $file_data['file'],
									$file_data['deps'],
									$file_data['ver']
				);
				break;
			case 'js':
				wp_enqueue_script( 	$file_handle,
									$this->theme_paths->url->js . '/' . $file_data['file'],
									$file_data['deps'],
									$file_data['ver'],
									true
				);
				break;
			default:
				return;
		}

	}

	public function enqueue_asset_file( $file_type, $file_handle, $file_data ){

		switch ( $file_type ) {
			case 'css':
				wp_enqueue_style( 	$file_handle,
									$this->theme_paths->url->assets . '/' . $file_data['file'],
									$file_data['deps'],
									$file_data['ver']
				);
				break;
			case 'js':
				wp_enqueue_script( 	$file_handle,
									$this->theme_paths->url->assets . '/' . $file_data['file'],
									$file_data['deps'],
									$file_data['ver'],
									true
				);
				break;
			default:
				return;
		}
	}


	public function render_header_logo() {
		if( $this->is_active_pro_plugin() ) {
			do_action( 'citadela_pro_header_logo' );
		}else{
			the_custom_logo();
		}
	}

	public function admin_body_class( $classes ){
		$screen = get_current_screen();
		if( $screen && $screen->base == 'toplevel_page_citadela-settings' ){
			$classes .= ' citadela-settings-page citadela-dashboard-settings';
		}

		// pages with customized wp header
		if ( $screen && !$screen->is_block_editor && in_array( $screen->post_type, ['special_page', 'citadela-item'] ) ) {
			$classes .= ' citadela-custom-header';
		}

		return $classes;
	}

	public function body_classes( $classes ){
		global $post;

		// check active custom header settings for page and ignore classes added by Citadela Pro plugin
		$custom_header = $this->get_page_meta( '_citadela_header' );

		$classes[] = $this->get_layout( 'themeLayout' ) . '-theme-layout';
		$classes[] = $this->get_layout( 'headerLayout' ) . '-header-layout';
		$classes[] = 'default-theme-design';

		$classes[] = $this->get_page_template_type();


		//classes from customizer options
		if( ! $custom_header && sanitize_html_class( get_theme_mod( 'citadela_setting_headerColorOverlay', false ) ) ){
			$classes[] = 'header-color-overlay';
		}
		//hide/show page title, from Page settings
		$classes[] = $this->get_page_meta( '_citadela_hide_page_title' ) ? 'no-page-title' : 'is-page-title';

		//remove space under header, from Page settings
		if( $this->get_page_meta( '_citadela_remove_header_space' ) ){
			$classes[] = 'no-header-space';
		}

		if( $this->is_active_pro_plugin() ){
			$classes[] = $this->get_page_meta( '_citadela_custom_class' ) ? : '';
		}else{
			$classes[] = 'sticky-header-desktop-none';
			$classes[] = 'sticky-header-mobile-burger';
		}

		//support for woocommerce
		if( $this->is_active_woocommerce() ){

			// product has title in content, there is no standard page title
			if( is_product() ){
				if ( ($key = array_search( 'is-page-title', $classes ) ) !== false) {
		            unset( $classes[$key] );
		        }
				$classes[] = 'no-page-title';
			}
		}

		// functionality for widget titles deprecated from WP 5.8
		/*
		if( get_theme_mod( 'citadela_setting_leftCollapsibleWidgetsOpened' ) ){
			$classes[] = 'left-widgets-default-opened';
		}
		if( get_theme_mod( 'citadela_setting_rightCollapsibleWidgetsOpened' ) ){
			$classes[] = 'right-widgets-default-opened';
		}
		if( get_theme_mod( 'citadela_setting_footerCollapsibleWidgetsOpened' ) ){
			$classes[] = 'footer-widgets-default-opened';
		}
		*/

		//support for Citadela Listing plugin special pages
		if( $this->is_active_directory_plugin() ){
			$is_special_page = false;
			if (is_singular( 'citadela-item' ) ) {
            	$post_id = CitadelaDirectoryLayouts::getSpecialPageId( 'single-item' );
            	$is_special_page = true;
	        }
	        if (is_tax( 'citadela-item-category' ) ) {
	            $post_id = CitadelaDirectoryLayouts::getSpecialPageId( 'item-category' );
	            $is_special_page = true;
	        }
	        if (is_tax( 'citadela-item-location' ) ) {
	            $post_id = CitadelaDirectoryLayouts::getSpecialPageId( 'item-location' );
	            $is_special_page = true;
	        }
	        if ( is_search() && isset( $_REQUEST['ctdl'] ) ) {
	            $post_id = CitadelaDirectoryLayouts::getSpecialPageId( 'search-results' );
	            $is_special_page = true;
	        }

	        if( $is_special_page ){
				//if we are in special page, we need remove classes first and then check meta with special page ID
				if ( ($key = array_search( 'no-page-title', $classes ) ) !== false) {
	            	unset( $classes[$key] );
		        }
		        if ( ($key = array_search( 'is-page-title', $classes ) ) !== false) {
		            unset( $classes[$key] );
		        }
		        if ( ($key = array_search( 'no-header-space', $classes ) ) !== false) {
		            unset( $classes[$key] );
		        }

	        	$hide_page_title = get_post_meta( $post_id, '_citadela_hide_page_title', true );
				$classes[] = $hide_page_title ? 'no-page-title' : 'is-page-title';
				$remove_header_space = get_post_meta( $post_id, '_citadela_remove_header_space', true );
				$classes[] = $remove_header_space ? 'no-header-space' : '';
			}


		}

		$option = get_option('citadela_pro_events');
		if (!$option || ($option && $option['citadela_css'])) {
			$classes[] = 'citadela-events-css';
		}

		return $classes;
    }

	public function menu_items_id( $id, $item, $args ) {
		return $id;
	}

	public function menu_args( $args ){
		$args['container_class'] = 'citadela-menu-container';
		if($args['theme_location']){
			$args['container_class'] .= ' citadela-menu-' . $args['theme_location'];
		}
		$args['menu_class'] = 'citadela-menu';
		$args['fallback_cb'] = array( $this, 'menu_fallback' );

		return $args;
	}

	public function menu_fallback( $args ){
		$defaults = array(
			'sort_column' => 'menu_order, post_title',
			'menu_class' => 'menu'
		);

		$args = wp_parse_args($args, $defaults);
		$list_args = $args;
		$list_args['echo'] = false;
		$list_args['title_li'] = '';
		$menu = str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages($list_args) );
		$menu = '<ul class="' . esc_attr($args['menu_class']) . '">' . $menu . '</ul>';
		$menu = '<div class="' . esc_attr($args['container_class']) . '">' . $menu . "</div>\n";
		echo wp_kses_post( $menu ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function get_page_template_type() {
		global $post;

		$fullwidth_template = 'page-fullwidth';
		$left_sidebar_template = 'left-sidebar';
		$right_sidebar_template = 'right-sidebar';

		//allow half layout only with active Listing plugin for now
		$half_layout_template = $this->is_active_directory_plugin() ? 'half-layout' : 'page-fullwidth';

		if (function_exists('tribe_is_event_query') && tribe_is_event_query()) {
			$template = tribe_get_option('tribeEventsTemplate', 'default');
			if ($template === 'page-templates/left-sidebar.php' && is_active_sidebar('pages-sidebar')) {
				return $left_sidebar_template;
			} else if ($template === 'page-templates/right-sidebar.php' && is_active_sidebar('pages-sidebar')) {
				return $right_sidebar_template;
			} else {
				return $fullwidth_template;
			}
		}

		if ( is_front_page() && is_home() ) {
			// Latest posts homepage defined in Reading Settings
			if ( is_active_sidebar( 'blog-sidebar' ) ) {
		    	return $right_sidebar_template;
			}else{
				return $fullwidth_template;
			}

		} elseif ( is_front_page() ) {
			// Static page homepage defined in Reading Settings
			if ( is_page_template( 'default' ) ) {
		        return $fullwidth_template;
		    }elseif ( is_page_template( 'page-templates/left-sidebar.php' ) && is_active_sidebar( 'home-sidebar' ) ) {
		    	return $left_sidebar_template;
			}elseif ( is_page_template( 'page-templates/right-sidebar.php' ) && is_active_sidebar( 'home-sidebar' ) ) {
		    	return $right_sidebar_template;
			}elseif ( is_page_template( 'half-layout-template' ) ) {
		    	return $half_layout_template;
			}
			else{
				return $fullwidth_template;
			}

		} elseif ( is_home() ) {
			// Blog page defined in Reading Settings
			// half layout feature available only with Listing plugin yet
			if ($this->is_active_pro_plugin() && $this->is_active_directory_plugin() && $this->is_special_page_template( 'half-layout-template', 'blog' ) ){
				//check for Blog special page
				return $half_layout_template;
			}else{
				if ( is_active_sidebar( 'blog-sidebar' ) ) {
			    	return $right_sidebar_template;
				}else{
					return $fullwidth_template;
				}
			}

		} else {
			// other types
			if( is_page() ){
				if ( is_page_template( 'default' ) ) {
			        return $fullwidth_template;
			    }elseif ( is_page_template( 'page-templates/left-sidebar.php' ) && is_active_sidebar( 'pages-sidebar' ) ) {
			    	return $left_sidebar_template;
				}elseif ( is_page_template( 'page-templates/right-sidebar.php' ) && is_active_sidebar( 'pages-sidebar' ) ) {
			    	return $right_sidebar_template;
				}elseif ( is_page_template( 'half-layout-template' ) ) {
			    	return $half_layout_template;
				}else{
					return $fullwidth_template;
				}

			}elseif( is_attachment() ){
				return $fullwidth_template;

			}elseif( is_single() ){
				$post_type = get_post_type();
				switch ($post_type) {
					case 'post':
						if ( is_active_sidebar( 'posts-sidebar' ) ){
							return $right_sidebar_template;
						}
						break;
					case 'citadela-item':
						if( $this->is_active_directory_plugin() && \CitadelaDirectory::getInstance()->ItemPageLayout_instance->ignore_special_page ){
							// item page which do not use Item Detail Special Page layout
							if( get_post_meta( $post->ID, "_citadela_page_template", true ) == 'half-layout-template' ){
								//on item detail page make sure that there are data to show on map on hidden empty map, otherwise return just fullwidth template
								$map_no_data_behavior = get_post_meta( $post->ID, '_citadela_half_map_noDataBehavior', true );
					            if( $map_no_data_behavior == 'hidden-map' ){
					                $track = get_post_meta($post->ID, '_citadela_gpx_track', true);
					                $lat = get_post_meta($post->ID, '_citadela_latitude', true);
					                $lon = get_post_meta($post->ID, '_citadela_longitude', true);
					                if( ! $track && ! $lon && ! $lat ){
					                    return $fullwidth_template;
					                }else{
					                	return $half_layout_template;
					                }
					            }else{
									return $half_layout_template;
					            }
							}elseif ( is_active_sidebar( 'item-sidebar' ) ){
								return $right_sidebar_template;
							}
						}else{
							// item page with Item Detail Special Page layout
							if ( $this->is_active_directory_plugin() && $this->is_special_page_template( 'half-layout-template', 'single-item' ) ) {
								//on item detail page make sure that there are data to show on map on hidden empty map, otherwise return just fullwidth template
								//we need to get item detail special page id, not item post id
								$map_no_data_behavior = get_post_meta( $this->get_page_id(), '_citadela_half_map_noDataBehavior', true );
					            if( $map_no_data_behavior == 'hidden-map' ){
					                $track = get_post_meta($post->ID, '_citadela_gpx_track', true);
					                $lat = get_post_meta($post->ID, '_citadela_latitude', true);
					                $lon = get_post_meta($post->ID, '_citadela_longitude', true);
					                if( ! $track && ! $lon && ! $lat ){
					                    return $fullwidth_template;
					                }else{
					                	return $half_layout_template;
					                }
					            }else{
									return $half_layout_template;
					            }
							}elseif ( is_active_sidebar( 'item-sidebar' ) ){
								return $right_sidebar_template;
							}
						}
						break;
					case 'tribe_events':
						if ( is_active_sidebar( 'posts-sidebar' ) ){
							return $right_sidebar_template;
						}
						break;
					default:
						return $fullwidth_template;
						break;
				}

			}elseif( is_tax() ){
				if( is_tax( 'citadela-item-category' ) ){

					if ( $this->is_active_directory_plugin() && $this->is_special_page_template( 'half-layout-template', 'item-category' ) ) {
				    	return $half_layout_template;
					}elseif( is_active_sidebar( 'item-category-sidebar' ) ){
						return $right_sidebar_template;
					}

				}

				if( is_tax( 'citadela-item-location' ) ){

					if ( $this->is_active_directory_plugin() && $this->is_special_page_template( 'half-layout-template', 'item-location' ) ) {
				    	return $half_layout_template;
					}elseif( is_active_sidebar( 'item-location-sidebar' ) ){
						return $right_sidebar_template;
					}

				}
				return $fullwidth_template;

			}elseif( is_archive() ){

				if( $this->is_active_woocommerce() ){
					if( is_shop() ) {
						if( is_active_sidebar( 'woocommerce-shop-sidebar' ) ){
							return $right_sidebar_template;
						}else{
							return $fullwidth_template;
						}

					}
				}

				if( is_category() ){

					if ( $this->is_active_directory_plugin() && $this->is_special_page_template( 'half-layout-template', 'posts-category' ) ) {
						return $half_layout_template;
					}elseif( is_active_sidebar( 'archives-sidebar' ) ){
						return $right_sidebar_template;
					}

				}

				if( is_tag() ){

					if ( $this->is_active_directory_plugin() && $this->is_special_page_template( 'half-layout-template', 'posts-tag' ) ) {
						return $half_layout_template;
					}elseif( is_active_sidebar( 'archives-sidebar' ) ){
						return $right_sidebar_template;
					}

				}

				if( is_date() ){

					if ( $this->is_active_directory_plugin() && $this->is_special_page_template( 'half-layout-template', 'posts-date' ) ) {
						return $half_layout_template;
					}elseif( is_active_sidebar( 'archives-sidebar' ) ){
						return $right_sidebar_template;
					}

				}

				if( is_author() ){

					if ( $this->is_active_directory_plugin() && $this->is_special_page_template( 'half-layout-template', 'posts-author' ) ) {
						return $half_layout_template;
					}elseif( is_active_sidebar( 'archives-sidebar' ) ){
						return $right_sidebar_template;
					}

				}

				if( is_active_sidebar( 'archives-sidebar' ) ){
					return $right_sidebar_template;
				}

			}elseif( is_404() ){

				if ( $this->is_active_directory_plugin() && $this->is_special_page_template( 'half-layout-template', '404-page' ) ) {
					return $half_layout_template;
				}elseif( is_active_sidebar( '404-sidebar' ) ){
					return $right_sidebar_template;
				}

			}elseif( is_search() ){
				if( isset( $_GET['ctdl'] ) ){
					// citadela listing items search results page
					if( $_GET['post_type'] == 'citadela-item' ){
						if ( $this->is_active_directory_plugin() && $this->is_special_page_template( 'half-layout-template', 'search-results' ) ) {
							return $half_layout_template;
						}
					}
					if( $_GET['post_type'] == 'post' ){
						if ( $this->is_active_directory_plugin() && $this->is_special_page_template( 'half-layout-template', 'posts-search-results' ) ) {
							return $half_layout_template;
						}
					}
					return $fullwidth_template;
				}else{
					// standard search results page
					if ( $this->is_active_directory_plugin() && $this->is_special_page_template( 'half-layout-template', 'default-search-results' ) ) {
						return $half_layout_template;
					}elseif( is_active_sidebar( 'search-sidebar' ) ){
						return $right_sidebar_template;
					}else{
						return $fullwidth_template;
					}
				}
			}else{
				return $fullwidth_template;
			}
		}

		//if no one of previous types, show fullwidth
		return $fullwidth_template;
	}

	/*
	*	check if special page has selected defined template
	*/
	public function is_special_page_template( $template, $page_type ){

		if( $page_type === 'blog' ){
			$page_id = \Citadela\Pro\Special_Pages\Page::id( $page_type );
		}else{
			$page_id = CitadelaDirectoryLayouts::getSpecialPageId( $page_type );
		}
		return $template == get_page_template_slug( $page_id );
	}


	/*
	*	get page id considering Citadela Special Pages and WooCommerce Pages
	*/
	public function get_page_id(){
		global $post;

		$id = false;

		if ( is_front_page() && is_home() ) {
			//Latest Posts homepage defined in Reading Settings
			// this page doesn't have custom meta data
			return $id;

		}elseif ( is_home() ) {
			//Blog Page defined in Reading Settings
			// if Citadela Pro plugin is active, check meta from special page
			if ( $this->is_active_pro_plugin() ){
				//consider Citadela Pro Special Pages
				return get_option( 'citadela_blog_page' );

			}else{
				return $id;
			}

		}elseif( is_page() || is_singular( 'post' ) ){
			//standard Page or Post
			$id = $post->ID;
		}elseif ( is_category() && $this->is_active_directory_plugin() ){
			$id = CitadelaDirectoryLayouts::getSpecialPageId('posts-category');

		}elseif ( is_tag() && $this->is_active_directory_plugin() ){
			$id = CitadelaDirectoryLayouts::getSpecialPageId('posts-tag');

		}elseif ( is_date() && $this->is_active_directory_plugin() ){
			$id = CitadelaDirectoryLayouts::getSpecialPageId('posts-date');

		}elseif ( is_author() && $this->is_active_directory_plugin() ){
			$id = CitadelaDirectoryLayouts::getSpecialPageId('posts-author');

		}elseif ( is_404() && $this->is_active_directory_plugin() ){
			$id = CitadelaDirectoryLayouts::getSpecialPageId('404-page');

		}elseif ( is_tax( 'citadela-item-category' ) && $this->is_active_directory_plugin() ) {
			$id = CitadelaDirectoryLayouts::getSpecialPageId('item-category');

		}elseif ( is_tax( 'citadela-item-location' ) && $this->is_active_directory_plugin() ) {
			$id = CitadelaDirectoryLayouts::getSpecialPageId('item-location');

		}elseif ( is_search() && $this->is_active_directory_plugin() ) {
			if( CitadelaDirectorySpecialPages::is_search_results_page() && isset( $_REQUEST[ 'post_type' ] ) ){
				if( $_REQUEST[ 'post_type' ] == 'citadela-item' ){
					$id = CitadelaDirectoryLayouts::getSpecialPageId('search-results');

				}elseif ( $_REQUEST[ 'post_type' ] == 'post' ) {
					$id = CitadelaDirectoryLayouts::getSpecialPageId('posts-search-results');

				}
            }elseif( CitadelaDirectorySpecialPages::is_default_search_results_page() ){
				$id = CitadelaDirectoryLayouts::getSpecialPageId('default-search-results');
			}
		}elseif ( is_single( $post ) && get_post_type( $post ) == 'citadela-item' && $this->is_active_directory_plugin() ) {
			// consider Citadela Listing special pages
			$item_detail_options = get_option('citadela_directory_item_detail');
			$allowed_editor = $item_detail_options && isset( $item_detail_options['enable'] ) && $item_detail_options['enable'];
			$ignore_special_page = get_post_meta( $post->ID, '_citadela_ignore_special_page', true );

			$id = $allowed_editor && $ignore_special_page ? $post->ID : CitadelaDirectoryLayouts::getSpecialPageId('single-item');

		}elseif( $this->is_active_woocommerce() && is_shop() ){
			// Woocommerce shop page
			$id = get_option( 'woocommerce_shop_page_id' );

		}else{

			return $id;
		}
		return $id;
	}
	/*
	*	function get meta only from Pages, including Special pages from Citadela Pro and Listing plugins
	*/
	public function get_page_meta( $meta_key = false ) {

		if( $meta_key === false ) return false;

		global $post;

		if ( is_front_page() && is_home() ) {
			//Latest Posts homepage defined in Reading Settings
			// this page doesn't have custom meta data
			return false;

		}elseif ( is_home() ) {
			//Blog Page defined in Reading Settings
			// if Citadela Pro plugin is active, check meta from special page
			if ( $this->is_active_pro_plugin() ){
				//consider Citadela Pro Special Pages
				$id = get_option( 'citadela_blog_page' );
				$meta = get_post_meta( $id, $meta_key, true );
				if($meta) return $meta;

			}else{
				return false;
			}

		}elseif( is_page() || is_singular( 'post' ) ){

			//standard Page or Post
			$meta = get_post_meta( $post->ID, $meta_key, true );
			if($meta) return $meta;

		}elseif ( is_category() && $this->is_active_directory_plugin() ){
			$id = CitadelaDirectoryLayouts::getSpecialPageId('posts-category');
			$meta = get_post_meta( $id, $meta_key, true );
			if($meta) return $meta;

		}elseif ( is_tag() && $this->is_active_directory_plugin() ){
			$id = CitadelaDirectoryLayouts::getSpecialPageId('posts-tag');
			$meta = get_post_meta( $id, $meta_key, true );
			if($meta) return $meta;

		}elseif ( is_date() && $this->is_active_directory_plugin() ){
			$id = CitadelaDirectoryLayouts::getSpecialPageId('posts-date');
			$meta = get_post_meta( $id, $meta_key, true );
			if($meta) return $meta;

		}elseif ( is_author() && $this->is_active_directory_plugin() ){
			$id = CitadelaDirectoryLayouts::getSpecialPageId('posts-author');
			$meta = get_post_meta( $id, $meta_key, true );
			if($meta) return $meta;

		}elseif ( is_404() && $this->is_active_directory_plugin() ){
			$id = CitadelaDirectoryLayouts::getSpecialPageId('404-page');
			$meta = get_post_meta( $id, $meta_key, true );
			if($meta) return $meta;

		}elseif ( is_tax( 'citadela-item-category' ) && $this->is_active_directory_plugin() ) {
			// consider Citadela Listing special page
			$id = CitadelaDirectoryLayouts::getSpecialPageId('item-category');
			$meta = get_post_meta( $id, $meta_key, true );
			if($meta) return $meta;

		}elseif ( is_tax( 'citadela-item-location' ) && $this->is_active_directory_plugin() ) {
			// consider Citadela Listing special page
			$id = CitadelaDirectoryLayouts::getSpecialPageId('item-location');
			$meta = get_post_meta( $id, $meta_key, true );
			if($meta) return $meta;

		}elseif ( is_search() && $this->is_active_directory_plugin() ) {
			// consider Citadela Listing special pages
			if( CitadelaDirectorySpecialPages::is_search_results_page() && isset( $_REQUEST[ 'post_type' ] ) ){
				if( $_REQUEST[ 'post_type' ] == 'citadela-item' ){
					$id = CitadelaDirectoryLayouts::getSpecialPageId('search-results');
					$meta = get_post_meta( $id, $meta_key, true );
					if($meta) return $meta;

				}elseif ( $_REQUEST[ 'post_type' ] == 'post' ) {
					$id = CitadelaDirectoryLayouts::getSpecialPageId('posts-search-results');
					$meta = get_post_meta( $id, $meta_key, true );
					if($meta) return $meta;
				}
			}
			if( method_exists("CitadelaDirectorySpecialPages", "is_default_search_results_page") && CitadelaDirectorySpecialPages::is_default_search_results_page() ){
					$id = CitadelaDirectoryLayouts::getSpecialPageId('default-search-results');
					$meta = get_post_meta( $id, $meta_key, true );
					if($meta) return $meta;
			}

		}elseif ( is_single( $post ) && get_post_type( $post ) == 'citadela-item' && $this->is_active_directory_plugin() ) {
			// consider Citadela Listing special pages
			$item_detail_options = get_option('citadela_directory_item_detail');
			$allowed_editor = $item_detail_options && isset( $item_detail_options['enable'] ) && $item_detail_options['enable'];
			$ignore_special_page = get_post_meta( $post->ID, '_citadela_ignore_special_page', true );
			$id = $allowed_editor && $ignore_special_page ? $post->ID : CitadelaDirectoryLayouts::getSpecialPageId('single-item');

			$meta = get_post_meta( $id, $meta_key, true );
			if($meta) return $meta;

		}elseif( $this->is_active_woocommerce() && is_shop() ){
			// Woocommerce shop page
			$meta = get_post_meta( get_option( 'woocommerce_shop_page_id' ), $meta_key, true );
			if($meta) return $meta;

		}else{
			return false;
		}
	}

	public function get_pro_theme_mod( $key, $default ){
		if( ! $this->is_active_pro_plugin() ){
			return null;
		}
		return get_theme_mod( $key, $default );
	}

	public function render_posts_pagination( $args = array() ) {

		$defaults = array(
		    'mid_size' => 2,
		    'prev_text' => esc_html__( 'Previous', 'citadela' ),
		    'next_text' => esc_html__( 'Next', 'citadela' )
		);

		$args = empty($args) ? $defaults : $args;

		the_posts_pagination($args);
	}

	public function wp_link_pages_args( $parsed_args ){
		$parsed_args['before'] = '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'citadela' ) . '</span>';
		return $parsed_args;
	}

	public function woocommerce_breadcrumb_defaults( $defaults ) {
		$defaults['delimiter'] = '<span></span>';
		return $defaults;
	}


	public function change_default_page_template_title() {
		 return esc_html__( 'Fullwidth page', 'citadela' );
	}

	public function preload_fontawesome_webfonst() {
    	if( ! defined('CITADELA_PRELOADED_FONTAWESOME') ){
			foreach ( [
				"fa-solid-900.woff2",
				"fa-regular-400.woff2",
				"fa-brands-400.woff2",
			] as $filename) {
				$url = $this->theme_paths->url->css . '/assets/fontawesome/webfonts/' . $filename;
    			?><link rel="preload" as="font" type="font/woff2" href="<?php echo esc_url( $url ); ?>" crossorigin/><?php
			}
			define('CITADELA_PRELOADED_FONTAWESOME', true);
    	}
    }

    public function get_current_locale() {
		return get_locale();
	}


	public function get_current_language_code() {
		$locale = get_locale();
		if($locale == 'zh_CN' ){
			return 'cn';
		}elseif($locale == 'zh_TW' ){
			return 'tw';
		}elseif($locale == 'pt_BR' ){
			return 'br';
		}else{
			return substr($locale, 0, 2);
		}
	}

	public function get_layout( $layout ){
		if( ! $this->is_active_pro_plugin() ){
			return $this->theme_config->default_layouts[$layout];
		}else{
			return sanitize_html_class( get_theme_mod( 'citadela_setting_'.$layout, $this->theme_config->default_layouts[$layout]) );
		}
	}

	public function get_config(){
		return citadela_config();
	}

	public function get_paths(){
		return citadela_paths();
	}

	public function is_active_pro_plugin(){
		return defined( 'CITADELA_PRO_PLUGIN' ) ? true : false;
	}

	public function is_active_directory_plugin(){
		return defined( 'CITADELA_DIRECTORY_PLUGIN' ) ? true : false;
	}

	public function is_active_blocks_plugin(){
		return defined( 'CITADELA_BLOCKS_PLUGIN' ) ? true : false;
	}

	public function is_active_woocommerce(){
		return class_exists( 'woocommerce' ) ? true : false;
	}

	public function after_switch_theme(){
		wp_redirect( esc_url( admin_url( 'admin.php?page=citadela-settings') ) );
	}

	public function before_wp58(){
		return class_exists( 'WP_Block_Editor_Context' ) ? false : true;
	}

	public function update_functions(){
		if( ! get_option('citadela_update_hide_sitetitle_and_tagline') ){
			if( get_theme_mod('citadela_setting_hideTaglineSitetitle') ){
				set_theme_mod( 'citadela_setting_hideSitetitleAndTaglineDesktop', 'hide-title-and-tagline' );
				set_theme_mod( 'citadela_setting_hideSitetitleAndTaglineMobile', 'hide-title-and-tagline' );
				set_theme_mod( 'citadela_setting_logoImageMaxWidthDesktop', 300 );
				set_theme_mod( 'citadela_setting_logoImageMaxWidthMobile', 300 );
			}
			update_option('citadela_update_hide_sitetitle_and_tagline', true );
		}
	}

	/* ************ helper methods ********** */

	public function autoload($class)
	{

		$class = str_replace( "_", "", $class );
        // autoload namespaced classes within ./customizer folder
        if(substr($class, 0, 20) === 'Citadela\Customizer\\' ){ // starts with
            $filename = str_replace(['Citadela\Customizer\\', '\\'], ['', '/'], $class);
            $file = __DIR__ . "/customizer/{$filename}.php";
		    if($file and file_exists($file) ){
		        require_once $file; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		        return;
		    }
        }

		$file = '';
		//paths from /citadela-theme folder
		$load_paths = array(
					'/admin/settings',
				);

		foreach ($load_paths as $path) {

			if(substr($class, 0, 13) === $this->theme_class_prefix){
				$file = $this->theme_paths->dir->citadela . $path . "/{$class}.php";
			}

			if($file and file_exists($file) ){
				require_once $file; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			}

        }
	}

	public static function get_instance() {
		if(!self::$instance){
			self::$instance = new self;
		}

		return self::$instance;
	}

}
