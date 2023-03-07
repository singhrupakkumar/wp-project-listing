<?php

define('CITADELA_THEME', true);

if ( ! function_exists( 'citadela_theme_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function citadela_theme_setup() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		register_nav_menus( array(
			'main-menu' => esc_html__( 'Main menu', 'citadela' ),
			'footer-menu' => esc_html__( 'Footer menu', 'citadela' ),
		) );
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
		add_theme_support( 'align-wide' );

		add_theme_support( 'woocommerce' );

		if(!isset($content_width)) $content_width = 768;
	}
endif;
add_action( 'after_setup_theme', 'citadela_theme_setup' );



/**
 *	WooCommerce
 */
if( class_exists('woocommerce') ){

	function citadela_woocommerce_get_image_size_gallery_thumbnail( $size ) {
		return array(
			'width' => 150,
			'height' => 150,
			'crop' => 0,
		);

	}
	add_filter( 'woocommerce_get_image_size_gallery_thumbnail', 'citadela_woocommerce_get_image_size_gallery_thumbnail' );
}


/**
 * Compatibility
 */
require_once get_template_directory() . '/citadela-theme/compatibility.php';

if(!citadela_support_php() or !citadela_support_wp()){
	return;
}
citadela_handle_ait_plugins_support();


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/citadela-theme/template-tags.php';


/**
 * Custom functions and hooks for Widgets
 */
require get_template_directory() . '/citadela-theme/widgets.php';


/**
 * Customizer additions.
 */
require get_template_directory() . '/citadela-theme/customizer.php';


/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/citadela-theme/jetpack.php';
}


require_once get_template_directory() . '/citadela-theme/config/paths.php';
require_once citadela_paths()->dir->config . '/config.php';
require_once citadela_paths()->dir->citadela . '/CitadelaTheme.php';

Citadela_Theme::get_instance()->run( __FILE__ );

require_once get_template_directory() . '/citadela-theme/Citadela.php';
add_action('admin_enqueue_scripts', function () {
	wp_enqueue_script('citadela-script', get_template_directory_uri() . '/design/js/citadela.js', [], filemtime(__DIR__ . '/design/js/citadela.js'));
});