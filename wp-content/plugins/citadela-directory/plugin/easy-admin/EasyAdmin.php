<?php

namespace Citadela\Directory;

class EasyAdmin {

    protected static $roles = [
        'subscriber'
    ];

    protected static $plugin = null;

    
    public static function run() {

        self::$plugin = \CitadelaDirectory::getInstance();

        add_action( 'admin_init', [ __CLASS__, 'admin_init' ] );
        add_action( 'admin_menu', [ __CLASS__, 'admin_menu' ] );
       
    }

    public static function admin_init() {
       
        if( ! self::easy_admin_css_exists() || ( isset( $_GET['settings-updated'] ) && isset( $_GET['citadela_directory_tab'] ) && $_GET['citadela_directory_tab'] == 'easyadmin' ) ){
            self::compile_easy_admin();
        }
        
        if( self::easy_admin_enabled() ){
            add_action( 'admin_head', [ __CLASS__, 'admin_head' ] );
            
            add_filter( 'admin_body_class', [ __CLASS__, 'admin_body_class' ] );
            add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_enqueue_scripts' ] );

            // disable functionality for easy admin user
            add_filter( 'show_admin_bar', '__return_false' );
            // screen options tab
            add_filter( 'screen_options_show_screen', '__return_false' );

            add_action( 'current_screen', [ __CLASS__, 'current_screen' ] );
            add_action( 'in_admin_header', [ __CLASS__, 'admin_header' ] );
        }
    }

   

    public static function admin_enqueue_scripts() {
       

        $cacheUrl = self::$plugin->paths->url->tmp;
        $cacheDir = self::$plugin->paths->dir->tmp;

        $easyAdminUrl = self::$plugin->paths->url->easyAdmin;
        $easyAdminDir = self::$plugin->paths->dir->easyAdmin;
        
        $jsUrl = self::$plugin->paths->url->js;
        $jsDir = self::$plugin->paths->dir->js;

        wp_enqueue_style(
            'citadela-easy-admin-layout',
             "{$easyAdminUrl}/admin-layout.css",
            [],
            filemtime("{$easyAdminDir}/admin-layout.css")
        );
        wp_enqueue_style(
            'citadela-easy-admin-colors',
             "{$cacheUrl}/citadela-easy-admin.css",
            [],
            filemtime("{$cacheDir}/citadela-easy-admin.css")
        );

        wp_enqueue_script( 
            'citadela-easyadmin', 
            "{$jsUrl}/easy-admin/easyadmin.js",
            array('jquery'),
            filemtime("{$jsDir}/easy-admin/easyadmin.js"),
            true
        );
    }

    public static function admin_menu() {
        if( self::easy_admin_enabled() ){
            remove_menu_page( 'index.php' );
        }
    }

    public static function admin_head() {
        $current_screen = get_current_screen();
        $current_screen->remove_help_tabs();
    }
    public static function current_screen( $current_screen ) {
        // redirect Dashboard to profile page
        if( $current_screen->id == "dashboard" ){
            wp_redirect( admin_url( 'profile.php' ) );
        }
    }

    public static function admin_body_class() {
        return 'citadela-easy-admin-enabled';
    }

    public static function compile_easy_admin() {
        \Citadela\Directory\Less_Compiler::compile_easy_admin();
    }
    
    private static function easy_admin_css_exists() {
        $cache = self::$plugin->paths->dir->tmp;
        $file = "{$cache}/citadela-easy-admin.css";
        return file_exists( $file );
    }  

    private static function easy_admin_enabled() {
        $user = wp_get_current_user();       
        if( $user && ! empty( $user->roles ) && (
	    !empty(array_intersect(self::$roles, $user->roles))
            && self::$plugin->pluginOptions->easyadmin['enable']
            &&  self::easy_admin_css_exists()
        ) ){
            return true;
        }else{
            return false;
        }
    }


    public static function admin_header() {
        global $menu, $submenu;
        $siteLogo = self::$plugin->pluginOptions->easyadmin['siteLogo'];
        
        echo '<div class="easyadmin-header-container">';
            echo '<div class="content">';

                echo '<div class="easyadmin-top-container">';
                    echo '<div class="content narrow">';

                            echo '<div class="easyadmin-logo-container">';
                                echo '<div class="content">';
                                    if( $siteLogo ){
                                        echo '<img src="'. esc_url( $siteLogo ).'" alt="'. esc_html( get_bloginfo( 'title' ) ).'" />';
                                    }else{
                                        echo '<div class="site-title">'. esc_html( get_bloginfo( 'title' ) ) .'</div>';
                                    }
                                echo '</div>';
                            echo '</div>';

                        echo '<div class="easyadmin-menu-container">';
                            echo '<div class="content">';
                                echo '<ul id="easyadmin-user-menu">';
                                    echo '<li><a href="'.esc_url( get_site_url() ).'" class="view-site">'.esc_html__("View Site", 'citadela-directory').'</a></li>';
                                    echo '<li><a href="'.esc_url( wp_logout_url( home_url() ) ).'" class="user-logout">'. esc_html__("Log Out", 'citadela-directory').'</a></li>';
                                echo '</ul>';
                            echo '</div>';
                        echo '</div>';

                    echo '</div>';
                echo '</div>';

                echo '<div class="easyadmin-bottom-container">';
                    echo '<div class="content narrow">';

                        echo '<div class="easyadmin-menu-container">';
                            echo '<div class="content">';
                                echo '<ul id="easyadmin-main-menu">';
                                    _wp_menu_output($menu, $submenu);
                                    do_action( 'adminmenu' );
                                echo '</ul>';
                            echo '</div>';
                        echo '</div>';

                    echo '</div>';
                echo '</div>';

            echo '</div>';
        echo '</div>';

        // remove color schemes from profile page
        remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker' );
    }

}
