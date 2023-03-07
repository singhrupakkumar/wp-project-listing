<?php

class CitadelaDirectorySearch {

    public static function init()
    {
        add_action( 'pre_get_posts', [ __CLASS__, 'pre_get_posts' ] );
        add_action( 'rest_api_init', array(__CLASS__, 'registerRestRoutes') );
        add_filter( 'posts_where', array( __CLASS__, 'includeMetaInSearch' ) );
    }

    public static function pre_get_posts( $query ) {
        if ( $query->is_main_query() && is_search() && isset( $_REQUEST[ 'ctdl' ] ) ) {
            $query->set( 'status', 'publish' );        
            self::pre_get_posts_tax_query( $query );          
        }

        return $query;
    }

    private static function pre_get_posts_tax_query( $query ) {
        $category = isset( $_REQUEST[ 'category' ] ) ? $_REQUEST[ 'category' ] : null;
        $location = isset( $_REQUEST[ 'location' ] ) ? $_REQUEST[ 'location' ] : null;
        
        $taxQuery = [];
        
        if ( $category ) {
            $category_taxonomy = $_REQUEST[ 'post_type' ] == 'post' ? 'category' : 'citadela-item-category';
            
            array_push( $taxQuery, [
                'taxonomy' => $category_taxonomy,
                'field' => 'slug',
                'terms' => $category
            ]);
                
        }
            
        if ( $location ) {
            $location_taxonomy = $_REQUEST[ 'post_type' ] == 'post' ? 'citadela-post-location' : 'citadela-item-location';
            
            array_push( $taxQuery, [
                'taxonomy' => $location_taxonomy,
                'field' => 'slug',
                'terms' => $location
            ]);
        }

        $query->set( 'tax_query', $taxQuery );
    }



    public static function getItems($args)
    {
        $s = isset($args['s']) ? $args['s'] : '';
        $category = isset($args['category']) ? $args['category'] : null;
        $location = isset($args['location']) ? $args['location'] : null;
        $posts_per_page = isset($args['posts_per_page']) ? $args['posts_per_page'] : -1;
        $offset = isset($args['offset']) ? $args['offset'] : 0;
        $paged = isset($args['paged']) ? $args['paged'] : 0;
        $only_featured = isset($args['only_featured']) ? $args['only_featured'] : false;
        $featured_first = isset($args['featured_first']) ? $args['featured_first'] : false;
        $orderby = isset($args['orderby']) ? $args['orderby'] : 'date';
        $order = isset($args['order']) ? $args['order'] : 'DESC';
        $dataType = isset($args['dataType']) ? $args['dataType'] : 'markers'; //filter posts in case only tracks are shown on map block
        $advanced_filters = isset( $args['advanced_filters'] ) && $args['advanced_filters'] ? $args['advanced_filters'] : [];
        $geolocation_search = isset( $args['geolocation'] );
        $authors = [];

        $taxQuery = [];
        $metaQuery = [];
        $post__in = [];
        $post__not_in = [];
        $orderBy = [];

        if ($only_featured) {
            $metaQuery['relation'] = 'AND';
            $metaQuery['featured'] = [
                'key'     => '_citadela_featured',
                'value'   => 1,
                'compare' => '='
            ];
        }

        if( $featured_first ){
            $metaQuery['relation'] = 'AND';
            $metaQuery['featured_first'] = [
                'relation'        => 'AND',
                'featured_clause' => [
                    'key'     => '_citadela_featured',
                    'compare' => 'EXISTS'
                ]
            ];
            $orderBy['featured_clause'] = 'DESC';
        }

        if( $dataType == 'tracks' ) {
            //get only posts which have tracks
            $metaQuery['relation'] = 'AND';
            $metaQuery['only_tracks'] = [
                'key'     => '_citadela_gpx_track',
                'compare' => 'EXISTS'
            ];   
        }

        if( isset( $args['refPost'] ) && ! empty($args['refPost'] ) ){
            $ref_post = get_post( $args['refPost'][0] );
            
            if( $ref_post ){
                
                

                if( isset( $args['similarByCategory'] ) && ! empty($args['similarByCategory'] ) ){
                    $terms = wp_get_object_terms( $ref_post->ID, 'citadela-item-category', array( 'fields' => 'ids', 'childless' => false, ) );
                    array_push($taxQuery, array(
                        'taxonomy' => 'citadela-item-category',
                        'field'    => 'term_id',
                        'terms'    => $terms,
                        'operator' => 'IN',
                    ) );
                }

                if( isset( $args['similarByLocation'] ) && ! empty($args['similarByLocation'] ) ){
                    $terms = wp_get_object_terms( $ref_post->ID, 'citadela-item-location', array( 'fields' => 'ids', 'childless' => false, ) );
                    array_push($taxQuery, array(
                        'taxonomy' => 'citadela-item-location',
                        'field'    => 'term_id',
                        'terms'    => $terms,
                        'operator' => 'IN',
                    ) );
                }

                if( isset( $args['similarByAuthor'] ) && ! empty($args['similarByAuthor'] ) ){
                    array_push( $authors, intval( $ref_post->post_author ) );
                }


                if( $orderby == 'gps' ){
                    $lat = get_post_meta($ref_post->ID, '_citadela_latitude', true );
                    $lon = get_post_meta($ref_post->ID, '_citadela_longitude', true );
                    if( ( $lat === "0" && $lon === "0" ) != true && ( $lat === "" && $lon === "" ) != true ){
                        $posts_by_radius = self::getItemsByRadius( 
                            $lat, 
                            $lon, 
                            6371, 
                            'citadela-item'
                        );
                        if( ! empty( $posts_by_radius ) ){
                            // always remove reference Item from received ids
                            if( in_array($ref_post->ID, $posts_by_radius) ){
                                $ref_post_index = array_search($ref_post->ID, $posts_by_radius);
                                unset($posts_by_radius[$ref_post_index]);
                            }
                            
                            // make sure the posts are not reordered by Featured option
                            unset($metaQuery['featured_first']);
                            
                            // always order ascending to show nearest posts by gps
                            $orderBy['post__in'] = 'ASC';
                            $post__in = $posts_by_radius;
                        }else{
                            return false;
                        }
                    }
                }

                // exclude reference post from main query
                array_push( $post__not_in, $ref_post->ID );

                // if visible reference post, decrease number of posts in main query, reference post will be in the front
                if( isset( $args['showReferencePost'] ) && $args['showReferencePost'] ){
                    $posts_per_page = $posts_per_page != -1 ? $posts_per_page - 1 : $posts_per_page;
                }
                
            }
        }

        if ($category) {
            array_push($taxQuery, array(
                'taxonomy' => 'citadela-item-category',
                'field' => is_numeric( $category ) ? 'id' : 'slug',
                'terms' => $category)
            );
        }

        if ($location) {
            array_push($taxQuery, array(
                'taxonomy' => 'citadela-item-location',
                'field' => is_numeric( $location ) ? 'id' : 'slug',
                'terms' => $location)
            );
        }

        if( $geolocation_search ) {
            if( $args['geolocation']['unit'] == 'mi' ){
                $args['geolocation']['rad'] = $args['geolocation']['rad'] * 1.609344;
            }

            $posts_by_radius = self::getItemsByRadius( $args['geolocation']['lat'], $args['geolocation']['lon'], $args['geolocation']['rad'] );
            if( ! empty( $posts_by_radius ) ){
                $post__in = $posts_by_radius;
            }else{
                return false; // we do not need to process query anymore, there are no posts that would pass geolocation parameters
            }
        }

        if( ! empty( $advanced_filters ) ){

            $filter_operators = isset($advanced_filters['operators']) ? $advanced_filters['operators'] : [];

            $main_operator = 'AND';
            
            $metaQuery['relation'] = 'AND';
            $metaQuery['advanced_filters'] = [];
            $metaQuery['advanced_filters']['relation'] = $main_operator; 
            
            // create meta query for each filters group
            foreach ($advanced_filters['filters'] as $filter_group => $data) {
                
                $group_operator = in_array( $filter_group, array_keys( $filter_operators ) ) ? $filter_operators[$filter_group] : 'OR'; //default group operator is OR

                // group of checkboxes
                if( $data['type'] == 'checkbox' ) {
                    
                    // if group operator is the same like main operator simply add query nodes, otherwise add node with another operator
                    if( $group_operator === $main_operator ){

                        foreach ( $data['keys'] as $filter_name ) {
                            $meta_key = "_citadela_item_extension_{$filter_name}";
                            $metaQuery['advanced_filters'][] = [
                                'key'     => $meta_key,
                                'value'   => '1',
                                'compare' => '=',
                            ];
                            
                        }

                    }else{
                        $query_data = [];
                        $query_data['relation'] = $group_operator;

                        foreach ( $data['keys'] as $filter_name ) {
                            $meta_key = "_citadela_item_extension_{$filter_name}";
                            $query_data[] = [
                                'key'     => $meta_key,
                                'value'   => '1',
                                'compare' => '=',
                            ];
                            
                        }
                        $metaQuery['advanced_filters'][] = $query_data;
                    }

                }
                

                // group of options in select input
                if( $data['type'] == 'select' ) {
                    
                    $meta_key = "_citadela_item_extension_{$data['input_key']}";

                    // if group operator is the same like main operator simply add query nodes, otherwise add node with another operator
                    // never use AND relation for single Select input, cannot be selected more options in simple select
                    if( "OR" === $main_operator ){ 

                        foreach ( $data['keys'] as $filter_name ) {
                            $metaQuery['advanced_filters'][] = [
                                'key'     => $meta_key,
                                'value'   => $filter_name,
                                'compare' => '=',
                            ];
                            
                        }

                    }else{
                        $query_data = [];
                        $query_data['relation'] = 'OR';
                        foreach ( $data['keys'] as $filter_name ) {
                            $query_data[] = [
                                'key'     => $meta_key,
                                'value'   => $filter_name,
                                'compare' => '=',
                            ];
                            
                        }

                        $metaQuery['advanced_filters'][] = $query_data;
                    }

                }

                // group of options in multiselect input
                if( $data['type'] == 'citadela_multiselect' ) {
                    
                    // if group operator is the same like main operator simply add query nodes, otherwise add node with another operator
                    if( $group_operator === $main_operator ){

                        foreach ( $data['keys'] as $filter_name ) {
                            $meta_key = "_citadela_item_extension_{$data['input_key']}_{$filter_name}";
                            $metaQuery['advanced_filters'][] = [
                                'key'     => $meta_key,
                                'value'   => '1',
                                'compare' => '=',
                            ];
                            
                        }

                    }else{
                        $query_data = [];
                        $query_data['relation'] = $group_operator;
                        foreach ( $data['keys'] as $filter_name ) {
                            $meta_key = "_citadela_item_extension_{$data['input_key']}_{$filter_name}";
                            $query_data[] = [
                                'key'     => $meta_key,
                                'value'   => '1',
                                'compare' => '=',
                            ];
                            
                        }

                        $metaQuery['advanced_filters'][] = $query_data;
                    }


                }
              
            }  

        }

        //set customized orderby parameter
        $orderBy[$orderby] = $order;

        $queryArgs = [
            's'                 => $s,
            'tax_query'         => $taxQuery,
            'meta_query'        => $metaQuery,
            'posts_per_page'    => $posts_per_page,
            'orderby'           => $orderBy,
            'order'             => $order,
            'paged'             => $paged,
            'post_type'         => 'citadela-item',
        ];

        if( ! empty( $post__in ) ){
            $queryArgs['post__in'] = $post__in;
        }

        if( ! empty( $post__not_in ) ){
            $queryArgs['post__not_in'] = $post__not_in;
        }

        if( ! empty( $authors ) ){
            $queryArgs['author__in'] = $authors;
        }

        return new WP_Query( $queryArgs );
    }



    public static function getBlogPosts( $args )
    {

        $s = isset($args['s']) ? $args['s'] : '';
        $category = isset($args['category']) ? $args['category'] : null;
        $location = isset($args['location']) ? $args['location'] : null;
        $posts_per_page = isset($args['posts_per_page']) ? $args['posts_per_page'] : -1;
        $paged = isset($args['paged']) ? $args['paged'] : 1;
        $orderby = isset($args['orderby']) ? $args['orderby'] : 'date';
        $order = isset($args['order']) ? $args['order'] : 'DESC';
        $offset = isset($args['offset']) ? $args['offset'] : 0;

        $taxQuery = [];

        if ( $category ) {
            array_push( $taxQuery, [
                'taxonomy' => 'category',
                'field' => is_numeric( $category ) ? 'id' : 'slug',
                'terms' => $category,
            ]);
        }

        if ( $location ) {
            array_push( $taxQuery, [
                'taxonomy' => 'citadela-post-location',
                'field' => is_numeric( $location ) ? 'id' : 'slug',
                'terms' => $location,
            ]);
        }

        $queryArgs = [
            's'                 => $s,
            'tax_query'         => $taxQuery,
            'posts_per_page'    => $posts_per_page,
            'orderby'           => $orderby,
            'order'             => $order,
            'paged'             => $paged,
            'post_type'         => 'post',
        ];

        if( $offset > 0 ){
            $current_page = intval( $paged );
            $offset = ( $current_page - 1 ) * $posts_per_page + $offset;
            $queryArgs['offset'] = $offset;
        }

        return (new WP_Query( $queryArgs ));
    }


    public static function getItemsByRadius( $lat, $lng, $radius = 6371, $post_type = 'citadela-item', $only_featured = false ){
        global $wpdb, $wp_query;

        $earth_radius = 6371;
        
        $sql = $wpdb->prepare( "
            SELECT $wpdb->posts.ID,
                ( %s * acos(
                    cos( radians(%s) ) *
                    cos( radians( _citadela_latitude.meta_value ) ) *
                    cos( radians( _citadela_longitude.meta_value ) - radians(%s) ) +
                    sin( radians(%s) ) *
                    sin( radians( _citadela_latitude.meta_value ) )
                ) )
                AS distance,
                _citadela_latitude.meta_value AS _citadela_latitude, 
                _citadela_longitude.meta_value AS _citadela_longitude 
                " . ( $only_featured ? ", _citadela_featured.meta_value AS _citadela_featured" : "" ) . "
                FROM $wpdb->posts
                INNER JOIN $wpdb->postmeta
                    AS _citadela_latitude
                    ON $wpdb->posts.ID = _citadela_latitude.post_id
                INNER JOIN $wpdb->postmeta
                    AS _citadela_longitude
                    ON $wpdb->posts.ID = _citadela_longitude.post_id
                " . ( $only_featured ? "INNER JOIN $wpdb->postmeta
                    AS _citadela_featured
                    ON $wpdb->posts.ID = _citadela_featured.post_id" : "" ) . "
                WHERE 1=1
                    AND ($wpdb->posts.post_type = %s )
                    AND ($wpdb->posts.post_status = 'publish' )
                    AND _citadela_latitude.meta_key='_citadela_latitude'
                    AND _citadela_longitude.meta_key='_citadela_longitude'
                    ". ( $only_featured ? "AND _citadela_featured.meta_key='_citadela_featured'" : "" ) . "
                HAVING distance < %s ". ( $only_featured ? "AND _citadela_featured='1'" : "" ) . "
                ORDER BY distance ASC",
            $earth_radius,
            $lat,
            $lng,
            $lat,
            $post_type,
            $radius
        );

        $posts = $wpdb->get_results( $sql, OBJECT_K );
        $posts_ids = array_keys($posts);
        return $posts_ids ;
    }


    public static function getTaxonomiesHierarchy()
    {
        $categories = [];
        $locations = [];

        foreach (['citadela-item-category', 'citadela-item-location'] as $taxonomy) {
            $terms = get_categories(array('taxonomy' => $taxonomy, 'hide_empty' => true, 'parent' => 0));

            foreach ($terms as $category) {
                $children = get_terms( $taxonomy, ['parent' => $category->term_id]);
                $result = [
                    'parent' => $category,
                    'children' => $children,
                ];
                if ($taxonomy == 'citadela-item-category') {
                    $categories[] = $result;
                } else {
                    $locations[] = $result;
                }
            }
        }

        return [$categories, $locations];
    }



    public static function registerRestRoutes()
    {
        register_rest_route( 'citadela-directory', '/map-data/points/citadela-item(?:/(?P<id>\d+))?', [
            'methods' => 'GET',
            'callback' =>  array(__CLASS__, 'restGetItemPoints'),
            'permission_callback' => "__return_true",
            'args' => ['id' => [
                    'validate_callback' => function($param, $request, $key) {
                    return is_numeric( $param );
                    }
                ],
            ]
        ] );

        register_rest_route( 'citadela-directory', '/map-data/points/post(?:/(?P<id>\d+))?', [
            'methods' => 'GET',
            'callback' =>  array(__CLASS__, 'restGetPostPoints'),
            'permission_callback' => "__return_true",
        ] );

        register_rest_route( 'citadela-directory', '/terms', [
            'methods' => 'GET',
            'callback' =>  array(__CLASS__, 'restGetTerms'),
            'permission_callback' => "__return_true",
        ] );

        register_rest_route( 'citadela-directory', '/posts', [
            'methods' => 'GET',
            'callback' =>  array(__CLASS__, 'restGetPosts'),
            'permission_callback' => "__return_true",
        ] );

        register_rest_route( 'citadela-directory', '/users', [
            'methods' => 'GET',
            'callback' =>  array(__CLASS__, 'restGetUsers'),
            'permission_callback' => "__return_true",
        ] );
        
    }



    public static function restGetItemPoints( WP_REST_Request $request )
    {
        $total = 0;
        $points = [];

        $args = $request->get_params();
        
        $perRequest = isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : -1;
        
        if (isset($request['id'])) {
            $post = get_post($request['id']);
            if( 
                isset( $args['dataType'] ) && 
                $args['dataType'] == 'tracks' && 
                ! get_post_meta($post->ID, '_citadela_gpx_track', true) )
            {
                //do not return point data if only tracks have to be displayed and post doesn't have a track
                $total = 0;
            }else{
                $points[] = CitadelaDirectoryFunctions::prepareMapPoint($post);
                $total = 1;
            }
        } else {

            $args['posts_per_page'] = $perRequest;

            $points = [];
            $query = CitadelaDirectorySearch::getItems($args);
            // $query may be false if no posts were found by geolocation, prevent problem to show all posts if no one meet geolocation search radius
            if( $query ){
                $total = $query->found_posts;
                foreach ($query->posts as $post) {
                    $points[] = CitadelaDirectoryFunctions::prepareMapPoint($post);
                }
            }
        }

        return ['total' => $total, 'points' => $points];
    }



    public static function restGetPostPoints( WP_REST_Request $request )
    {
        $total = 0;
        $points = [];

        // filter posts without gps or with gps set to [0, 0]
        $metaQuery = [
            'relation' => 'OR',
            [
                'key' => '_citadela_longitude',
                'value' => 0,
                'compare' => '>'
            ],
            [
                'key' => '_citadela_latitude',
                'value' => 0,
                'compare' => '>'
            ],
        ];

        if (isset($request['id'])) {
            $args = [
                'post__in' => [$request['id']],
                'meta_query' => $metaQuery
            ];

            $query = new WP_Query( $args );

            if ($query->have_posts()) {
                $points[] = CitadelaDirectoryFunctions::prepareMapPoint($query->posts[0]);
                $total = 1;
            }
            return ['total' => $total, 'points' => $points];

        }

        // if query is empty we assume that single post does not have gps coordinates -> continue and display all posts instead

        $args = $request->get_params();

        $perRequest = isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : -1;
        
        $taxQuery = [];

        if (isset($args['location']) && $args['location']) {
            array_push($taxQuery, array(
                'taxonomy' => 'citadela-post-location',
                'field' => is_numeric( $args['location'] ) ? 'id' : 'slug',
                'terms' => $args['location']
                )
            );
        }

        if (isset($args['category']) && $args['category']) {
            array_push($taxQuery, array(
                'taxonomy' => 'category',
                'field' => is_numeric( $args['category'] ) ? 'id' : 'slug',
                'terms' => $args['category'])
            );
        }

        $s = isset($args['s']) ? $args['s'] : '';
        $offset = isset($args['offset']) ? $args['offset'] : 0;

        $dateQuery = isset( $args['date_query'] ) ? $args['date_query'] : [];
        $author = isset( $args['author'] ) ? $args['author'] : null;
        $tag = isset( $args['tag'] ) ? $args['tag'] : null;
        $post_category_id = isset( $args['post_category'] ) ? $args['post_category'] : null;

        if( $post_category_id ){
            array_push($taxQuery, array(
                    'taxonomy' => 'category',
                    'field' => 'id',
                    'terms' => $post_category_id 
                )
            );
        }


        $args = [
            's' => $s,
            'offset' => $offset,
            'post_type' => 'post',
            'tax_query' => $taxQuery,
            'meta_query' => $metaQuery,
        ];

        /*
        if( $dateQuery ) {
            $args['date_query'] = $dateQuery;
        }

        if( $author ) {
            $args['author'] = $author;
        }

        if( $tag ) {
            $args['tag'] = $tag;
        }
        */
        
        $args['posts_per_page'] = $perRequest;


        $query = new WP_Query( $args );

        $points = [];
        $total = $query->found_posts;

        foreach ($query->posts as $post) {
            $points[] = CitadelaDirectoryFunctions::prepareMapPoint($post);
        }

        return ['total' => $total, 'points' => $points];
    }



    public static function restGetTerms(WP_REST_Request $request)
    {
        $taxonomy = $request['taxonomy'];

        $foundTerms = get_terms($taxonomy, [
            'number' => 0,
            'hide_empty' => true,
        ]);

        if (!$foundTerms) {
            return [];
        }

        return $foundTerms;
    }


    public static function restGetPosts(WP_REST_Request $request)
    {
        $post_type = $request['post_type'];
        $limit = isset( $request['limit'] ) ? $request['limit'] : 100;
        $search = isset( $request['search'] ) ? $request['search'] : '' ;
        $exclude_posts = isset( $request['exclude_posts'] ) ? explode( ',', $request['exclude_posts'] ) : [] ;
        $selected_posts = isset( $request['selected_posts'] ) ? explode( ',', $request['selected_posts'] ) : [] ;

        $post_id = isset( $request['post_id'] ) ? $request['post_id'] : '' ;

        if( $post_id ){
            $post = get_post($post_id);
            return $post ? $post : [];
        }

        if( ! empty( $selected_posts ) ){
            // get only selected posts
            $selected_posts_query = new WP_Query( [
                'post_type' => $post_type,
                'posts_per_page' => -1,
                'post__in' => $selected_posts,
            ] );

            return $selected_posts_query->posts ? $selected_posts_query->posts : [];

        }else{
            if( $search ){
                
                global $wpdb;
                $posts = $wpdb->get_results( $wpdb->prepare( 
                    "SELECT * FROM $wpdb->posts WHERE post_type LIKE '%s' AND post_title LIKE '%s'", 
                    '%'.$wpdb->esc_like( $post_type ).'%',
                    '%'.$wpdb->esc_like( $search ).'%'
                ) );

            }else{
                $args = [
                    'post_type' => $post_type,
                    'posts_per_page' => $limit,
                    'post__not_in' => $exclude_posts, // do not retrieve selected posts, separate query will get these posts
                ];

                $query = new WP_Query( $args );
                $posts = $query->posts;

                $selected_posts = [];
                if( $exclude_posts ) {
                    // selected posts, load only posts excluded from main query
                    $selected_posts_query = new WP_Query( [
                        'post_type' => $post_type,
                        'posts_per_page' => -1,
                        'post__in' => $exclude_posts,
                    ] );

                    $selected_posts = $selected_posts_query->posts ? $selected_posts_query->posts : [];
                }

                $posts = array_merge( $selected_posts, $posts );
            }
        }

        return $posts ? $posts : [];
    }

    public static function restGetUsers(WP_REST_Request $request)
    {

        $limit = isset( $request['limit'] ) ? $request['limit'] : 100;
        $search = isset( $request['search'] ) ? $request['search'] : '' ;
        $exclude_posts = isset( $request['exclude_posts'] ) ? explode( ',', $request['exclude_posts'] ) : [] ;
        $selected_users = isset( $request['selected_posts'] ) ? explode( ',', $request['selected_posts'] ) : [] ;
        $fields = isset( $request['fields'] ) ? $request['fields'] : 'all' ;

        $post_id = isset( $request['post_id'] ) ? $request['post_id'] : '' ;

        if( $post_id ){
            $post = get_user_by('id', $post_id);
            return $post ? $post : [];
        }

        if( ! empty( $selected_users ) ){
            // get only selected posts
            $selected_users_query = new WP_User_Query( [
                //'number' => -1,
                'include' => $selected_users,
                'fields' => $fields,
            ] );

            return $selected_users_query->get_results();

        }else{
            if( $search ){
                
                global $wpdb;
                $users = $wpdb->get_results( $wpdb->prepare( 
                    "SELECT * FROM $wpdb->users WHERE 
                    display_name LIKE '%s' 
                    OR user_login LIKE '%s' 
                    OR user_nicename LIKE '%s' 
                    OR user_email LIKE '%s'",
                    '%'.$wpdb->esc_like( $search ).'%',
                    '%'.$wpdb->esc_like( $search ).'%',
                    '%'.$wpdb->esc_like( $search ).'%',
                    '%'.$wpdb->esc_like( $search ).'%'
                ) );
                $users = $users ? $users : [];
            }else{
                $args = [
                    'number' => $limit,
                    'exclude' => $exclude_posts, // do not retrieve selected posts, separate query will get these posts
                ];

                $query = new WP_User_Query( $args );
                $users = $query->get_results();
                
                $selected_users = [];
                if( $exclude_posts ) {
                    // selected posts, load only posts excluded from main query
                    $selected_users_query = new WP_User_Query( [
                        //'number' => -1,
                        'include' => $exclude_posts,
                    ] );
                    $selected_users = $selected_users_query->get_results();
                }

                $users = array_merge( $selected_users, $users );
            }
        }
        
        return $users ? $users : [];
    }

    /* because item cpt is registered with exclude_from_search=>true we must enable item cpt on taxonomy pages */
    public static function preGetPosts($query)
    {
        if ($query->is_tax('citadela-item-category') || $query->is_tax('citadela-item-location')) {
            $query->set('post_type', 'citadela-item');
        }

        return $query;
    }


    public static function includeMetaInSearch( $where ) {
        //apply filter only for citadela items search
        $apply = false;
        // results on search results page
        if( is_search() && isset( $_REQUEST[ 'ctdl' ] ) && isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == 'citadela-item' ) {
            $apply = true;
        }

        // results on map loaded via rest api
        if( isset( $_REQUEST[ 'citadela_search' ] ) && $_REQUEST[ 'citadela_search' ] == 'citadela-item' ) {
            $apply = true;
        }

        $s = isset($_GET['s']) ? $_GET['s'] : '';
        
        if( ! $apply || $s == '' ) return $where;

        // list of meta keys where we are searching
        $metaKeys = [
            '_citadela_subtitle',
            '_citadela_address',
        ];
        $metaKeys = "'" . implode( "', '", esc_sql( $metaKeys ) ) . "'";

        global $wpdb;

        $toFind = "(".$wpdb->posts . ".post_title";

        $likeClause = $wpdb->esc_like( $s );
        $likeClause = '%' . $likeClause . '%';

        // search in postmeta values and return array of post ids
        $subQuery = $wpdb->prepare(
            "SELECT group_concat(post_id) as ids FROM {$wpdb->postmeta} AS postmeta
                WHERE postmeta.meta_value LIKE %s
                AND postmeta.meta_key IN ({$metaKeys})",
            $likeClause
        );
        $subQuery = "(FIND_IN_SET(".$wpdb->posts.".ID, (".$subQuery."))) OR ";

        $subqueryLength = strlen($subQuery);

        $positions = self::findStrPos($toFind, $where);

        $newWhere = $where;
        for ($i = 0; $i < sizeof($positions); $i++) {
            // insert subquery on each position where $toFind occured
            // consider that each next position is moved by the length of previously inserted subquery
            $newWhere = substr_replace($newWhere, $subQuery, $positions[$i] + $i * $subqueryLength, 0);
        }

        // Return revised WHERE clause
        return $newWhere;
    }

    public static function findStrPos( $needle, $haystack ) {
        $fnd = array();
        $pos = 0;

        while ($pos <= strlen($haystack)) {
            $pos = strpos($haystack, $needle, $pos);
            if ($pos > -1) {
                $fnd[] = $pos++;
                continue;
            }
            break;
        }
        return $fnd;
    }
}