<?php
/**
 * Citadela Theme Configuration
 *
 */

function citadela_config() {

	static $_config;

	if(is_null($_config)){

		$_config = new stdClass;

		$_config->menus = (object) array(
			'main'   => esc_html__('Main menu', 'citadela'),
			'footer' => esc_html__('Footer menu', 'citadela'),
		);

		$_config->assets = (object) array(
			'frontend' => (object) array(
				'js' => array(
					'citadela-modernizr-touch' 	=> array(
													'file' 	=> 'modernizr/modernizr.touch.min.js',
													'deps' 	=> array(),
													'ver'	=> '3.6.0'
												),
					'citadela-waypoints' 			=> array(
													'file' 	=> 'waypoints/jquery.waypoints.min.js',
													'deps' 	=> array(),
													'ver'	=> '4.0.1'
												),
					'citadela-photoswipe' 		=> array(
													'file' 	=> 'photoswipe/photoswipe.min.js',
													'deps' 	=> array(),
													'ver'	=> '4.1.3'
												),
					'citadela-photoswipe-ui' 		=> array(
													'file' 	=> 'photoswipe/photoswipe-ui-default.min.js',
													'deps' 	=> array(),
													'ver'	=> '4.1.3'
												),
					'citadela-focus-within-polyfil' => array(
													'file' 	=> 'polyfills/focus-within-polyfill.min.js',
													'deps' 	=> array(),
													'ver'	=> '5.0.4'
												),

				),

				'css' => array(
					'citadela-photoswipe-css' 	=> array(
													'file' 	=> 'photoswipe/photoswipe.css',
													'deps' 	=> array(),
													'ver'	=> '4.1.3'
												),
					'citadela-photoswipe-css-default-skin'
											 	=> array(
													'file' 	=> 'photoswipe/default-skin/default-skin.css',
													'deps' 	=> array(),
													'ver'	=> '4.1.3'
												),

				),
			),

			'admin' => (object) array(
				'js' => array(

				),

				'css' => array(

				),
			),

		);


		$_config->styles = (object) array(
			'frontend' => array(
				'citadela-fontawesome'	=> array(
										'file'	=> 'assets/fontawesome/css/all.min.css',
										'deps' 	=> array(),
										'ver'	=> '5.8.2',
									),
			),

			'admin' => array(
				'citadela-fontawesome'	=> array(
										'file'	=> 'assets/fontawesome/css/all.min.css',
										'deps' 	=> array(),
										'ver'	=> '5.8.2',
									),
				'citadela-admin-styles' => array(
								'file' 	=> 'admin/admin-style.css',
								'deps' 	=> array(),
								'ver'	=> filemtime( citadela_paths()->dir->css . '/admin/admin-style.css' ),
							),
			),

		);

		$_config->scripts = (object) array(
			'frontend' => array(
				'citadela-fancybox' 	=> array(
										'file' 	=> 'fancybox.js',
										'deps' 	=> array('jquery'),
										'ver'	=> filemtime( citadela_paths()->dir->js . '/fancybox.js' ),
									),
				'citadela-menu' 		=> array(
										'file' 	=> 'menu.js',
										'deps' 	=> array('jquery'),
										'ver'	=> filemtime( citadela_paths()->dir->js . '/menu.js' ),
									),
				'citadela-mobile-js'	=> array(
										'file' 	=> 'mobile.js',
										'deps' 	=> array('jquery', 'citadela-modernizr-touch'),
										'ver'	=> filemtime( citadela_paths()->dir->js . '/mobile.js' ),
									),
			),
			'admin' => array(

			),
		);

		$_config->default_layouts = array(
			'themeLayout' 		=> 'classic',
			'headerLayout' 		=> 'classic',
			'themeDesign' 		=> 'default',
		);

		return $_config;

	}else{
		return $_config;
	}

}