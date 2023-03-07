<?php

namespace Citadela\Pro\Special_Pages;

class Feature {

	function __construct() {

        add_action( 'admin_menu', [ $this, 'menu' ] );
        add_action( 'admin_init', [ $this, 'admin_init' ] );

        add_filter( 'template_include', [ $this, 'include_templates'] );
        add_action( 'ctdl_pro_special_page_content', [ $this, 'special_page_content' ] );
        add_action( 'ctdl_page_title', [ $this, 'page_title' ] );

        add_filter( 'ctdl_block_page_title', [ $this, 'custom_page_title'], 10, 3 ); // inject html of custom page title: citadela-blocks/page-title
        add_filter( 'body_class', [ $this, 'body_class' ], 11 );

        add_action( 'wp_head', array( $this, 'wp_head' ) );
        add_action( 'admin_head', array( $this, 'admin_head' ) );

        add_filter( 'views_edit-special_page', [ $this, 'remove_special_pages_post_status_filters' ] );
        
        $this->register_post_type();
        $this->add_capabilities();

        self::customize_title_on_edit();
        
        //do not initialize special pages during import process
        $import_progress = get_option( "citadela_layout_import_progress" );
        if( $import_progress !== "wip" ) {
            Page::prepare();
        }
        
    }

    function admin_init() {
        
        if( ! is_plugin_active( 'citadela-directory/citadela-directory.php' ) ){
            add_filter( 'pre_get_posts', [ $this, 'show_only_pro_plugin_special_pages' ] );
        }

    }

    function customize_title_on_edit(){
        global $pagenow;
        if( ! ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'special_page' ) ) return;

        $p = get_post_type_object( 'special_page' );
        if ( ! $p ) return;

        $p->labels->name = __( 'Citadela - Special Pages', 'citadela-pro' );

    }

    function register_post_type() {
        if ( post_type_exists( 'special_page' ) ) { return; }

        register_post_type( 'special_page', [
            'labels' => [
                'name'                  => _x( 'Special Pages', 'post type general name', 'citadela-pro' ),
                'singular_name'         => _x( 'Special Page', 'post type singular name', 'citadela-pro' ),
                'menu_name'             => _x( 'Special Pages', 'admin menu', 'citadela-pro' ),
                'edit_item'             => __( 'Edit Special Page', 'citadela-pro' ),
                'all_items'             => __( 'All Special Pages', 'citadela-pro' ),
                'search_items'          => __( 'Search Special ages', 'citadela-pro' ),
                'not_found'             => __( 'No special pages found.', 'citadela-pro' ),
                'filter_items_list'     => __( 'Filter special pages list', 'citadela-pro' ),
                'items_list_navigation' => __( 'Special pages list navigation', 'citadela-pro' ),
                'items_list'            => __( 'Special pages list', 'citadela-pro' ),
                'item_updated'          => __( 'Special page updated.', 'citadela-pro' ),
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
                'read_post'          => 'read_special_page',
                'publish_posts'      => 'publish_special_pages',
                'edit_post'          => 'edit_special_page',
                'edit_posts'         => 'edit_special_pages',

                // roles cannot have these capabilities
                'create_posts'       => 'create_special_pages',
                'delete_posts'       => 'delete_special_pages',
            ],

            'supports' => [
                'title',
                'editor',
                'custom-fields',
            ],
        ] );
    }



    function add_capabilities() {
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



    function menu() {
        global $menu;

        foreach ( $menu as $key => $item ) {
            if ( $item[2] == 'edit.php?post_type=special_page' ) {
                return; // menu is already registered from another plugin or theme
            }
        }

        $menu_item = [
            'page_title' => __('Citadela Special Pages', 'citadela-pro'),
            'menu_title' => __('Citadela Special Pages', 'citadela-pro'),
            'capability' => 'edit_dashboard', //needs to be changed for future
            'menu_slug'  => 'edit.php?post_type=special_page',
            'function'   => '',
            'icon_url'   => Icon::data_url(),
            'position'   => 25 //after Comments menu
        ];

        call_user_func_array( 'add_menu_page', array_values( $menu_item ) );
    }



    function page_title() {
        $special_page = false;

        $qo = get_queried_object();

        if ( $qo && isset( $qo->ID ) && $qo->ID == get_option( 'page_for_posts' ) ) {
            $special_page = 'blog';
            $post_id = Page::id( $special_page );
            $hide_page_title = get_post_meta( $post_id, '_citadela_hide_page_title', true );
        }

        if ( $special_page == 'blog' && !$hide_page_title ) {
            $blog_page_post = get_post( get_option('page_for_posts') );
            $titleText = $blog_page_post->post_title;
            ?>
            <div class="page-title standard">
                <header class="entry-header">
                    <div class="entry-header-wrap">
                        <h1 class="entry-title"><?php echo esc_html($titleText); ?></h1>
                    </div>
                </header>
            </div>
            <?php

        } else {

            return;

        }
    }



    function include_templates( $template ) {

        $qo = get_queried_object();

        if ( $qo && isset( $qo->post_type ) && $qo->post_type == 'page' && $qo->ID == get_option( 'page_for_posts' ) ) {
            return locate_template( 'blog-page.php' ) ? : Page::config( 'blog.template' );
        }

        return $template;
    }



    function special_page_content() {
        $content = "";

        $qo = get_queried_object();
        if ( $qo && isset( $qo->ID ) &&  $qo->ID == get_option( 'page_for_posts' ) ) {
            $content = Page::content( 'blog' );
        }

        $content = apply_filters( 'the_content', $content );
        echo str_replace( ']]>', ']]&gt;', $content );
    }



    function custom_page_title( $html, $attributes, $title_styles ) {
        $qo = get_queried_object();
        if ( $qo && isset( $qo->ID ) && $qo->ID == get_option( 'page_for_posts' ) ) {
            return '
                <h1 class="entry-title" style="' . $title_styles . '">
                    ' . get_queried_object()->post_title . '
                </h1>
            ';
        }

        return $html;
    }



    function body_class( $classes ) {
		$special_page = false;

		$qo = get_queried_object();
        if ( $qo && isset( $qo->post_type ) && $qo->post_type == 'page' && $qo->ID == get_option( 'page_for_posts' ) ) {
            $special_page = 'blog';
        }

		if ( $special_page ) {
			$post_id = Page::id( $special_page );

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

    function admin_head(){
        $this->globalJsVars();
    }

    function wp_head(){
        $this->globalJsVars();
    }

    function globalJsVars() {
        $settings = array(
            'wpSettings' => array(
                'postsPerPage' => get_option('posts_per_page'),
            ),
			'specialPages' => array(
				'blog' => Page::id( 'blog' ),
			),
        );

        if ( get_option( 'page_for_posts' ) ) {
            $settings['page_for_posts'] = [
                'id' => get_option( 'page_for_posts' ),
                'title' => get_post( get_option( 'page_for_posts' ) )->post_title,
            ];
        }

        ?>
        <script type="text/javascript">
            var CitadelaProSettings = <?php echo json_encode( $settings ); ?>
        </script>
        <?php
    }

    

    function remove_special_pages_post_status_filters( $views ) {

        unset( $views['all'] );
        unset( $views['publish'] );
        unset( $views['draft'] );
        unset( $views['trash'] );
        unset( $views['pending'] );

        return $views;

    }



    function show_only_pro_plugin_special_pages( $query ) {

        global $pagenow;

        if( ! ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'special_page' ) )
            return $query;

        $special_pages = Page::get_ids();
        
        $query->set( 'post__in', $special_pages );

        return $query;
    }



}
