<?php
/**
 * Citadela Theme Register sidebars
 */

function citadela_theme_sidebars() {

	$sidebars = array(
		array(
			'id'            => 'home-sidebar',
			'class'         => 'home-sidebar',
			'name'          => esc_html__( 'Home widgets area', 'citadela' ),
			'description'   => esc_html__( 'Widgets displayed on home page.', 'citadela' ),
		),
		array(
			'id'            => 'blog-sidebar',
			'class'         => 'blog-sidebar',
			'name'          => esc_html__( 'Blog page widgets area', 'citadela' ),
			'description'   => esc_html__( 'Widgets displayed on blog page.', 'citadela' ),
		),
		array(
			'id'            => 'pages-sidebar',
			'class'         => 'pages-sidebar',
			'name'          => esc_html__( 'Pages widgets area', 'citadela' ),
			'description'   => esc_html__( 'Widgets displayed on pages.', 'citadela' ),
		),
		array(
			'id'            => 'posts-sidebar',
			'class'         => 'posts-sidebar',
			'name'          => esc_html__( 'Posts widgets area', 'citadela' ),
			'description'   => esc_html__( 'Widgets displayed on posts.', 'citadela' ),
		),
		array(
			'id'            => 'archives-sidebar',
			'class'         => 'archives-sidebar',
			'name'          => esc_html__( 'Archives widgets area', 'citadela' ),
			'description'   => esc_html__( 'Widgets displayed on archive pages.', 'citadela' ),
		),
		array(
			'id'            => 'search-sidebar',
			'class'         => 'search-sidebar',
			'name'          => esc_html__( 'Search results widgets area', 'citadela' ),
			'description'   => esc_html__( 'Widgets displayed on search results page.', 'citadela' ),
		),
		array(
			'id'            => '404-sidebar',
			'class'         => '404-sidebar',
			'name'          => esc_html__( '404 page widgets area', 'citadela' ),
			'description'   => esc_html__( 'Widgets displayed on 404 page.', 'citadela' ),
		),
		array(
			'id'            => 'footer-widgets-area',
			'class'         => 'footer-widgets-area',
			'name'          => esc_html__( 'Footer widgets area', 'citadela' ),
			'description'   => esc_html__( 'Widgets displayed in the footer.', 'citadela' ),
		),
	);
	
	if( Citadela_Theme::get_instance()->is_active_woocommerce() ) {
		$sidebars[] = array(
			'id'            => 'woocommerce-shop-sidebar',
			'class'         => 'woocommerce-shop-sidebar',
			'name'          => esc_html__( 'Woocommerce Shop widgets area', 'citadela' ),
			'description'   => esc_html__( 'Widgets displayed on Shop page.', 'citadela' ),
		);
	}
	

	// support old widgets structure	
	if( ! function_exists( 'wp_use_widgets_block_editor' ) || false === wp_use_widgets_block_editor() ) {
		$defaults = [
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => "</div></div>",
			'before_title'  => '<div class="widget-title">',
			'after_title'   => '</div><div class="widget-container">',
		];
	}else{
		$defaults = [
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => "</div>",
		];
	}

	
	foreach ($sidebars as $sidebar) {
		$args = array_merge( $sidebar, $defaults );
		register_sidebar( $args );
	}
}
add_action( 'widgets_init', 'citadela_theme_sidebars' );

function citadela_theme_widget_title( $title, $instance = array(), $id_base = '') {
	//do not show title for woocommerce minicart in header
	if( isset( $instance['citadela-woocommerce-minicart-widget'] ) && $instance['citadela-woocommerce-minicart-widget'] ) return '';

	$has_title = (trim(str_replace('&nbsp;', '', $title)) !== '');

	if($has_title){
		// default filters were removed, so apply these function manualy to title
		$title = esc_html(convert_chars(wptexturize($title)));
		if($id_base === 'rss'){
			return $title;
		}else{
			return "<h3>{$title}</h3>";
		}
	}
	// if title is empty return whitespace thus condition for checking
	// emptyness of the title in default WP widget will always pass
	// and will outputs $before_title . ' ' . $after_title
	return '<!-- citadela-no-widget-title -->';
}

function citadela_theme_widget_title_filters() {
	remove_filter( 'widget_title', 'wptexturize' );
	remove_filter( 'widget_title', 'convert_chars' );
	remove_filter( 'widget_title', 'esc_html' );
	add_filter( 'widget_title', 'citadela_theme_widget_title', 3, 1999 );
}
add_action( 'init', 'citadela_theme_widget_title_filters' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function citadela_theme_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'citadela_theme_pingback_header' );
