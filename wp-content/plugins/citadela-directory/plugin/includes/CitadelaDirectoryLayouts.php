<?php

class CitadelaDirectoryLayouts {

    private static $plugin = null;

    public static function init()
    {
        self::$plugin = CitadelaDirectory::getInstance();
        add_action( 'enqueue_block_editor_assets', array(__CLASS__, 'blacklistItemBlocks') );

        add_filter( 'template_include', array(__CLASS__, 'include_special_page_template') );

        add_action( 'ctdl_special_page_content', array(__CLASS__, 'pasteSpecialPageContent'));

    }

    // public method used in citadela theme to prevent creation of Citadela Theme Metabox,
    // options in metabox are not usable for this type of pages
    public static function specialPages()
    {
        return array(
            'single-item' => array(
                'option_key' => 'citadela_single_item_page',
                'title' => esc_html__( "Item Detail Page", 'citadela-directory' )
            ),
            'search-results' => array(
                'option_key' => 'citadela_search_items_page',
                'title' => esc_html__( 'Listing Search Results', 'citadela-directory' )
            ),
            'item-category' => array(
                'option_key' => 'citadela_item_category_page',
                'title' => esc_html__( 'Listing Category Page', 'citadela-directory' )
            ),
            'item-location' => array(
                'option_key' => 'citadela_item_location_page',
                'title' => esc_html__( 'Listing Location Page', 'citadela-directory' )
            ),
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
        );
    }



    public static function getSpecialPageId( $specialPage )
    {
        return get_option(self::specialPages()[$specialPage]['option_key']);
    }



    public static function isSpecialPage($id){
        return (bool) self::specialPageById($id);
    }



    public static function specialPageById($id)
    {
        foreach (self::specialPages() as $type => $settings) {
            if (get_option( $settings['option_key'] ) == $id) return $type;
        }
        return false;
    }



    public static function itemPageHasBlock( $block )
    {
        $block = 'citadela-directory/'.$block;
        $id = (int) self::getSpecialPageId('single-item');
        return has_block($block, $id);
    }



    public static function blacklistItemBlocks()
    {
        $deps = [
            'wp-blocks', 
            'wp-dom-ready', 
            'wp-edit-post',
        ];
        $current_screen = get_current_screen();
        if( $current_screen && $current_screen->id == 'widgets' ){
            // "wp-editor" script should not be enqueued together with the new widgets editor (wp-edit-widgets or wp-customize-widgets)
            unset( $deps[array_search('wp-edit-post', $deps)] );
            $deps[] = 'wp-edit-widgets';
        }

        wp_enqueue_script(
            'citadela-directory-blacklist-blocks',
            plugins_url( '../../design/js/blacklist-blocks.js', __FILE__ ),
            $deps,
            filemtime( CitadelaDirectoryPaths::getPaths()->dir->design . '/js/blacklist-blocks.js' )
        );

        $data = array();
        foreach (self::specialPages() as $type => $settings) {
            if ($id =  get_option( $settings['option_key'] )) {
                $data[$type] = $id;
            }
        }
		wp_localize_script('citadela-directory-blacklist-blocks', 'CitadelaDirectorySpecialPages', $data);
    }




    /* replaces content of special page with actual custom gutenberg content of corresponding page */
    public static function pasteSpecialPageContent()
    {
        $content = "";

        if (is_singular('citadela-item')) {

            $content = self::getSpecialPageContent( 'single-item' );
        }
        if (is_tax('citadela-item-category')) {
            $content = self::getSpecialPageContent( 'item-category' );
        }
        if (is_tax('citadela-item-location')) {
            $content = self::getSpecialPageContent( 'item-location' );
        }

        if (is_category()) {
            $content = self::getSpecialPageContent( 'posts-category' );
        }
        if (is_tag()) {
            $content = self::getSpecialPageContent( 'posts-tag' );
        }
        if (is_date()) {
            $content = self::getSpecialPageContent( 'posts-date' );
        }
        if (is_author()) {
            $content = self::getSpecialPageContent( 'posts-author' );
        }
        if( is_404()) {
            $content = self::getSpecialPageContent( '404-page' );
        }
        if ( is_search() && isset( $_REQUEST[ 'ctdl' ] ) ) {
            if ( $_REQUEST[ 'post_type' ] == 'post' ) {
                
                $content = self::getSpecialPageContent( 'posts-search-results' );

            } else {
                
                $content = self::getSpecialPageContent( 'search-results' );

            }
        }

        if ( is_search() && ! isset( $_REQUEST[ 'ctdl' ] ) ) {
            // default WordPress search results page
            $content = self::getSpecialPageContent( 'default-search-results' );
        }

        echo apply_filters( 'the_content',  str_replace( ']]>', ']]&gt;', $content ) );
    }


    public static function include_special_page_template( $template ) {
        // single, archive and taxonomy templates for item cpt are loaded in CitadelaItem class
        if ( is_search() && isset( $_REQUEST[ 'ctdl' ] ) ) {
            if ( $_REQUEST[ 'post_type' ] == 'post' ) {
                
                return locate_template( 'items-search-page.php' ) ? : CitadelaDirectoryPaths::getPaths()->dir->parts . '/posts-search-page.php';
            
            } else {
                
                return locate_template( 'items-search-page.php' ) ? : CitadelaDirectoryPaths::getPaths()->dir->parts . '/items-search-page.php';
            
            }
        }

        if ( is_search() && ! isset( $_REQUEST[ 'ctdl' ] ) ) {
            return locate_template( 'default-search-results.php' ) ? : CitadelaDirectoryPaths::getPaths()->dir->parts . '/default-search-results.php';
        }

        if( is_category() ){
            return locate_template( 'posts-category.php' ) ? : CitadelaDirectoryPaths::getPaths()->dir->parts . '/posts-category.php';
        }
        if (is_tag()) {
            return locate_template( 'posts-tag.php' ) ? : CitadelaDirectoryPaths::getPaths()->dir->parts . '/posts-tag.php';
        }
        if (is_date()) {
            return locate_template( 'posts-date.php' ) ? : CitadelaDirectoryPaths::getPaths()->dir->parts . '/posts-date.php';
        }
        if (is_author()) {
            return locate_template( 'posts-author.php' ) ? : CitadelaDirectoryPaths::getPaths()->dir->parts . '/posts-author.php';
        }
        if (is_404()) {
            return locate_template( '404-page.php' ) ? : CitadelaDirectoryPaths::getPaths()->dir->parts . '/404-page.php';
        }
        return $template;
    }



    /* retrieves a gutenberg content of custom 'layout' page */
    public static function getSpecialPageContent($key = 'single-item')
    {
        $id = get_option( self::specialPages()[$key]['option_key'] );
        $page = get_post($id);
        return $page->post_content;
    }


}