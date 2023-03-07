<?php

namespace Citadela\Pro\Custom_Header;

use Citadela\Pro\Less_Compiler;
use Citadela\Pro\Asset;

class Feature {

	function __construct() {

		$this->register_pages_meta();

		add_action( 'rest_after_insert_page', [ $this, 'rest_after_insert' ], 10, 2 );
		add_action( 'rest_after_insert_special_page', [ $this, 'rest_after_insert' ], 10, 2 );
		add_action( 'rest_after_insert_citadela-item', [ $this, 'rest_after_insert' ], 10, 2 );
			
		add_action( 'body_class', [ $this, 'body_class' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_enqueue' ] );
		
		add_action( 'citadela_pro_header_logo', [ $this, 'header_logo' ] );
		add_action( 'citadela_pro_site_branding_data', [ $this, 'site_branding_data' ] );
		add_action( 'citadela_pro_logo_wrapper_data', [ $this, 'logo_wrapper_data' ] );
		add_action( 'citadela_pro_logo_font_size_data', [ $this, 'logo_font_size_data' ] );

        
        if( ! current_user_can( 'edit_posts' ) ) return;

        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
		
	}


	function frontend_enqueue() {

		$post_id = \Citadela_Theme::get_instance()->get_page_id();
		$header = get_post_meta( $post_id, '_citadela_header', true);

		if( $header ){
			$path = Less_Compiler::cache_path();
			$url = Less_Compiler::cache_url();
	   		$filePath = "{$path}/citadela-custom-header-{$post_id}.css";
	   		$fileUrl = "{$url}/citadela-custom-header-{$post_id}.css";
	   		$lessFilePath = Asset::path( '/css/custom-header.less');
	   		if( ! file_exists( $filePath ) || ( filemtime( $lessFilePath ) > filemtime( $filePath ) ) ) {
	   			Less_Compiler::compile_custom_header( $post_id, $this->get_less_vars( $post_id ) );
	   		}
   			wp_enqueue_style( "citadela-custom-header-styles", $fileUrl, array(), filemtime( $filePath ) );
		}

	}

	function enqueue_block_editor_assets(){
		$current_screen = get_current_screen();
		// enqueue settings only for editor on pages or posts
		if( $current_screen && $current_screen->id != 'widgets' ){
			wp_enqueue_script( 'citadela-pro-custom-header-editor-js' );
		}
    }

	function register_pages_meta() {
		$post_types = [ 'page', 'special_page' ];

        if( defined('CITADELA_DIRECTORY_PLUGIN') ){
            if( \CitadelaDirectory::getInstance()->ItemPageLayout_instance->allowed_editor ){
                $post_types[] = 'citadela-item';            
            }
        }

		foreach ( $post_types as $post_type ) {

            register_meta( 'post', '_citadela_header', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'boolean',
                'single' => true,
                'sanitize_callback' => [ $this, 'sanitize_checkbox'],
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_header_over_content', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'boolean',
                'single' => true,
                'sanitize_callback' => [ $this, 'sanitize_checkbox'],
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_header_text_color', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_header_logo', [
                'object_subtype' => $post_type,
                'type' => 'object',
                'show_in_rest' => array(
		             'schema' => array(
		                 'type'       => 'object',
		                 'properties' => array(
		                     'id' => array(
		                         'type' => 'number',
		                     ),
		                     'url'  => array(
		                         'type' => 'string',
		                     ),
		                     'size'  => array(
		                         'type' => 'object',
		                         'properties' => array(
				                     'height' => array(
				                         'type' => 'number',
				                     ),
				                     'width'  => array(
				                         'type' => 'number',
				                     ),
				                 ),
		                     ),
		                 ),
		             ),
		         ),
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_header_bg_image', [
                'object_subtype' => $post_type,
                'type' => 'object',
                'show_in_rest' => array(
		             'schema' => array(
		                 'type'       => 'object',
		                 'properties' => array(
		                     'id' => array(
		                         'type' => 'number',
		                     ),
		                     'url'  => array(
		                         'type' => 'string',
		                     ),
		                     'size'  => array(
		                         'type' => 'object',
		                         'properties' => array(
				                     'height' => array(
				                         'type' => 'number',
				                     ),
				                     'width'  => array(
				                         'type' => 'number',
				                     ),
				                 ),
		                     ),
		                 ),
		             ),
		         ),
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_header_bg_position', [
                'object_subtype' => $post_type,
                'show_in_rest' => array(
		             'schema' => array(
		                 'type'       => 'object',
		                 'properties' => array(
		                     'x' => array(
		                         'type' => 'number',
		                     ),
		                     'y'  => array(
		                         'type' => 'number',
		                     ),
		                 ),
		             ),
		         ),
                'type' => 'object',
                'single' => true,
                'sanitize_callback' => [ $this, 'sanitize_background'],
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_header_bg_size', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'sanitize_callback' => [ $this, 'sanitize_background'],
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_header_bg_repeat', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'sanitize_callback' => [ $this, 'sanitize_background'],
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_header_bg_fixed', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'boolean',
                'single' => true,
                'sanitize_callback' => [ $this, 'sanitize_checkbox'],
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_header_bg_color', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );
            
            register_meta( 'post', '_citadela_header_bg_image_overlay', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_header_transparent_bg', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'boolean',
                'single' => true,
                'sanitize_callback' => [ $this, 'sanitize_checkbox'],
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

        }
        
        $custom_header_path = \ctdl\pro\path( '/assets/custom-header' );
        $custom_header_url = \ctdl\pro\url( '/assets/custom-header' );
        
        $editor_asset_file = include( "{$custom_header_path}/build/editor.asset.php" );

        wp_register_script(
            'citadela-pro-custom-header-editor-js',
            "{$custom_header_url}/build/editor.js",
            array_merge( [ 'wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-element' ], $editor_asset_file[ 'dependencies' ] ) ,
            filemtime( "{$custom_header_path}/build/editor.js" ),
            true
        );
        wp_set_script_translations( "citadela-pro-custom-header-editor-js", 'citadela-pro', \ctdl\pro\path( '/languages' ) );

	}

	function sanitize_checkbox( $meta_value, $meta_key, $meta_type ) {
		return $meta_value ? $meta_value : 0;
	}

	function sanitize_background( $meta_value, $meta_key, $meta_type ) {
		$base = '_citadela_header_bg';
		
		switch ($meta_key) {
			case "{$base}_size":
				$return = $meta_value ? $meta_value : 'cover';
				break;

			case "{$base}_repeat":
				$return = $meta_value ? $meta_value : 'no-repeat';
				break;
			
			case "{$base}_position":
				$return = $meta_value ? $meta_value : [ 'x' => '0.5', 'y' => '0.5' ];
				break;

			default:
				$return = $meta_value;
				break;
		}
		
		return $return;

	}

	function header_logo() {
		$theme_instance = \Citadela_Theme::get_instance();

		$custom_header = $theme_instance->get_page_meta( '_citadela_header' );
		$logo_image = $theme_instance->get_page_meta( '_citadela_header_logo' );
		$logo_id = $logo_image['id'];
		
		$sticky_header_desktop = get_theme_mod( 'citadela_setting_stickyHeaderDesktop', 'none' );
		$sticky_header_mobile = get_theme_mod( 'citadela_setting_stickyHeaderMobile', 'burger-menu-only' );
		$enableStickyMenu = $sticky_header_desktop == 'sticky-header-desktop-full' || $sticky_header_mobile == 'sticky-header-mobile-full';
		
		$main_logo_id = get_theme_mod( 'custom_logo' );

		if( $custom_header && $logo_image ){
				ob_start();
	        ?>

	        <a href="<?php echo esc_url( home_url() ) ?>" class="custom-logo-link custom-header-logo" rel="home">
				<?php 
				$srcset = wp_get_attachment_image_srcset( $logo_id, 'full' );
        		$sizes = wp_get_attachment_image_sizes( $logo_id, 'full' );
				echo wp_get_attachment_image( $logo_id, 'full', false, [ 'class' => 'custom-logo', 'srcset' => $srcset, 'sizes' => $sizes ]);
	           	?>
	        </a>

	        <?php if( $enableStickyMenu ) : ?>
	        	<a href="<?php echo esc_url( home_url() ) ?>" class="custom-logo-link default-logo" rel="home" style="display:none;">
					<?php 
					$srcset = wp_get_attachment_image_srcset( $main_logo_id, 'full' );
	        		$sizes = wp_get_attachment_image_sizes( $main_logo_id, 'full' );
					echo wp_get_attachment_image( $main_logo_id, 'full', false, [ 'class' => 'custom-logo', 'srcset' => $srcset, 'sizes' => $sizes ]);
		           	?>
		        </a>
	        <?php endif; ?>

	        <?php
	        echo ob_get_clean();

		}else{
			the_custom_logo();
		}

	}

	function site_branding_data() {
		$logo_space_maxwidth_desktop = get_theme_mod( 'citadela_setting_logoSpaceMaxWidthDesktop', 400 );
		echo 'style="max-width:'.$logo_space_maxwidth_desktop.'px;"'; 
	}

	function logo_wrapper_data() {
		$logo_image_maxwidth_desktop = get_theme_mod( 'citadela_setting_logoImageMaxWidthDesktop', 150 );
		$logo_image_maxwidth_mobile = get_theme_mod( 'citadela_setting_logoImageMaxWidthMobile', 150 );
		$logo_image_maxwidth_mobile_data = [];
		$logo_image_maxwidth_mobile_data['desktop'] = $logo_image_maxwidth_desktop.'px';
		$logo_image_maxwidth_mobile_data['mobile'] = $logo_image_maxwidth_mobile.'px';
		echo 'style="max-width:'.$logo_image_maxwidth_desktop.'px;"'; 
		echo 'data-mobile-max-width="'.htmlspecialchars( json_encode( $logo_image_maxwidth_mobile_data ) ).'"'; 
	}
	function logo_font_size_data(){
		$logo_fontsize_mobile = get_theme_mod( 'citadela_setting_logoFontSize', '' );
		if( $logo_fontsize_mobile ){
			$logo_fontsize_mobile_data = [];
			$logo_fontsize_mobile_data['desktop'] = '';
			$logo_fontsize_mobile_data['mobile'] = $logo_fontsize_mobile.'em';
			echo 'data-mobile-font-size="'.htmlspecialchars( json_encode( $logo_fontsize_mobile_data ) ).'"'; 
		}

	}
	function body_class( $classes ) {
		$themeInstance = \Citadela_Theme::get_instance();

		$header = $themeInstance->get_page_meta( '_citadela_header' );
		
		if( $header ){
			$classes[] = 'custom-header';

			$headerOverContent = $themeInstance->get_page_meta( '_citadela_header_over_content' );
			$headerTransparentBg = $themeInstance->get_page_meta( '_citadela_header_transparent_bg' );
			if( $headerOverContent ) $classes[] = 'custom-header-over-content';
			if( $headerTransparentBg ) $classes[] = 'custom-header-transparent-bg';
		}

		return $classes;
    }

    function rest_after_insert( $post, $request ){

		if( in_array( $post->post_type, [ 'page', 'special_page', 'citadela-item' ] ) ){
			
			$header = get_post_meta( $post->ID, '_citadela_header', true);
			if( $header ){
	   			//is custom header, compile css file for header
	   			Less_Compiler::compile_custom_header( $post->ID, $this->get_less_vars( $post->ID ) );
	   		}else{
	   			//remove css file from header
	   			$path = Less_Compiler::cache_path();
	   			wp_delete_file("{$path}/citadela-custom-header-{$post->ID}.css");
	   		}
	    }

    }

    function get_less_vars( $post_id = null ){
    	if( ! $post_id ) return;

    	$headerTextColor = get_post_meta( $post_id, '_citadela_header_text_color', true);
    	$headerOverlayColor = get_post_meta( $post_id, '_citadela_header_bg_image_overlay', true);
    	$headerBgColor = get_post_meta( $post_id, '_citadela_header_bg_color', true);
    	$headerBgImage = get_post_meta( $post_id, '_citadela_header_bg_image', true);
    	$headerBgImage = $headerBgImage && isset($headerBgImage['url']) ? "url({$headerBgImage['url']})" : '';

    	$headerBgSize = get_post_meta( $post_id, '_citadela_header_bg_size', true);
		$headerBgRepeat = get_post_meta( $post_id, '_citadela_header_bg_repeat', true);
		$headerBgFixed = get_post_meta( $post_id, '_citadela_header_bg_fixed', true);
		$headerBgPosition = get_post_meta( $post_id, '_citadela_header_bg_position', true);
		$headerBgPositionX = $headerBgPosition['x'] * 100;
		$headerBgPositionY = $headerBgPosition['y'] * 100;
		$headerBgPosition = "{$headerBgPositionX}% {$headerBgPositionY}%";
    	
		return [
			'customHeaderTextColor' => $headerTextColor,
			'customHeaderOverlayColor' => $headerOverlayColor,
			'customHeaderBgColor' => $headerBgColor,
			'customHeaderBgImage' => $headerBgImage,
			'customHeaderBgSize' => $this->validate_size_less_var( $headerBgSize ),
			'customHeaderBgRepeat' => $headerBgRepeat,
			'customHeaderBgAttachemnt' => $headerBgFixed ? 'fixed' : 'scroll',
			'customHeaderBgPosition' => $headerBgPosition,
		];
    }

    function validate_size_less_var( $var ){
    	switch ( $var ) {
    		case 'full-horizontal':
    			return '100% auto';
    			break;

    		case 'full-vertical':
    			return 'auto 100%';
    			break;
    		
    		default:
    			return $var;
    			break;
    	}
    }
}
