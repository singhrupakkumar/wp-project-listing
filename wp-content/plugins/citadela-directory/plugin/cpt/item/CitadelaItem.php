<?php

// ===============================================
// Citadela Listing Item CPT
// -----------------------------------------------


class CitadelaItem {

	public static $plugin, $config;
	public static $cptCodeName, $taxCategoryCodeName, $taxLocationCodeName;

	public function __construct(){
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}

	public static function init() {
		$cptCodeName = 'citadela-item';

		self::$cptCodeName = $cptCodeName;
		self::$taxCategoryCodeName = $taxCategoryCodeName = "{$cptCodeName}-category";
		self::$taxLocationCodeName = $taxLocationCodeName = "{$cptCodeName}-location";
		self::$plugin = CitadelaDirectory::getInstance();
		self::$config = CitadelaItemConfig::getData();

		add_action('after_setup_theme', function () use ($taxCategoryCodeName, $taxLocationCodeName) {
			if (Citadela::$allowed) {

				CitadelaItemMetabox::run();

				add_action('init', array(__CLASS__, 'citadelaOnInit'), 20, 0);

				add_filter('single_template', array(__CLASS__, 'citadelaLoadSingleCptTemplate') );
				add_filter('archive_template', array(__CLASS__, 'citadelaLoadArchiveTemplate') );
				add_filter('taxonomy_template', array(__CLASS__, 'citadelaLoadTaxonomyTemplate') );

				add_action($taxCategoryCodeName.'_add_form_fields', array(__CLASS__, 'add_taxonomy_form_fields'), 10, 2 );
				add_action($taxCategoryCodeName.'_edit_form_fields', array(__CLASS__, 'edit_taxonomy_form_fields'), 10, 2 );
				add_action("edited_{$taxCategoryCodeName}", array(__CLASS__, 'save_item_taxonomy_form_fields'), 10, 2);
				add_action("created_{$taxCategoryCodeName}", array(__CLASS__, 'save_item_taxonomy_form_fields'), 10, 2);

				add_action($taxLocationCodeName.'_add_form_fields', array(__CLASS__, 'add_taxonomy_form_fields'), 10, 2 );
				add_action($taxLocationCodeName.'_edit_form_fields', array(__CLASS__, 'edit_taxonomy_form_fields'), 10, 2 );
				add_action("edited_{$taxLocationCodeName}", array(__CLASS__, 'save_item_taxonomy_form_fields'), 10, 2);
				add_action("created_{$taxLocationCodeName}", array(__CLASS__, 'save_item_taxonomy_form_fields'), 10, 2);


				add_action('admin_enqueue_scripts', array(__CLASS__, 'citadelaEnqueueAdminPageWpMediaJs'));

				add_filter( 'use_block_editor_for_post_type', function($use_block_editor, $post_type) {
					if ($post_type == 'citadela-item' && ! self::$plugin->ItemPageLayout_instance->allowed_editor) {
						return false;
					}
					return $use_block_editor;
				}, 10, 2 );

				if( self::$plugin->ItemPageLayout_instance->allowed_editor ){
					//we need to support custom fields for Item post in order to use custom Citadela options on item pages (options like Hide page title, Custom Header etc...)
					add_post_type_support( 'citadela-item', 'custom-fields' );
				}
				add_action( 'admin_footer', [ __CLASS__, 'admin_footer' ] );

			}
		}, 100);

	}

	public static function citadelaOnInit() {

		self::citadelaRegisterCpt();
		self::citadelaRegisterTax();
		
		$taxCategoryCodeName = self::$taxCategoryCodeName;
		add_filter( "manage_edit-{$taxCategoryCodeName}_columns", [ __CLASS__, 'manage_item_category_columns'], 10);
		add_filter( "manage_{$taxCategoryCodeName}_custom_column", [ __CLASS__, 'manage_item_category_custom_column'], 10, 3 );

		$taxLocationCodeName = self::$taxLocationCodeName;
		add_filter( "manage_edit-{$taxLocationCodeName}_columns", [ __CLASS__, 'manage_item_location_columns'], 10);
		add_filter( "manage_{$taxLocationCodeName}_custom_column", [ __CLASS__, 'manage_item_location_custom_column'], 10, 3 );
		
		// if allowed editor and user has comments permissions, add Comments metabox (this metabox isn't added by default in editor)
	    $add_comments_metabox = false;
	    $allowed_editor = self::$plugin->ItemPageLayout_instance->allowed_editor;
	    if( $allowed_editor ){
		    if( \Citadela\Directory\ItemReviews::$enabled ){
		        if( \Citadela\Directory\Subscriptions\Feature::getPermission('comments') && ! \Citadela\Directory\Subscriptions\Feature::getPermission('reviews') ){
		        	$add_comments_metabox = true;
		        }
		    }else{
		    	if( \Citadela\Directory\Subscriptions\Feature::getPermission('comments') ){
		    		$add_comments_metabox = true;
		        }
		    }
	    }

	    if( $add_comments_metabox ){
	    	if( $allowed_editor ) {
	    		add_filter( 'comment_row_actions', [ __CLASS__, 'comment_row_actions' ], 10, 2 );
	    	}
        	add_action( 'add_meta_boxes', [ __CLASS__, 'add_meta_boxes_comments' ], 10, 2 );
        	add_action( 'enqueue_block_editor_assets', [ __CLASS__, 'enqueue_block_editor_assets' ] );
	    }

	    add_filter( 'get_comment_author_IP', [ __CLASS__, 'get_comment_author_IP' ], 10, 3 );
	    
	}


	public static function citadelaRegisterCpt() {
		$cpt = self::$config->cpt;
		$args = $cpt->args;
		$args['labels'] = $cpt->labels;
		register_post_type( self::$cptCodeName, $args );

	}

	public static function citadelaRegisterTax() {
		$taxonomies = self::$config->taxonomies;
		foreach ($taxonomies as $tax => $data) {
			$args = $data['args'];
			$args['labels'] = $data['labels'];
			register_taxonomy( self::$cptCodeName . '-' . $tax, self::$cptCodeName, $args );
		}
	}

	/*
	*	Modify Single CPT post template file path
	*/
	public static function citadelaLoadSingleCptTemplate( $single_template ) {
		$post = get_queried_object();

		if($post->post_type !== self::$cptCodeName) return $single_template;

		$theme_template_file = locate_template('single-item.php');
		$plugin_template_file = self::$plugin->paths->dir->cpt . '/item/templates/single-item.php';
		if( $theme_template_file ){
            return $theme_template_file;
		}else{
            return $plugin_template_file;
		}

	}

	/*
	*	Modify CPT archive template file path
	*/
	public static function citadelaLoadArchiveTemplate( $archive_template ) {
		$postType = get_query_var('post_type');
		if( $postType !== self::$cptCodeName ) return $archive_template;

		$theme_template_file = locate_template('archive-item.php');
		$plugin_template_file = self::$plugin->paths->dir->cpt . '/item/templates/archive-item.php';
		if( $theme_template_file ){
			return $theme_template_file;
		}else{
			return $plugin_template_file;
		}
	}


	/*
	*	Modify taxonomy template file path
	*/
	public static function citadelaLoadTaxonomyTemplate( $taxonomy_template ) {
		$term = get_queried_object();
		if($term->taxonomy != self::$cptCodeName . '-category' && $term->taxonomy != self::$cptCodeName . '-location') return $taxonomy_template;

		$originalTax = $term->taxonomy;
		$unprefixedTax = str_replace(self::$cptCodeName.'-', '', $originalTax);

		$theme_template_file = locate_template("taxonomy-{$unprefixedTax}.php");
		$plugin_template_file = self::$plugin->paths->dir->cpt . "/item/templates/taxonomy-{$unprefixedTax}.php";

		if( $theme_template_file ){
			return $theme_template_file;
		}else{
			return $plugin_template_file;
		}
	}


	public static function citadelaGetTermChilds( $term_id, $taxonomy, $meta_query_args = [] ){

		$meta_query = [];
		
		if ( ! empty( $meta_query_args ) ){
			if( isset( $meta_query_args['featured'] ) && $meta_query_args['featured'] ) {
				$meta_query[] = [
					'key'       => "{$taxonomy}-featured",
					'value'     => 1,
					'compare'   => '='
				];		
			}
		}
		
		$args = [
			'taxonomy'		=> $taxonomy,
			'parent'   		=> $term_id,
			'orderby'		=> 'title',
			'hide_empty'	=> true,
			'meta_query' 	=> $meta_query
		];
		
		if( ! empty( $meta_query ) ){
			$args['meta_query'] = $meta_query;
		}

		$query = new WP_Term_Query($args);
		return $query->get_terms();
	}

	public static function citadelaAddCptCapabilities() {
		$roles = self::citadelaGetPluginRelatedRoles();
		foreach ($roles as $roleName) {
			$role = get_role($roleName);
			$cptCaps = self::$config->cpt->args['capabilities'];
			foreach ($cptCaps as $cap) {
				$role->add_cap($cap);
			}

			$taxonomies = self::$config->taxonomies;
			foreach ($taxonomies as $tax => $data) {
				$taxCaps = $data['args']['capabilities'];
				foreach ($taxCaps as $cap) {
					$role->add_cap($cap);
				}
			}
		}

	}


	public static function citadelaRemoveCptCapabilities() {
		$roles = self::citadelaGetPluginRelatedRoles();
		foreach ($roles as $roleName) {
			$role = get_role($roleName);
			$cptCaps = self::$config->cpt->args['capabilities'];
			foreach ($cptCaps as $cap) {
				$role->remove_cap($cap);
			}

			$taxonomies = self::$config->taxonomies;
			foreach ($taxonomies as $tax => $data) {
				$taxCaps = $data['args']['capabilities'];
				foreach ($taxCaps as $cap) {
					$role->remove_cap($cap);
				}
			}
		}

	}


	public static function citadelaGetPluginRelatedRoles() {
		return array(
					'administrator',
				);
	}


	public static function citadelaRenderTaxonomyFormFields( $data, $screenType ){
		
		if($data['taxonomy'] === 'citadela-item-category' ){
			$taxCodeName = self::$taxCategoryCodeName;
			$taxConfig = self::$config->categoryOptions;
		}

		if($data['taxonomy'] === 'citadela-item-location' ){
			$taxCodeName = self::$taxLocationCodeName;
			$taxConfig = self::$config->locationOptions;
		}

		// get array of general term meta data
		$termMeta = isset($data['tag']) ? get_term_meta($data['tag']->term_id, "{$taxCodeName}-meta", true) : array();

		foreach ($taxConfig as $sectionId => $inputs) {
			foreach ($inputs as $inputId => $inputConfig) {
				if( isset( $inputConfig['single-meta'] ) && $inputConfig['single-meta'] ){
					//set term meta to single meta value
					$termMeta = isset($data['tag']) ? get_term_meta($data['tag']->term_id, "{$taxCodeName}-{$inputId}", true) : '';
					$singleMeta = true;
				}else{
					//set term meta to general meta data saved in array
					$singleMeta = false;
				}
				
				CitadelaControl::citadelaRenderTaxonomyInput( $inputId, $inputConfig, $termMeta, $taxCodeName, $screenType, $singleMeta );
			}
		}
	}

	public static function add_taxonomy_form_fields( $taxonomy ) {
		$data = array(
				'taxonomy' 	=> $taxonomy
			);
		self::citadelaRenderTaxonomyFormFields( $data, 'add-term');
	}


	public static function edit_taxonomy_form_fields ($tag, $taxonomy ) {
		$data = array(
				'tag' 		=> $tag,
				'taxonomy' 	=> $taxonomy
			);
		self::citadelaRenderTaxonomyFormFields( $data, 'edit-term');
	}


	public static function save_item_taxonomy_form_fields( $term_id ) {
		$term = get_term( $term_id );

		if($term->taxonomy === 'citadela-item-category' ){
			$taxCodeName = self::$taxCategoryCodeName;
			$taxConfig = self::$config->categoryOptions;
		}

		if($term->taxonomy === 'citadela-item-location' ){
			$taxCodeName = self::$taxLocationCodeName;
			$taxConfig = self::$config->locationOptions;
		}

		$termMetaData = array();

		// we will save data for all inputs available in config file, to make sure all of them are saved (prevents not saved data for unchecked checkbox)
		if( isset( $_POST[$taxCodeName] ) ){
			//term is created or edited on Add/Edit Term page, we have access to selected custom data (icon, color.....)
			foreach ($taxConfig as $sectionId => $inputs) {
				foreach ($inputs as $inputId => $inputConfig) {
					$value = CitadelaDirectoryFunctions::validate_saved_value( $_POST[$taxCodeName][$inputId], $inputConfig['type']  );
					if( isset( $inputConfig['single-meta'] ) && $inputConfig['single-meta'] ){
						//save this meta as single meta value under unique meta key
						update_term_meta($term_id, "{$taxCodeName}-{$inputId}", $value );
					}else{
						//save this meta in array under general meta key
						$termMetaData[$inputId] = $value;
					}
				}
			}
		}else{
			//term is added via Add New Item page, we have to set default values for custom data	
			foreach ($taxConfig as $sectionId => $inputs) {
				foreach ($inputs as $inputId => $inputConfig) {
					$value = CitadelaDirectoryFunctions::validate_saved_value( $_POST[$taxCodeName][$inputId], $inputConfig['type']  );
					if( isset( $inputConfig['single-meta'] ) && $inputConfig['single-meta'] ){
						//save this meta as single meta value under unique meta key
						update_term_meta($term_id, "{$taxCodeName}-{$inputId}", $value );
					}else{
						//save this meta in array under general meta key
						$termMetaData[$inputId] = isset($inputConfig['default']) ? $inputConfig['default'] : '';
					}
				}
			}
		}

		// save general term meta
		if( ! empty( $termMetaData ) ){
			update_term_meta($term_id, "{$taxCodeName}-meta", $termMetaData);
		}
	}

	public static function add_meta_boxes_comments( $post_type, $post ){
        if ( $post_type === 'citadela-item' ){
        	add_meta_box( 'commentsdiv', esc_html__( 'Comments', 'citadela-directory' ), 'post_comment_meta_box', 'citadela-item', 'normal' );
        }
    }


    public static function enqueue_block_editor_assets(){
        wp_enqueue_script( 'postbox' );
        wp_enqueue_script( 'post' );
        wp_enqueue_script( 'admin-comments' );
        wp_enqueue_script( 'edit-comments' );
        wp_enqueue_script( 'comment-reply' );
    }


	public static function citadelaEnqueueAdminPageWpMediaJs() {
		//necessary to use uploader for Citadela Image Select input type
		wp_enqueue_media ();
	}



	public static function manage_item_category_columns( $columns ){
		$columns['citadela_featured_item_category'] = esc_html__('Featured', 'citadela-directory');
		return $columns;
	}

	public static function manage_item_category_custom_column( $value, $column_name, $tax_id ) {
		 
		 switch ($column_name) {
		 	case 'citadela_featured_item_category':
		 		$featured = get_term_meta($tax_id, "citadela-item-category-featured", true);
		 		if( $featured ){
		 			echo '<i class="fas fa-star"></i>';
		 		}
		 		break;
		 	
		 	default:
		 		return;
		 		break;
		 }

		
	}

	public static function manage_item_location_columns( $columns ){
		$columns['citadela_featured_item_location'] = esc_html__('Featured', 'citadela-directory');
		return $columns;
	}

	public static function manage_item_location_custom_column( $value, $column_name, $tax_id ) {
		 
		 switch ($column_name) {
		 	case 'citadela_featured_item_location':
		 		$featured = get_term_meta($tax_id, "citadela-item-location-featured", true);
		 		if( $featured ){
		 			echo '<i class="fas fa-star"></i>';
		 		}
		 		break;
		 	
		 	default:
		 		echo '';
		 		break;
		 }

		
	}

	public static function get_comment_author_IP( $comment_author_IP, $comment_ID, $comment ){
        // do not show IP address for subscribers - IP address has link to Edit Comment screen which is not accessible for subscribers
        if( self::$plugin->Subscriptions_instance->enabled ){
            $user = wp_get_current_user();
            // check if current user is Subscriber and has active subscription
            if ( in_array( 'subscriber', (array) $user->roles ) && self::$plugin->Subscriptions_instance->user_has_active_subscription( intval( $user->ID ) ) ) {
                return "";
            }
        }
        return $comment_author_IP;
    }

    public static function comment_row_actions( $actions, $comment ){
    	global $pagenow;
    	
    	// remove action buttons only in metabox where comments are loaded via ajax, keep button on standard Comments page
    	if( $pagenow == 'admin-ajax.php' ){
	    	unset( $actions['reply'] );
	    	unset( $actions['quickedit'] );
    	}

    	return $actions;
    }
    public static function admin_footer(){
        $screen    = get_current_screen();
        $screen_id = $screen ? $screen->id : '';

        if( $screen_id === 'citadela-item' ):

        	if( self::$plugin->ItemPageLayout_instance->allowed_editor ):
        	?>

		    <script type="text/javascript">
		        // fix malfuncion of default wordpress metabox opening/closing in Gutenberg editor
		        jQuery('body.wp-admin.post-type-citadela-item').find('#normal-sortables .postbox').each(function(){
		            var $metabox = jQuery(this);
		            $metabox.find('.handlediv').on('click', function(){
		                if( $metabox.hasClass('closed') ){
		                    $metabox.removeClass('closed');
		                }else{
		                    $metabox.addClass('closed');
		                }
		            });
		        });
		    </script>

	        <?php endif; ?>

	        <script type="text/javascript">
		        // remove Add Comment button from Item Reviews metabox on Item Edit pages, this button adds simple Comment, not Item Review
		        jQuery('body.wp-admin.post-type-citadela-item').find('#commentsdiv').find('#add-new-comment').remove();
		    </script>
	    
	    <?php endif; ?>

	    <?php
    }

}

