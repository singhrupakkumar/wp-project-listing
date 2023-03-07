<?php

class CitadelaDirectorySpecialPages {
    public static function init() {
        global $wp_version;
        add_action( 'init', [ __CLASS__, 'onInit' ] );
        add_action( 'admin_init', [ __CLASS__, 'admin_init' ] );
        add_action( 'admin_menu', [ __CLASS__, 'menu' ] );
        add_action( 'ctdl_page_title', [ __CLASS__, 'page_title' ] );
        
        // WP 5.8 and early compatibility, refer to https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#block_categories_all
        if( class_exists("WP_Block_Editor_Context") ){
            add_filter( 'block_categories_all', [ __CLASS__, 'block_categories' ] );
        }else{
            add_filter( 'block_categories', [ __CLASS__, 'block_categories' ] );
        }

        add_filter( 'ctdl_block_page_title', [ __CLASS__, 'custom_page_title'], 10, 3 ); // html of custom page title: citadela-blocks/page-title, $attr: block attributes, $title_styles: styles for h1 title

        add_filter( 'views_edit-special_page', [ __CLASS__, 'remove_special_pages_post_status_filters' ] );
        self::disable_template_editor();
    }

    static function config() {
        return [

            'single-item' => [
                'option_key' => 'citadela_single_item_page',
                'title' => esc_html__( "Item Detail Page", 'citadela-directory' )
            ],
            'search-results' => [
                'option_key' => 'citadela_search_items_page',
                'title' => esc_html__( 'Listing Search Results', 'citadela-directory' )
            ],
            'item-category' => [
                'option_key' => 'citadela_item_category_page',
                'title' => esc_html__( 'Listing Category Page', 'citadela-directory' )
            ],
            'item-location' => [
                'option_key' => 'citadela_item_location_page',
                'title' => esc_html__( 'Listing Location Page', 'citadela-directory' )
            ],
            'posts-search-results' => array(
                'option_key' => 'citadela_posts_search_results_page',
                'title' => esc_html__( 'Posts Search Results', 'citadela-directory' )
            ),
            'posts-category' => array(
                'option_key' => 'citadela_posts_category_page',
                'title' => esc_html__( 'Posts Category Page', 'citadela-directory' )
            ),
            'posts-tag' => array(
                'option_key' => 'citadela_posts_tag_page',
                'title' => esc_html__( 'Posts Tag Page', 'citadela-directory' )
            ),
            'posts-date' => array(
                'option_key' => 'citadela_posts_date_page',
                'title' => esc_html__( 'Posts Date Page', 'citadela-directory' )
            ),
            'posts-author' => array(
                'option_key' => 'citadela_posts_author_page',
                'title' => esc_html__( 'Posts Author Page', 'citadela-directory' )
            ),
            'default-search-results' => array(
                'option_key' => 'citadela_default_search_results_page',
                'title' => esc_html__( 'Default Search Results Page', 'citadela-directory' )
            ),
            '404-page' => array(
                'option_key' => 'citadela_404_page',
                'title' => esc_html__( '404 Page', 'citadela-directory' )
            ),

		];
    }
    public static function admin_init() {

        if( ! is_plugin_active( 'citadela-pro/citadela-pro.php' ) ){
            add_filter( 'pre_get_posts', [ __CLASS__, 'show_only_directory_plugin_special_pages' ] );
        }
    }

    public static function disable_template_editor(){
        global $pagenow;
        if( $pagenow == 'post.php' && isset( $_GET['post'] ) && $_GET['post'] != '' ){
            $post = get_post( $_GET['post'] );
            if( $post && $post->post_type == 'special_page' ){
                remove_theme_support('block-templates');
            }
        }
    }

    public static function onInit() {
        self::register_post_type();
        self::add_capabilities();

        self::maybe_migrate( 'pages_to_cpt' );

        self::customize_title_on_edit();

        //do not initialize special pages during import process
        $import_progress = get_option( "citadela_layout_import_progress" );
        if( $import_progress !== "wip" ) {
            self::prepare();
        }
    }

    static function customize_title_on_edit(){
        global $pagenow;
        if( ! ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'special_page' ) ) return;

        $p = get_post_type_object( 'special_page' );
        if ( ! $p ) return;

        $p->labels->name = esc_html__( 'Citadela - Special Pages', 'citadela-directory' );

    }
    
    public static function get_ids() {
        $pages_ids = [];
        foreach (self::config() as $slug => $settings) {
            array_push( $pages_ids, get_option( $settings['option_key'] ) );
        }
        return $pages_ids;
    }

    public static function register_post_type() {
        if ( post_type_exists( 'special_page' ) ) { return; }

        register_post_type( 'special_page', [
            'labels'                => [
                'name'                     => esc_html_x( 'Special Pages', 'post type general name', 'citadela-directory' ),
                'singular_name'            => esc_html_x( 'Special Page', 'post type singular name', 'citadela-directory' ),
                'menu_name'                => esc_html_x( 'Special Pages', 'admin menu', 'citadela-directory' ),
                'edit_item'                => esc_html__( 'Edit Special Page', 'citadela-directory' ),
                'all_items'                => esc_html__( 'All Special Pages', 'citadela-directory' ),
                'search_items'             => esc_html__( 'Search Special ages', 'citadela-directory' ),
                'not_found'                => esc_html__( 'No special pages found.', 'citadela-directory' ),
                'filter_items_list'        => esc_html__( 'Filter special pages list', 'citadela-directory' ),
                'items_list_navigation'    => esc_html__( 'Special pages list navigation', 'citadela-directory' ),
                'items_list'               => esc_html__( 'Special pages list', 'citadela-directory' ),
                'item_updated'             => esc_html__( 'Special page updated.', 'citadela-directory' ),
            ],
            'public'                => true,
            '_builtin'              => true, /* internal use only. don't use this when registering your own post type. */
            'show_ui'               => true,
            'show_in_menu'          => false,
            'rewrite'               => false,
            'show_in_rest'          => true,
            'exclude_from_search'   => true,
            'rest_base'             => 'special_pages',

            'capabilities' => [
                'read_private_posts' => 'read_private_special_pages',
                'read_post' => 'read_special_page',
                'publish_posts' => 'publish_special_pages',
                'edit_post' => 'edit_special_page',
                'edit_posts' => 'edit_special_pages',

                // roles cannot have these capabilities
                'create_posts' => 'create_special_pages',
                'delete_posts' => 'delete_special_pages',
            ],

            'supports' => [
                'title',
                'editor',
                'custom-fields',
            ],
        ] );
    }

    public static function add_capabilities() {
        $role = get_role( 'administrator' );

        foreach ( [
            'read_private_special_pages',
            'read_special_page',
            'publish_special_pages',
            'edit_special_page',
            'edit_special_pages',
        ] as $cap ) {
            $role->add_cap( $cap );
        }
    }

    public static function menu() {
        global $menu;

        foreach ( $menu as $key => $item ) {
            if ( $item[2] == 'edit.php?post_type=special_page' ) {
                return; // menu is already registered from another plugin or theme
            }
        }

        $menu_item = [
			'page_title'	=> esc_html__( 'Citadela Special Pages', 'citadela-directory' ),
			'menu_title'	=> esc_html__( 'Citadela Special Pages', 'citadela-directory' ),
			'capability'	=> 'edit_dashboard', //needs to be changed for future
			'menu_slug'		=> 'edit.php?post_type=special_page',
			'function'		=> '',
			'icon_url'		=> 'data:image/svg+xml;base64,' . base64_encode(CitadelaDirectorySettings::getCitadelaSvgLogo( 'citadela-logo-special-pages.svg' )),
			'position'		=> 25 //after Comments menu
        ];

        call_user_func_array( 'add_menu_page', array_values( $menu_item ) );
    }

    public static function page_title() {
        // Standard page title

        $hide_title = false;
        $page_title = "";
        $header_class = [];
        
        if ( self::is_search_results_page() ) {
            
            if ( $_REQUEST[ 'post_type' ] == 'post' ) {
                $page_id = CitadelaDirectoryLayouts::getSpecialPageId( 'posts-search-results' );
            } else {
                $page_id = CitadelaDirectoryLayouts::getSpecialPageId( 'search-results' );
            }

            $hide_title = get_post_meta( $page_id, '_citadela_hide_page_title', true );
            $page_title = self::search_results_page_title();

        } elseif ( self::is_default_search_results_page() ){

            $page_id = CitadelaDirectoryLayouts::getSpecialPageId( 'default-search-results' );
            $hide_title = get_post_meta( $page_id, '_citadela_hide_page_title', true );
            $page_title = self::default_search_results_page_title();            

        } elseif ( is_category() ){

            $page_id = CitadelaDirectoryLayouts::getSpecialPageId( 'posts-category' );
            $hide_title = get_post_meta( $page_id, '_citadela_hide_page_title', true );
            $page_title = self::posts_category_page_title();
            $qo = get_queried_object();
            if( $qo && $qo->description ) { $header_class[] = 'has-subtitle'; }

        } elseif ( is_tag() ){

            $page_id = CitadelaDirectoryLayouts::getSpecialPageId( 'posts-tag' );
            $hide_title = get_post_meta( $page_id, '_citadela_hide_page_title', true );
            $page_title = self::posts_tag_page_title();
            $qo = get_queried_object();
            if( $qo && $qo->description ) { $header_class[] = 'has-subtitle'; }

        } elseif ( is_date() ){
            
            $page_id = CitadelaDirectoryLayouts::getSpecialPageId( 'posts-date' );
            $hide_title = get_post_meta( $page_id, '_citadela_hide_page_title', true );
            $page_title = self::posts_date_page_title();

        } elseif ( is_author() ){

            $page_id = CitadelaDirectoryLayouts::getSpecialPageId( 'posts-author' );
            $hide_title = get_post_meta( $page_id, '_citadela_hide_page_title', true );
            $page_title = self::posts_author_page_title();
            $qo = get_queried_object();

            if( $qo && $qo->description ) { $header_class[] = 'has-subtitle'; }
        } elseif ( is_404() ){

            $page_id = CitadelaDirectoryLayouts::getSpecialPageId( '404-page' );
            $hide_title = get_post_meta( $page_id, '_citadela_hide_page_title', true );
            $page_title = self::nothing_found_page_title();
            $qo = get_queried_object();

            if( $qo && $qo->description ) { $header_class[] = 'has-subtitle'; }

        } else {

            return;
            
        }

        if ( $hide_title ) {
            return;
        }

        ob_start();
        ?>

        <div class="page-title standard">
            <header class="entry-header <?php echo implode( ' ', $header_class ); ?>">
                <div class="entry-header-wrap">
                    
                    <?php echo $page_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

                </div>
            </header>
        </div>

        <?php
        echo apply_filters( 'ctdl_page_title_html', ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public static function custom_page_title( $html, $test, $title_styles ) {       
        // Custom page title in block
        if ( self::is_search_results_page() ) {
            return self::search_results_page_title( $title_styles );
        }
        if ( self::is_default_search_results_page() ) {
            return self::default_search_results_page_title( $title_styles );
        }

        return $html;
    }



    public static function posts_category_page_title() {
        
        $page_title = single_cat_title('', false);
        $description = get_the_archive_description();
        
        ob_start();
        ?>
        <h1 class="entry-title">
            <span class="main-text"><?php esc_html_e('Category archives: ', 'citadela-directory'); ?></span>
            <span class="main-data"><?php echo $page_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        </h1>
        <?php if( $description ) : ?>
            <div class="entry-subtitle">
                <p class="ctdl-subtitle"><?php echo wp_kses_post( $description ); ?></p>
            </div>
        <?php endif; ?>

        <?php
        return ob_get_clean();
    }

    public static function posts_tag_page_title() {
        
        $page_title = single_tag_title('', false);
        $description = get_the_archive_description();
        
        ob_start();
        ?>
        <h1 class="entry-title">
            <span class="main-text"><?php esc_html_e('Tag archives: ', 'citadela-directory'); ?></span>
            <span class="main-data"><?php echo $page_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        </h1>
        <?php if( $description ) : ?>
            <div class="entry-subtitle">
                <p class="ctdl-subtitle"><?php echo wp_kses_post( $description ); ?></p>
            </div>
        <?php endif; ?>

        <?php
        return ob_get_clean();
    }
    
    public static function posts_date_page_title() {

        $page_title = get_the_date();
               
        ob_start();
        ?>
        <h1 class="entry-title">
            <span class="main-text"><?php esc_html_e('Date archives: ', 'citadela-directory'); ?></span>
            <span class="main-data"><?php echo $page_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        </h1>
        <?php
        return ob_get_clean();
    }

    public static function posts_author_page_title() {
        
        $authorUrl = get_author_posts_url( get_the_author_meta( 'ID' ) );
        $authorName = esc_html( get_the_author() );
        $page_title = single_tag_title('', false);
        $description = get_the_archive_description();
        
        ob_start();
        ?>
        <h1 class="entry-title">
            <span class="main-text"><?php esc_html_e('Author archives: ', 'citadela-directory'); ?></span>
            <span class="author vcard main-data"><a class="url fn n" href="<?php echo esc_url( $authorUrl ); ?>"><?php echo $authorName; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></span>
        </h1>
        <?php if( $description ) : ?>
            <div class="entry-subtitle">
                <p class="ctdl-subtitle"><?php echo wp_kses_post( $description ); ?></p>
            </div>
        <?php endif; ?>

        <?php
        return ob_get_clean();
    }

    public static function nothing_found_page_title() {
        $page_title = get_the_date();
               
        ob_start();
        ?>
        <h1 class="entry-title"><?php esc_html_e('Oops! That page can&rsquo;t be found.', 'citadela-directory'); ?></h1>
        <?php
        return ob_get_clean();
    }

    public static function default_search_results_page_title( $title_styles = "" ) {

        $search_keyword = !empty( $_REQUEST[ 's' ] ) ? $_REQUEST['s'] : esc_html__( 'Everything', 'citadela-directory' );

        ob_start();
        ?>

        <h1 class="entry-title" <?php if( $title_styles ){ echo "style=\"{$title_styles}\""; } ?>>
            <span class="main-text">
                <?php esc_html_e('Search results for: ', 'citadela-directory'); ?>
            </span>

            <span class="main-data">
                <?php esc_html_e( $search_keyword ); ?>
            </span>
        </h1>

        <?php
        return ob_get_clean();
    }

    public static function search_results_page_title( $title_styles = "" ) {
        $search_keyword = $_REQUEST['s'];
        $search_params = [];

        if ( !empty( $_REQUEST[ 's' ] ) ) {
            $search_params[] = $_REQUEST[ 's' ];
        }

        if ( !empty( $_REQUEST[ 'category' ] ) ) {
            $term = get_term_by( 'slug', $_REQUEST[ 'category' ], $_REQUEST[ 'post_type' ] == 'post' ? 'category' : 'citadela-item-category' );
            if ( $term ) {
                $search_params[] = $term->name;
            }
        }
        
        if ( !empty( $_REQUEST[ 'location' ] ) ) {
            $term = get_term_by( 'slug', $_REQUEST[ 'location' ], $_REQUEST[ 'post_type' ] == 'post' ? 'citadela-post-location' : 'citadela-item-location' );
            if ( $term ) {
                $search_params[] = $term->name;
            }
        }
        
        $search_params = $search_params ? : [ esc_html__( 'Everything', 'citadela-directory' ) ];
        
        ob_start();
        ?>

        <h1 class="entry-title" <?php if( $title_styles ){ echo "style=\"{$title_styles}\""; } ?>>
            <span class="main-text">
                <?php esc_html_e('Search results for: ', 'citadela-directory'); ?>
            </span>

            <span class="main-data">
                <?php echo esc_html( stripslashes ( implode( ', ', $search_params ) ) ); ?>
            </span>
        </h1>

        <?php
        return ob_get_clean();
    }

    public static function block_categories( $categories ) {
        $new_categories = [
            'citadela-directory-blocks' => [
                'slug' => 'citadela-directory-blocks',
                'title' => esc_html__( 'Citadela Listing Blocks', 'citadela-directory' ),
            ],

            'citadela-posts-blocks' => [
                'slug' => 'citadela-posts-blocks',
                'title' => esc_html__( 'Citadela Posts Blocks', 'citadela-directory' ),
            ],
        ];

        foreach ( $categories as $cat ) {
			if ( $cat[ 'slug' ] === 'citadela-directory-blocks' ) {
                unset( $new_categories[ 'citadela-directory-blocks' ] );
            }

            if ( $cat[ 'slug' ] === 'citadela-posts-blocks' ) {
                unset( $new_categories[ 'citadela-posts-blocks' ] );
            }
        }

        return array_merge( array_values( $new_categories ),
            $categories
        );
    }

    public static function is_search_results_page() {
        if ( is_search() && isset( $_REQUEST[ 'ctdl' ] ) ) {
            return true;
        }

        return false;
    }

    public static function is_default_search_results_page() {
        if ( is_search() && ! isset( $_REQUEST[ 'ctdl' ] ) ) {
            return true;
        }

        return false;
    }

    public static function prepare() {
        $missing = [];

        $config = self::config();

        foreach ( $config as $slug => $settings ) {
            $id = get_option( $config[ $slug ][ 'option_key' ] );
            $post_type = get_post_type( intval( $id ) );
            if ( $post_type !== "special_page" ) {
                $missing[] = $slug;
            }
        }

        if ( $missing ) {
            $defaultContent = json_decode( file_get_contents( CitadelaDirectoryPaths::getPaths()->dir->includes . '/default-content.json' ) );

            foreach ( $missing as $slug ) {
                $args = [
                    'post_type' => 'special_page',
                    'post_status' => 'publish',
                    'post_title' => $config[ $slug ][ 'title' ],
                    'post_content' => $defaultContent->$slug->content,
                ];

                $id = wp_insert_post( $args );
                update_option( $config[ $slug ][ 'option_key' ], $id );
            }
        }

    }

    public static function maybe_migrate( $migration_key ) {
        if ( !get_option("ctdl_directory_migration_{$migration_key}") ) {
            self::migrate_pages_to_cpt();
            update_option( "ctdl_directory_migration_{$migration_key}", "1" );
        }
    }

    protected static function migrate_pages_to_cpt() {
        $config = self::config();

        foreach ( $config as $slug => $settings ) {
            $option_key = "" . $config[ $slug ][ 'option_key' ];
            $backup_option_key = "" . $config[ $slug ][ 'option_key' ] . "_old";
            $current_special_page_id = get_option( $option_key );

            $page_already_migrated = get_option( $backup_option_key );
            if ( $current_special_page_id && !$page_already_migrated ) {

                // create new special_page post type
                $args = [
                    'post_type' => 'special_page',
                    'post_status' => 'publish',
                    'post_title' => $config[ $slug ][ 'title' ],
                    'post_content' => get_post( $current_special_page_id )->post_content,
                ];

                $new_special_page_id = wp_insert_post( $args );

                add_option( $backup_option_key, $current_special_page_id );
                update_option( $option_key, $new_special_page_id );

                $val = get_post_meta( $current_special_page_id, '_citadela_hide_page_title', true );
                update_post_meta( $new_special_page_id, '_citadela_hide_page_title', $val );

                $val = get_post_meta( $current_special_page_id, '_citadela_remove_header_space', true );
                update_post_meta( $new_special_page_id, '_citadela_remove_header_space', $val );

                wp_trash_post( $current_special_page_id );
            }
        }
    }

    public static function remove_special_pages_post_status_filters( $views ) {

        unset( $views['all'] );
        unset( $views['publish'] );
        unset( $views['draft'] );
        unset( $views['trash'] );
        unset( $views['pending'] );

        return $views;

    }


    public static function show_only_directory_plugin_special_pages( $query ) {

        global $pagenow;

        if( ! ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'special_page' ) )
            return $query;

        $special_pages = self::get_ids();
        
        $query->set( 'post__in', $special_pages );

        return $query;
    }

}
