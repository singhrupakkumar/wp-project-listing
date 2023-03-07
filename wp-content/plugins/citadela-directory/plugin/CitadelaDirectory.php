<?php

// ===============================================
// Citadela Listing plugin
// -----------------------------------------------


class CitadelaDirectory {

	public $basedir, $baseurl, $basename, $paths;
	public $configGeneral;
	public $pluginCodeName;
	public $pluginClassPrefix;
	public $ajaxActions;
	public $pluginOptions;
	public $HalfLayoutMap_instance;
	public $Subscriptions_instance;
	public $ItemPageLayout_instance;

	private static $instance;


	public function run($pluginFile) {
		$this->pluginCodeName = 'citadela-directory';
		$this->pluginClassPrefix = 'CitadelaDirectory';

		$this->basedir = dirname($pluginFile);
		$this->baseurl = plugins_url('', $pluginFile);
		$this->basename = plugin_basename($pluginFile);

		spl_autoload_register(array($this, 'autoload'));

		if(!CitadelaDirectoryCompatibility::supportPhp() or !CitadelaDirectoryCompatibility::supportWp()){
			return;
		}

		CitadelaDirectoryCompatibility::handleThemesSupport($this->basename);

		$this->paths = CitadelaDirectoryPaths::getPaths();
		$this->configGeneral = CitadelaDirectoryConfigGeneral::getConfigData();

		register_activation_hook($pluginFile, array($this, 'activation'));
		register_deactivation_hook($pluginFile, array($this, 'deactivation'));
		
		add_action( 'plugins_loaded', array($this, 'plugins_loaded') );

		$this->ItemPageLayout_instance = new \Citadela\Directory\ItemPageLayout\Feature();

		load_plugin_textdomain( 'citadela-directory', false, basename($this->paths->dir->plugin_dir) . '/languages');
		CitadelaItem::init();
		
		\Citadela\Directory\Migration::run();

		$this->pluginOptions = $this->getPluginOptions();

		$this->Subscriptions_instance = new \Citadela\Directory\Subscriptions\Feature();

		add_action('after_setup_theme', function () {
			if (Citadela::$allowed) {

				add_action( 'init', array($this, 'init') );
				add_action( 'wp_head', array( $this, 'wp_head' ) );
				add_action( 'admin_head', array( $this, 'admin_head' ) );
				
				
				add_action( 'ctdl_directory_categories_list', array($this, 'categories_list'), 10, 2 );
				add_action( 'ctdl_directory_post_locations_list', array($this, 'post_locations_list'), 10, 2 );
				add_action( 'ctdl_directory_post_thumbnail', array($this, 'post_thumbnail') );
				add_action( 'ctdl_directory_posted_on', array($this, 'posted_on') );
				add_action( 'ctdl_directory_posted_by', array($this, 'posted_by') );
				add_action( 'ctdl_directory_leave_comment', array($this, 'leave_comment') );

				

				add_action( 'wp_enqueue_scripts', [ $this, 'enqueueScripts' ], 10 );
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAdminScripts' ] );

				add_action( 'widgets_init',  [ $this, 'registerSidebars' ] );
				add_filter( 'body_class', [ $this, 'body_class' ], 11 );
				add_filter( 'upload_mimes', [ $this, 'upload_mimes' ], 1, 1 );
				
				CitadelaDirectorySettings::init();
				CitadelaPost::init();
				CitadelaUser::init();
				CitadelaDirectoryLayouts::init();
				CitadelaDirectorySpecialPages::init();
				CitadelaDirectorySearch::init();
				CitadelaButterbeanGpxAssets::init();
		
				$this->registerImageSizes();

				\Citadela\Directory\ItemExtension::run();
				\Citadela\Directory\AdvancedFilters::run();
				\Citadela\Directory\ItemReviews::run();
				\Citadela\Directory\EasyAdmin::run();
				\Citadela\Directory\ClaimListing::run();
				\Citadela\Directory\Events::run();
				$this->HalfLayoutMap_instance = new \Citadela\Directory\HalfLayoutMap\Feature();
				$this->fixFeaturedItems();
				$this->updateSpecialPagesNames();

			} else {
				add_action('admin_enqueue_scripts', function () {
					wp_enqueue_style('citadela-admin-style', plugin_dir_url(__DIR__) .  '/design/css/admin/admin-style.css');
				});
			}
		}, 100);

	}
	
	public function init() {
		CitadelaDirectoryRecaptcha::init();
		$this->registerFrontendAjax();
		$this->registerBlocks();
	}

	

	public function wp_head() {
		$this->preload_fontawesome_webfonst();
		$this->initGlobalJsVariables();
	}

	public function preload_fontawesome_webfonst() {
    	if( ! defined('CITADELA_PRELOADED_FONTAWESOME') ){
			foreach ( [
				"fa-solid-900.woff2",
				"fa-regular-400.woff2",
				"fa-brands-400.woff2",
			] as $filename) {
				$url = $this->paths->url->assets . '/fontawesome/webfonts/' . $filename;
    			?><link rel="preload" as="font" type="font/woff2" href="<?php echo esc_url( $url ); ?>" crossorigin/><?php
			}
			define('CITADELA_PRELOADED_FONTAWESOME', true);
    	}
    }

	public function admin_head() {
		$this->initGlobalJsVariables();
	}

	public function plugins_loaded(){
		load_plugin_textdomain( 'citadela-directory', false, basename($this->paths->dir->plugin_dir) . '/languages');
	}


    public function categories_list( $separator = '', $styles = [] ) {
        if ( function_exists( 'citadela_theme_categories_list' ) ) {
            citadela_theme_categories_list( $separator, $styles );
        } else {
            if ( 'post' === get_post_type() ) {
                global $post;
		    	$categories = get_the_terms($post->ID, 'category');
				$categories = apply_filters( 'the_category_list', $categories, $post->ID);
				
				if ( $categories && ! is_wp_error( $categories ) ) {

					$itemDataCategoryStyle = ! empty( $styles ) && isset( $styles['itemDataCategoryStyle'] ) ? $styles['itemDataCategoryStyle'] : "";

					$cat_links = [];
					foreach ($categories as $cat) {
		            	array_push(
		            		$cat_links, 
		            		'<a href="' . esc_url( get_term_link( $cat->term_id ) ) . '" rel="category tag" ' . $itemDataCategoryStyle . '>' . $cat->name . '</a>'
		            	);
						
					}

				}
				
				echo '<span class="cats-links">';
				/* translators: Posted in [categories list]. */
	            echo 	'<span class="cats-text">';
	            esc_html_e( 'Posted in', 'citadela' );
	            echo '</span> ';
				echo 	'<span class="cats-list">' . implode($separator, $cat_links) . '</span>';
				echo '</span>';
            }
        }
    }


    public function post_locations_list( $separator = '', $styles = [] ) {
    	if ( function_exists( 'citadela_theme_post_locations_list' ) ) {
            citadela_theme_post_locations_list( $separator, $styles );
        } else {
        	if ( 'post' === get_post_type() ) {
		    	global $post;
		    	$locations = get_the_terms($post->ID, 'citadela-post-location');
				$locations = apply_filters( 'the_category_list', $locations, $post->ID);

		        if ( $locations && ! is_wp_error( $locations ) ) {
		        	
					$itemDataLocationStyle = ! empty( $styles ) && isset( $styles['itemDataLocationStyle'] ) ? $styles['itemDataLocationStyle'] : "";

					$loc_links = [];
					foreach ($locations as $loc) {
		            	array_push(
		            		$loc_links, 
		            		'<a href="' . esc_url( add_query_arg( array( 'ctdl' => 'true', 'post_type' => 'post', 's' => '', 'category' => '', 'location' => $loc->slug ), get_home_url() ) ) . '" rel="location tag" ' . $itemDataLocationStyle . '>' . $loc->name . '</a>'
		            	);
						
					}
		            echo '<span class="locs-links">';
		            echo 	'<span class="locs-text">';
		            esc_html_e( 'Location', 'citadela' );
		            echo 	'</span> ';
		            echo 	'<span class="locs-list">';
		            echo 		implode($separator, $loc_links);
		            echo 	'</span>';
		            echo '</span>';
		        }
			}
	    }
    }


    public function post_thumbnail(  ) {
        if ( function_exists( 'citadela_theme_post_thumbnail' ) ) {
            citadela_theme_post_thumbnail();
        } else {
            if ( post_password_required() ) {
                return;
            }

            global $post;

            if ( is_singular() ) :

                if ( is_attachment() && wp_attachment_is_image( $post->id ) ) :
                ?>
                    <div class="post-thumbnail">
                        <a href="<?php echo esc_url( wp_get_attachment_url( $post->id ) ); ?>"
                                title="<?php the_title_attribute(); ?>" rel="attachment">
                            <?php echo wp_get_attachment_image($post->id, 'large'); ?>
                        </a>
                    </div><!-- .post-thumbnail -->

                <?php else : ?>

                    <div class="post-thumbnail">
                        <?php the_post_thumbnail(); ?>
                    </div><!-- .post-thumbnail -->
                <?php endif; ?>


            <?php else : ?>

                <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                    <?php
                    the_post_thumbnail( 'post-thumbnail', array(
                        'alt' => the_title_attribute( array(
                            'echo' => false,
                        ) ),
                    ) );
                    ?>
                </a>

            <?php
            endif; // End is_singular().
        }
    }

    public function posted_on( $styles = [] ) {
        if ( function_exists( 'citadela_theme_posted_on' ) ) {
            citadela_theme_posted_on( $styles );
        } else {
            
            $links_style = ! empty( $styles ) && isset( $styles['entryMetaLinksStyle'] ) ? $styles['entryMetaLinksStyle'] : "";

			$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
			if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
				$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
			}

			$time_string = sprintf( $time_string,
				esc_attr( get_the_date( DATE_W3C ) ),
				esc_html( get_the_date() ),
				esc_attr( get_the_modified_date( DATE_W3C ) ),
				esc_html( get_the_modified_date() )
			);

			$archiveYear  = get_the_time('Y');
			$archiveMonth = get_the_time('m');
			$archiveDay   = get_the_time('d');
			$archiveLink = get_day_link( $archiveYear, $archiveMonth, $archiveDay );

			echo '<span class="posted-on">';
					/* translators: Posted on [post date]. */
	        echo 	'<span class="posted-on-text">';
	        esc_html_e( 'Posted on', 'citadela' );
	        echo '</span> ';
			echo 	'<span class="posted-on-date"><a href="' . esc_url( $archiveLink ) . '" rel="bookmark" ' . $links_style . '>' . $time_string . '</a></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</span>';
        }
    }

    public function posted_on_data() {
        $archiveYear  = get_the_time('Y');
		$archiveMonth = get_the_time('m');
		$archiveDay   = get_the_time('d');
		$archiveLink = get_day_link( $archiveYear, $archiveMonth, $archiveDay );

		return (object) [
			'date'	=> esc_html( get_the_date() ),
			'year' 	=> esc_html( $archiveYear ),
			'month' => esc_html( $archiveMonth ),
			'day' 	=> esc_html( $archiveDay ),
			'monthText' => (object) [
					'full' => esc_html( get_the_time('F') ),
					'short' => esc_html( get_the_time('M') ),
				],
			'link'	=> (object) [
					'year' 	=> esc_url( get_year_link( $archiveYear ) ),
					'month' => esc_url( get_month_link( $archiveYear, $archiveMonth ) ),
					'day' 	=> esc_url( get_day_link( $archiveYear, $archiveMonth, $archiveDay ) ),
				],
		];
    }

    public function posted_by( $styles = [] ) {
        if ( function_exists( 'citadela_theme_posted_by' ) ) {
            citadela_theme_posted_by( $styles );
        } else {
        	
        	$links_style = ! empty( $styles ) && isset( $styles['entryMetaLinksStyle'] ) ? $styles['entryMetaLinksStyle'] : "";

			if( is_single() ){
				global $post;
				$authorName = get_the_author_meta( 'display_name', $post->post_author);
				$authorUrl = get_author_posts_url( $post->post_author);
			}else{
				$authorUrl = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
				$authorName = esc_html( get_the_author() );
			}
			echo '<span class="byline">';
					/* translators: [posted] by [post author]. */
	        echo 	'<span class="byline-text">';
	        esc_html_e( 'by', 'citadela' );
	        echo '</span> ';
			echo 	'<span class="author vcard"><a class="url fn n" href="' . esc_url($authorUrl) . '" ' . $links_style . '>' . esc_html($authorName) . '</a></span>';
			echo '</span>';
        }
    }

    public function leave_comment( $styles = [] ) {
        if ( function_exists( 'citadela_theme_leave_comment' ) ) {
            citadela_theme_leave_comment( $styles );
        } else {
            if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {

				$post_title = get_the_title();
				$number = get_comments_number();


				$leave_comment =
					'<span class="comments-number">0</span> '.
					'<span class="comments-text">'.
						sprintf(
							wp_kses(
								/* translators: %s: post title */
								__( 'Comments<span class="screen-reader-text"> on %s</span>', 'citadela' ),
								array(
									'span' => array(
										'class' => array(),
									),
								)
							),
							$post_title
						).
					'</span>';

				$one_comment =
					'<span class="comments-number">1</span> '.
					/* translators: %s: post title */
					'<span class="comments-text">' . sprintf( __( 'Comment<span class="screen-reader-text"> on %s', 'citadela' ), $post_title ) . '</span>';

				$more_comments =
					'<span class="comments-number">' . $number . '</span> '.
					/* translators: %s: post title */
					'<span class="comments-text">' . sprintf( __( 'Comments<span class="screen-reader-text"> on %s', 'citadela' ), $post_title ) . '</span>';

				$commentsLinkStyle = ! empty( $styles ) && isset( $styles['commentsLinkStyle'] ) ? $styles['commentsLinkStyle'] : "";

				echo '<span class="comments-link" ' . $commentsLinkStyle . '>';
					comments_popup_link(
						$leave_comment,
						$one_comment,
						$more_comments
					);
				echo '</span>';
			}
        }
    }

	public function registerFrontendAjax() {

		$ajaxActions = array();
		$ajaxFunctionPrefix = 'wpajax_';
		foreach($this->configGeneral->frontendAjax as $classId){
			$class = $this->id2class($classId, 'Ajax', $this->pluginClassPrefix);

			$instance = new $class();

			$methods = get_class_methods($class);

			foreach($methods as $method){
				if(substr($method, 0, 7) === $ajaxFunctionPrefix){
					$ajaxActions["{$classId}:{$method}"] = "{$classId}:{$method}";
					add_action("wp_ajax_{$classId}:{$method}", array($instance, $method)); // Authenticated actions
					add_action("wp_ajax_nopriv_{$classId}:{$method}", array($instance, $method)); // Non-admin actions
				}
			}
		}

		$this->setAjaxActions($ajaxActions);
	}

	public function setAjaxActions( $callbacks ) {
		$this->ajaxActions = $callbacks;
	}

	public function initGlobalJsVariables() {
		
		$settings = $this->getGlobalJsSettings();

		?>
		<script type="text/javascript">
			var CitadelaDirectorySettings = <?php echo json_encode( $settings ); ?>
		</script>
		<?php

	}

	public function getGlobalJsSettings() {
		global $post;

		$special_pages = [];
		foreach (CitadelaDirectoryLayouts::specialPages() as $key => $data) {
			$special_pages[$key] = CitadelaDirectoryLayouts::getSpecialPageId($key);
		}

		$settings = array(
			'home' => array(
				'url' => home_url(),
			),
			'wpSettings' => array(
				'postsPerPage' => get_option('posts_per_page'),
			),
			'ajax' => array(
				'url'     => admin_url('admin-ajax.php'),
				'actions' => array(),
			),
			'paths' => array(
				'css' => $this->paths->url->css,
				'assets' => $this->paths->url->assets,
			),
			'images' => $this->paths->url->images,
			'keys' => array(
				'recaptchaSiteKey' => CitadelaDirectoryRecaptcha::$siteKey,
			),
			'specialPages' => $special_pages,
			'l10n' => array(
				'datetimes' => array(
					'startOfWeek' => get_option('start_of_week'),
				),
			),
			'features' => array(
				'item_reviews' => \Citadela\Directory\ItemReviews::$enabled,
				'item_extension' => \Citadela\Directory\ItemExtension::$enabled,
			),
			'options' => array(
				'item_extension' => \Citadela\Directory\ItemExtension::$options,
				'item_detail' => get_option('citadela_directory_item_detail'),
			),
		);
		
		if( class_exists('woocommerce') && $this->Subscriptions_instance->enabled ){
			foreach (get_posts([
                'numberposts' => -1,
                'post_type' => 'product',
                'meta_key' => '_subscription_price',
            ]) as $subscription) {
            	$product = wc_get_product($subscription->ID);
            	if( $product->is_type('subscription') ){
					$settings['citadelaSubscriptionProducts'][] = [
						'id' => $subscription->ID,
						'post_name' => $subscription->post_name,
						'post_title' => $subscription->post_title,
					];
            	}
            }
		}
		
		$settings['ajax']['actions'] = $this->ajaxActions;

		if( $post ){
			$settings['currentPost'] = array(
				'post_id' => $post->ID,
				'post_type' => $post->post_type,
			);
		}


		// admin variables
		if( is_admin() ){
			$settings['itemCategoryTerms'] = [];
			$screen = get_current_screen();
			$settings['current_screen'] = $screen;
	        if( $screen && $screen->post_type == 'citadela-item' ) {
				$terms = get_terms( 'citadela-item-category' , [ 'hide_empty' => false ] );
				foreach ($terms as $term) {
                    $meta = get_term_meta( $term->term_id, 'citadela-item-category-meta', true );
                    $settings['itemCategoryTerms'][$term->term_id] = [
                            'term_name' => htmlspecialchars_decode( $term->name ),
                            'term_slug' => $term->slug,
                            'term_meta' => $meta,
                        ];
                }
			}
		}

		return $settings;
	}

	public function enqueueScripts() {
		$this->enqueueLeafletAssets();
		
		// plugin assets css files
		$citadela_assets_css = $this->configGeneral->assets->frontend->css;
		if (is_array($citadela_assets_css)){
			foreach ($citadela_assets_css as $fileHandle => $fileData) {
				$this->enqueueAssetFile('css', $fileHandle, $fileData);
			}
		}

		// plugin assets javascript files
		$citadela_assets_js = $this->configGeneral->assets->frontend->js;
		if (is_array($citadela_assets_js)){
			foreach ($citadela_assets_js as $fileHandle => $fileData) {
				$this->enqueueAssetFile('js', $fileHandle, $fileData);
			}
		}

		// plugin css files
		$citadela_styles = $this->configGeneral->styles->frontend;
		if (is_array($citadela_styles)){
			foreach ($citadela_styles as $fileHandle => $fileData) {
				$this->enqueuePluginFile('css', $fileHandle, $fileData);
			}
		}

		// plugin javascript files
		$citadela_scripts = $this->configGeneral->scripts->frontend;
		if (is_array($citadela_scripts)){
			foreach ($citadela_scripts as $fileHandle => $fileData) {
				$this->enqueuePluginFile('js', $fileHandle, $fileData);
			}
		}
		
	}

	public function enqueueAdminScripts() {
		
		wp_enqueue_script( 'underscore' );
		
		// plugin assets css files
		$citadela_assets_css = $this->configGeneral->assets->backend->css;
		if (is_array($citadela_assets_css)){
			foreach ($citadela_assets_css as $fileHandle => $fileData) {
				$this->enqueueAssetFile('css', $fileHandle, $fileData);
			}
		}

		// plugin assets javascript files
		$citadela_assets_js = $this->configGeneral->assets->backend->js;
		if (is_array($citadela_assets_js)){
			foreach ($citadela_assets_js as $fileHandle => $fileData) {
				$this->enqueueAssetFile('js', $fileHandle, $fileData);
			}
		}

		// plugin css files
		$citadela_styles = $this->configGeneral->styles->backend;
		if (is_array($citadela_styles)){
			foreach ($citadela_styles as $fileHandle => $fileData) {
				$this->enqueuePluginFile('css', $fileHandle, $fileData);
			}
		}

		// plugin javascript files
		$citadela_scripts = $this->configGeneral->scripts->backend;
		if (is_array($citadela_scripts)){
			foreach ($citadela_scripts as $fileHandle => $fileData) {
				$this->enqueuePluginFile('js', $fileHandle, $fileData);
			}
		}


	}

	public function enqueuePluginFile($fileType, $fileHandle, $fileData) {
		switch ($fileType) {
			case 'css':
				wp_enqueue_style( 	$fileHandle,
									$this->paths->url->css . '/' . $fileData['file'],
									$fileData['deps'],
									$fileData['ver']
				);
				break;
			case 'js':
				if( defined( 'CITADELA_THEME') ){
					//do not enqueue javascripts enqueued by theme
					if( ! in_array( $fileHandle, [ 'citadela-directory-fancybox' ] ) ) {
						wp_enqueue_script( 	$fileHandle,
											$this->paths->url->js . '/' . $fileData['file'],
											$fileData['deps'],
											$fileData['ver'],
											true //in footer
						);
					}
				}else{
					wp_enqueue_script( 	$fileHandle,
										$this->paths->url->js . '/' . $fileData['file'],
										$fileData['deps'],
										$fileData['ver'],
										true //in footer
					);
				}
				break;
			default:
				return;
		}
	}

	public function enqueueAssetFile($fileType, $fileHandle, $fileData) {

		switch ($fileType) {
			case 'css':
				wp_enqueue_style( 	$fileHandle,
									$this->paths->url->assets . '/' . $fileData['file'],
									$fileData['deps'],
									$fileData['ver']
				);
				break;
			case 'js':
				wp_enqueue_script( 	$fileHandle,
									$this->paths->url->assets . '/' . $fileData['file'],
									$fileData['deps'],
									$fileData['ver'],
									true //in footer
				);
				break;
			default:
				return;
		}
	}

	public function enqueueGoogleMasApi() {
        $api_key = isset( $this->pluginOptions->general['google_maps_api_key'] ) ? $this->pluginOptions->general['google_maps_api_key'] : '' ;
        wp_enqueue_script('citadela-google-maps', "//maps.google.com/maps/api/js?key={$api_key}&language=", [] );
    }

	public function enqueueLeafletAssets() {
        $assets = $this->configGeneral->assets->leaflet->js;
        foreach ($assets as $handle => $asset) {
            wp_enqueue_script( $handle, "{$this->paths->url->assets}/{$asset['file']}", $asset['deps'], $asset['ver'], false );            
        }
        $css = $this->configGeneral->assets->leaflet->css;
        foreach ($css as $handle => $asset) {
            wp_enqueue_style(   $handle, "{$this->paths->url->assets}/{$asset['file']}", $asset['deps'], $asset['ver'] );
        }
    }

	public function registerBlocks() {
		new \Citadela\Directory\Blocks\Feature();
	}

	public function registerSidebars() {
			$sidebars = array(
			array(
				'id'            => 'item-sidebar',
				'class'         => 'item-sidebar',
				'name'          => esc_html__( 'Item widgets area', 'citadela-directory' ),
				'description'   => esc_html__( 'Widgets displayed on Item posts pages.',  'citadela-directory' ),
			),
			array(
				'id'            => 'item-category-sidebar',
				'class'         => 'item-category-sidebar',
				'name'          => esc_html__( 'Item Categories widgets area',  'citadela-directory' ),
				'description'   => esc_html__( 'Widgets displayed on Item Categories pages.',  'citadela-directory' ),
			),
			array(
				'id'            => 'item-location-sidebar',
				'class'         => 'item-location-sidebar',
				'name'          => esc_html__( 'Item Locations widgets area',  'citadela-directory' ),
				'description'   => esc_html__( 'Widgets displayed on Item Locations pages.',  'citadela-directory' ),
			),
		);

		$defaults = array(
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => '',
		);

		foreach ($sidebars as $sidebar) {
			$args = array_merge( $sidebar, $defaults );
			register_sidebar( $args );
		}
	}

	public function getCurrentLocale() {
		return get_locale();
	}

	public function getCurrentLanguageCode() {
		$locale = get_locale();
		if($locale == 'zh_CN'){
			return 'cn';
		}elseif($locale == 'zh_TW'){
			return 'tw';
		}elseif($locale == 'pt_BR'){
			return 'br';
		}else{
			return substr($locale, 0, 2);
		}
	}

	public function getPluginOptions() {
		$tabs = CitadelaDirectorySettings::getNavigationTabs();
		$pluginOptions = array();
		foreach ($tabs as $tabId => $tabData) {
			$tabOptions = CitadelaDirectorySettings::getTabSettings($tabId);
			$pluginOptions[$tabId] = $tabOptions ? $tabOptions : array();
		}
		return (object) $pluginOptions;
	}

	public function upload_mimes( $mime_types ) {
		//support gpx files upload
		$mime_types['gpx'] = 'text/xml';
		
		return $mime_types;
	}

	public function activation() {
		CitadelaItem::citadelaRegisterCpt();
		CitadelaItem::citadelaRegisterTax();
		flush_rewrite_rules();
		CitadelaItem::citadelaAddCptCapabilities();
	}

	public function deactivation() {
		CitadelaItem::citadelaRemoveCptCapabilities();
		flush_rewrite_rules();
	}



	/* ************ helper methods ********** */

	public static function hex2rgb($hexColor) {
		if ($hexColor[0] == '#')
			$hexColor = substr($hexColor, 1);

		if (strlen($hexColor) == 6)
			list($r, $g, $b) = array($hexColor[0].$hexColor[1], $hexColor[2].$hexColor[3], $hexColor[4].$hexColor[5]);
		elseif (strlen($hexColor) == 3)
			list($r, $g, $b) = array($hexColor[0].$hexColor[0], $hexColor[1].$hexColor[1], $hexColor[2].$hexColor[2]);
		else
			return array('r' => 'you', 'g' => 'entered wrong', 'b' => "hex color: $hexColor");

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		return array('r' => $r, 'g' => $g, 'b' => $b);
	}

	public static function rgba2hex($string) {
		$string = trim($string);

		if( substr($string, 0, 4) == 'rgba' ){

			$values = array_map('trim', explode(',', substr($string, 5, -1)));

			$a = array_pop($values);

			$out = "#";

			foreach ($values as $c)
			{
				$hex = base_convert($c, 10, 16);
				$out .= ($c < 16) ? ("0" . $hex) : $hex;
			}

			$return = (object) array(
				'hex' => $out,
				'opacity' => $a * 100,
				'a' => $a,
			);

			return $return;
		}else{
			return (object) array(
				'hex' => $string,
				'opacity' => 100,
				'a' => 1,
			);
		}
	}

	public function id2class($id, $suffix, $prefix = '') {
		$prefix = ($prefix) ? $prefix : 'Citadela';
		return $prefix . ucfirst(self::dash2camel($id)) . ucfirst($suffix);
	}

	public static function dash2camel($s) {
		$s = self::_2class($s);
		$s[0] = strtolower($s[0]);
		return $s;
	}

	public static function _2class($s) {
		$s = ucwords(strtolower(str_replace(array('-', '_'), ' ', $s)));
		return str_replace(' ', '', $s);
	}

    public static function camel2dash($s)
	{
		$s = preg_replace('#(.)(?=[A-Z])#', '$1-', $s);
		$s = strtolower($s);
		return $s;
	}

	public static function getInstance() {
		if(!self::$instance){
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function autoload($class) {

		if(substr($class, 0, 19) === 'Citadela\Directory\\'){ // starts with
		    $filename = str_replace(['Citadela\Directory\\', '\\'], ['', '/'], $class);
		    $file = __DIR__ . "/less/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
		    }

		    $filename = str_replace(['Citadela\Directory\\', '\\'], ['', '/'], $class);
		    $file = __DIR__ . "/migration/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
		    }

            $filename = str_replace(['Citadela\Directory\Blocks\\', '\\'], ['', '/'], $class);
            $file = __DIR__ . "/../blocks/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
            }

            // autoload blocks classes
            $filename = str_replace(['Citadela\Directory\Blocks\\', '\\'], ['', '/'], $class);
            $blockId = self::camel2dash($filename);
            $file = __DIR__ . "/../blocks/{$blockId}/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
			}
			
			$filename = str_replace(['Citadela\Directory\Subscriptions\\', '\\'], ['', '/'], $class);
            $file = __DIR__ . "/subscriptions/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
            }

            $filename = str_replace(['Citadela\Directory\\', '\\'], ['', '/'], $class);
		    $file = __DIR__ . "/easy-admin/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
		    }

			$filename = str_replace(['Citadela\Directory\\', '\\'], ['', '/'], $class);
		    $file = __DIR__ . "/events/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
		    }

		    $filename = str_replace(['Citadela\Directory\\', '\\'], ['', '/'], $class);
		    $file = __DIR__ . "/item-reviews/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
		    }

		    $filename = str_replace(['Citadela\Directory\HalfLayoutMap\\', '\\'], ['', '/'], $class);
            $file = __DIR__ . "/half-layout-map/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
			}

			$filename = str_replace(['Citadela\Directory\ItemPageLayout\\', '\\'], ['', '/'], $class);
            $file = __DIR__ . "/item-page-layout/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
			}
			
			$filename = str_replace(['Citadela\Directory\\', '\\'], ['', '/'], $class);
		    $file = __DIR__ . "/claim-listing/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
		    }
		    $filename = str_replace(['Citadela\Directory\\', '\\'], ['', '/'], $class);
		    $file = __DIR__ . "/advanced-filters/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
		    }
		    $filename = str_replace(['Citadela\Directory\\', '\\'], ['', '/'], $class);
		    $file = __DIR__ . "/item-extension/{$filename}.php";
		    if($file and file_exists($file)){
		        require_once $file;
		        return;
		    }
		}

		$file = '';
		$loadPaths = array(
					'/plugin/ajax',
					'/plugin/controls',
					'/plugin/controls/inputs',
					'/plugin/controls/butterbean',
					'/plugin/controls/butterbean/assets',
					'/plugin/cpt',
					'/plugin/cpt/item',
					'/plugin/cpt/post',
					'/plugin/cpt/user',
					'/plugin/includes',
					'/plugin/settings',
					'/plugin/settings/pages',
				);

		foreach ($loadPaths as $path) {

			if(substr($class, 0, 8) === 'Citadela'){
				$file = $this->basedir . $path . "/{$class}.php";
			}

			if($file and file_exists($file)){
				require_once $file;
			}

		}
	}

    public function registerImageSizes() {
        add_image_size( 'citadela_item_thumbnail', 640, 480, true );
    }

	//only for internal use, get fontawesome icons list from complete fontawesome.json to fontawesome picker (picker missing newer icons)
    private function faJsonToPicker() {
    	return;
    	$json = file_get_contents($this->paths->url->assets . "/fontawesome/json/fontawesome.json");
		foreach (json_decode($json) as $iconName => $iconData) {
			$styles = $iconData->styles;
			foreach ($styles as $style) {
				switch ($style) {
					case 'regular':
						$type = 'far';
						break;
					case 'solid':
						$type = 'fas';
						break;
					case 'brands':
						$type = 'fab';
						break;
					default:
						break;
				}

				$terms = $iconData->search->terms;
				$termString = '';
				foreach ($terms as $term) {
					$termString .= '"'.$term.'",';
				}
				$termString = substr($termString, 0, -1);
				echo '{title:"'.$type.' fa-'.$iconName.'",searchTerms:['.$termString.']},';

			}
		}
	}
	
	public static function body_class( $classes ) {
		global $post;
		$special_page = false;

		if ( is_singular( 'citadela-item' ) ) {
			$special_page = 'single-item';
		}
		
		if ( is_tax( 'citadela-item-category' ) ) {
			$special_page = 'item-category';
		}
		
		if ( is_tax( 'citadela-item-location' ) ) {
			$special_page = 'item-location';
		}
		
		if ( is_search() && isset( $_REQUEST[ 'ctdl' ] ) ) {
			if ( $_REQUEST[ 'post_type' ] == 'post' ) {
				$special_page = 'posts-search-results';
			} else {
				$special_page = 'search-results';
			}
		}
		
		if ( $special_page ) {
			
			$ignore_special_page = self::$instance->ItemPageLayout_instance->ignore_special_page;

			$post_id = $special_page == 'single-item' && $ignore_special_page ? $post->ID : CitadelaDirectoryLayouts::getSpecialPageId( $special_page );
			
			//if we are in special page, we need remove classes first and then check meta with special page ID
			if (($key = array_search('no-page-title', $classes)) !== false) {
				unset($classes[$key]);
			}
			if (($key = array_search('is-page-title', $classes)) !== false) {
				unset($classes[$key]);
			}
			if (($key = array_search('no-header-space', $classes)) !== false) {
				unset($classes[$key]);
			}

			$hide_page_title = get_post_meta( $post_id, '_citadela_hide_page_title', true );
			$classes[] = $hide_page_title ? 'no-page-title' : 'is-page-title';
			$remove_header_space = get_post_meta( $post_id, '_citadela_remove_header_space', true );
			$classes[] = $remove_header_space ? 'no-header-space' : '';
		}

		return $classes;
	}

	public function fixFeaturedItems() {
		if (!get_option('citadela_directory_featured_items')) {
			global $wpdb;
			$items = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} p WHERE post_type = 'citadela-item' AND (SELECT count(meta_id) FROM {$wpdb->postmeta} WHERE p.ID = post_id AND meta_key = '_citadela_featured') = 0 LIMIT 1000", ARRAY_N);
			if (count($items)) {
				foreach ($items as $item) {
					update_post_meta($item[0], '_citadela_featured', '');
				}
			} else {
				update_option('citadela_directory_featured_items', true);
			}
		}
	}

	public function updateSpecialPagesNames() {
		if (!get_option('citadela_directory_update_special_pages_names')) {
			$map_names = [ 'Directory Search Results', 'Directory Category Page', 'Directory Location Page' ];
			$special_pages = CitadelaDirectoryLayouts::specialPages();
			foreach ($special_pages as $key => $data) {
				$id = get_option( $data['option_key'] );
        		$page = get_post( $id );
        		if( $page && in_array( $page->post_title, $map_names ) ){
					wp_update_post( [ 'ID' => $id, 'post_title' => str_replace( 'Directory', 'Listing', $page->post_title ) ] );
        		}
			}
			update_option('citadela_directory_update_special_pages_names', true);
		}
	}

	public function is_googlebot($ip) {
        @$host = gethostbyaddr($ip);
        @$ip2 = gethostbyname($host);

        if ($ip == $ip2 && preg_match('/(googlebot|google).com$/', $host)) {
                return true;
        } else {
                return false;
        }
	}

	public function get_the_user_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
}
