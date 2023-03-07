<?php
function citadela_blocks_enqueue_assets_service() {
	$slug = "service";
	$pluginInstance = CitadelaBlocks::getInstance();
	if( ! is_admin() ) {
		$block_url = $pluginInstance->paths->url->blocks . '/' . $slug;
		$block_path = $pluginInstance->paths->dir->blocks . '/' . $slug;
		wp_register_script(
			"citadela-{$slug}-frontend-js",
			$block_url . '/frontend-js.js',
			array('jquery'),
			filemtime( $block_path . '/frontend-js.js' )
		);
		wp_enqueue_script("citadela-{$slug}-frontend-js");
	}
}
add_action( 'enqueue_block_assets', 'citadela_blocks_enqueue_assets_service');


/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function citadela_blocks_init_service() {

    $slug = "service";
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type_from_metadata' ) ) {
		return;
	}
	$dir = dirname( __FILE__ );

	$editor_css = 'css/editor.css';
	wp_register_style(
		"citadela-block-{$slug}-editor",
		plugins_url( $editor_css, __FILE__ ),
		array(),
		filemtime( "$dir/$editor_css" )
	);

	$style_css = 'css/style.css';
	wp_register_style(
		"citadela-block-{$slug}-style",
		plugins_url( $style_css, __FILE__ ),
		array(),
		filemtime( "$dir/$style_css" )
	);

	register_block_type_from_metadata( __DIR__, array(
		'editor_style'    => "citadela-block-{$slug}-editor",
        'style'           => "citadela-block-{$slug}-style",
    ) );
}
add_action( 'init', 'citadela_blocks_init_service' );
