<?php

// ===============================================
// Citadela Listing plugin custom functions
// -----------------------------------------------


class CitadelaDirectoryFunctions {

	protected static $plugin;

	public function __construct(){
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}

    public static function getBlocksByName( $parsed_blocks, $block_name ){
        
        $blocks = [];
        foreach ($parsed_blocks as $key => $data) {
            $found_blocks = self::maybeGetBlock( $data, $block_name );
            if( ! empty( $found_blocks ) ){
                foreach ( $found_blocks as $block ) {
                    $blocks[] = $block;
                }

            } 
        }

        return $blocks;

    }

    private static function maybeGetBlock( $block, $block_name)
    {

        // if wanted block, return it
        if( $block['blockName'] === $block_name ) {
            return [ $block ];
        }
        
        $blocks = [];

        // maybe it's reusable block with wanted block inside
        if( $block['blockName'] === 'core/block' && isset( $block['attrs']['ref'] ) ) {
            $parsed_reusable_block = parse_blocks( get_post( $block['attrs']['ref'] )->post_content );
            foreach ($parsed_reusable_block as $key => $data) {
                $found_blocks = self::maybeGetBlock( $data, $block_name );
                if( ! empty( $found_blocks ) ){
                    foreach ( $found_blocks as $block ) {
                        $blocks[] = $block;
                    }

                } 
            }
        }

        if( ! empty( $block['innerBlocks'] ) ) {
            foreach( $block['innerBlocks'] as $inner_block ) {
                $found_blocks = self::maybeGetBlock( $inner_block, $block_name );
                if( ! empty( $found_blocks ) ){
                    foreach ($found_blocks as $block) {
                        $blocks[] = $block;
                    }
                }
            }
        }
        return $blocks;
    }

    /* TODO: this is simplified version of getMarkerPinData function */
    public static function prepareMapPoint($post)
    {
        $track = get_post_meta($post->ID, '_citadela_gpx_track', true);
        $point = [
            'track' => $track ? json_decode( $track ) : [],
            'coordinates' => [
                'longitude' => (float) get_post_meta($post->ID, '_citadela_longitude', true),
                'latitude' => (float) get_post_meta($post->ID, '_citadela_latitude', true),
            ],
            'title' => $post->post_title,
            'permalink' => get_permalink($post->ID),
            'address' => get_post_meta($post->ID, '_citadela_address', true),
            'image' => get_the_post_thumbnail_url( $post->ID , 'thumbnail'),
            'postType' => $post->post_type,
        ];

        $point = array_merge( $point, self::prepareMarkerIcon($post));

		return $point;
    }

    /* TODO: this is simplified version of getMarkerPinData function */
    public static function prepareMarkerIcon($post)
    {
        $taxonomySlug = $post->post_type == 'citadela-item' ? 'citadela-item-category' : 'category';
        $taxonomyMetaKey = $post->post_type == 'citadela-item' ? 'citadela-item-category-meta' : 'citadela-post-category';

        $args = array('orderby'=> 'parent');

        $featured_category = get_post_meta($post->ID, '_citadela_featured_category', true);
        if( $featured_category ){
            $term = get_term( intval($featured_category) );
            if(!$term) return array(
                "faIcon"  => 'fas fa-circle',
                "color" => '',
            );
        }else{

            $terms = wp_get_post_terms( $post->ID, $taxonomySlug, $args);

            if(!$terms) return array(
                "faIcon"  => 'fas fa-circle',
                "color" => '',
            );

            $term = $terms[0];

        }

        $meta = (object) get_term_meta( $term->term_id, $taxonomyMetaKey, true );
        
        $category_icon = empty($meta->category_icon) ? 'fas fa-circle' : $meta->category_icon;
        $category_color = empty($meta->category_color) ? '' : $meta->category_color;

        return array(
            "faIcon"  => $category_icon,
            "color" => $category_color,
        );
    }



	/*
	*	get all custom post meta without citadela_ prefix
	*/
	public static function getItemMeta( $post_id ){
		$prefix = '_citadela_';
		$itemFields = CitadelaItem::$config->fields;

		$citadelaMeta = [];
		foreach ($itemFields as $metaboxName => $metaboxData) {
			foreach ($metaboxData as $inputName => $inputData) {
				if($inputData['type'] == 'citadela_map'){
					//custom input data
					foreach ($inputData['settings'] as $key => $metaKey) {
						$citadelaMeta[$key] = get_post_meta($post_id, $metaKey, true);
					}
				}else{
					//general butterbean inputs data
					$metaKey = $prefix.$inputName;
					$citadelaMeta[$inputName] = get_post_meta($post_id, $metaKey, true);
				}
			}

		}
		return (object) $citadelaMeta;
	}


    /*
    * $dataType = which data to get (tracks, markers, all...)
    */
    public static function guessMapEndpoint($attributes = [], $dataType = 'markers' )
    {
        $postType = isset($attributes['postType']) ? $attributes['postType'] : 'citadela-item';

        $data = [];
        
        if( $postType == 'citadela-item' && isset( $attributes['advanced_filters'] ) && $attributes['advanced_filters'] ){
            $data['advanced_filters']['filters'] = \Citadela\Directory\AdvancedFilters::$current_filters;
        }

        // if $attributes are not passed we assume special page and query should be from context
        // fixed-map is simple map which show predefined posts (Listing Google Map, Posts map), to recognize if it isn't automatic map
        if ( isset( $attributes['fixed-map'] ) && $attributes['fixed-map'] && $postType == 'citadela-item') {
			
            $data['dataType'] = $dataType;
            $data['category'] = $attributes['category'];
			$data['location'] = $attributes['location'];
			$data['only_featured'] = $attributes['onlyFeatured'];

        } elseif ( isset( $attributes['fixed-map'] ) && $attributes['fixed-map'] && $postType == 'post') {
            
            $data['dataType'] = $dataType;
            $data['category'] = $attributes['category'];
            $data['location'] = $attributes['location'];

        /* SEARCH RESULTS PAGE */
        } elseif ( is_search() && isset( $_REQUEST[ 'ctdl' ] ) ) {

            $postType = self::guessPostTypeFromContext();
            $data['citadela_search'] = $postType;
            $data['s'] = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';
            $data['dataType'] = $dataType;
            $data['category'] = isset($_REQUEST['category']) ? $_REQUEST['category'] : null;
            $data['location'] = isset($_REQUEST['location']) ? $_REQUEST['location'] : null;
            
            if( $postType == 'citadela-item' && isset( $attributes['advanced_filters'] ) && $attributes['advanced_filters'] ){
                $data['advanced_filters']['operators'] = \Citadela\Directory\AdvancedFilters::getFiltersOperator('search-results');
            }

            if( isset( $attributes['geolocation'] ) ){
                $data['geolocation'] = $attributes['geolocation'];
            }


        /* SINGLE ITEM PAGE or SINGLE POST PAGE */
		} elseif (is_singular('citadela-item') || is_singular('post')) {
            
            global $post;
            $data['dataType'] = $dataType;
            $queryString = build_query( $data );
            return site_url() . "/wp-json/citadela-directory/map-data/points/{$post->post_type}/{$post->ID}?{$queryString}";

        /* ITEM TAXONOMY PAGES */
        } elseif (is_tax('citadela-item-category')) {

			$data['dataType'] = $dataType;
			$data['category'] = get_queried_object()->slug;

            if( isset( $attributes['advanced_filters'] ) && $attributes['advanced_filters'] ){
                $data['advanced_filters']['operators'] = \Citadela\Directory\AdvancedFilters::getFiltersOperator('item-category');
            }

        } elseif (is_tax('citadela-item-location')) {
			
            $data['dataType'] = $dataType;
			$data['location'] = get_queried_object()->slug;

            if( isset( $attributes['advanced_filters'] ) && $attributes['advanced_filters'] ){
                $data['advanced_filters']['operators'] = \Citadela\Directory\AdvancedFilters::getFiltersOperator('item-location');
            }

        /*} elseif ( is_date() ){
            $postType = self::guessPostTypeFromContext();
            $data[ 'date_query' ] = [
                [
                    'year'  => get_query_var('year'),
                    'month' => get_query_var('monthnum'),
                    'day'   => get_query_var('day'),
                ],
            ];

        } elseif ( is_tag() ){
            $postType = self::guessPostTypeFromContext();
            $data[ 'tag' ] = get_query_var('tag');

        } elseif ( is_author() ){
            $postType = self::guessPostTypeFromContext();
            $data[ 'author' ] = get_query_var('author');

        } elseif ( is_category() ){
            $postType = self::guessPostTypeFromContext();
            $term = get_queried_object('term_id');
            $data[ 'post_category' ] = $term->term_id;
        */
        } else {
            // fallback if something goes wrong (simulate search page)
			// also fix for back compatibility when search results was on wp page
			$data['s'] = '';
            $data['dataType'] = $dataType;
			$data['category'] = null;
			$data['location'] = null;
        }

        if( isset( $attributes['limitPosts'] ) && $attributes['limitPosts'] && isset( $attributes['maxPosts'] ) && intval( $attributes['maxPosts'] ) > 0 ){
            $data['posts_per_page'] = $attributes['maxPosts'];   
        }

        $queryString = build_query( $data );

		return site_url() . "/wp-json/citadela-directory/map-data/points/{$postType}?{$queryString}";
    }



    public static function guessPostTypeFromContext() {
        $context = self::guessContext();

        if (!$context) {
            return null;
        }

        if ( in_array($context, ['posts-search-results', 'single-post', 'post-category', 'post-location', 'posts-tag', 'posts-date', 'posts-category', 'posts-author' ]) ) {
            return 'post';
        }

        return 'citadela-item';
    }



    public static function guessContext() {
        if ( is_search() && isset( $_REQUEST[ 'ctdl' ] ) ) {
            if ( $_REQUEST['post_type'] == 'post' ) {
                return 'posts-search-results';
            }
            return 'items-search-results';

        } elseif ( is_singular('citadela-item') ) {
            return 'single-item';

        } elseif ( is_singular('post') ) {
            return 'single-post';

        } elseif ( is_tax('citadela-item-category') ) {
            return 'citadela-item-category';

        } elseif ( is_tax('citadela-item-location') ) {
            return 'citadela-item-location';

        } elseif ( is_tax('category') ) {
            return 'post-category';

        } elseif ( is_tax('citadela-post-location') ) {
            return 'post-location';

        } elseif ( is_date() ){
            return 'posts-date';
        
        } elseif ( is_category() ){
            return 'posts-category';
            
        } elseif ( is_tag() ){
            return 'posts-tag';
            
        } elseif ( is_author() ){
            return 'posts-author';
            
        }

        return null;
    }

    public static function validate_saved_value( $value, $type ){
        switch ( $type ) {
            case 'checkbox':
                return $value ? 1 : 0;
                break;
            
            default:
                return $value;
                break;
        }
    }
}

