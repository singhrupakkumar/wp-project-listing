<?php

// ===============================================
// Citadela Listing plugin configuration
// -----------------------------------------------


class CitadelaDirectoryConfigGeneral {
	
	protected static $plugin;

	public function __construct(){
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}

	public static function getConfigData(){
		
		static $_citadelaConfiguration;
		self::$plugin = CitadelaDirectory::getInstance();

		if(is_null($_citadelaConfiguration)){
			$_citadelaConfiguration = new stdClass;	

			// main plugin CSS and Javascript assets
			$_citadelaConfiguration->assets = (object) array(
				'leaflet' => (object) [
					'css' => [
						'citadela-leaflet'
						 	=> array(
								'file' 	=> 'leaflet/leaflet.css',
								'deps' 	=> array( 'citadela-leaflet-markercluster' ),
								'ver'	=> '1.6.0'
							),
						'citadela-leaflet-markercluster'
						 	=> array(
								'file' 	=> 'leaflet/MarkerCluster.css',
								'deps' 	=> array( 'citadela-leaflet-markercluster-default' ),
								'ver'	=> '1.4.1'
							),
						'citadela-leaflet-markercluster-default'
						 	=> array(
								'file' 	=> 'leaflet/MarkerCluster.Default.css',
								'deps' 	=> array(),
								'ver'	=> '1.4.1'
							),
						'citadela-leaflet-gesture-handling'
						 	=> array(
								'file' 	=> 'leaflet/leaflet-gesture-handling.min.css',
								'deps' 	=> array(),
								'ver'	=> '1.4.1'
							),
					],
					'js' => [
				        'citadela-leaflet'  => array(
				                    'file'  => 'leaflet/leaflet.js',
				                    'deps'  => array(),
				                    'ver'   => '1.6.0',
				                ),
					],
				 ],

				'frontend' => (object) array(

						'css' => array(
							'citadela-fontawesome'	=> array(
										'file'	=> 'fontawesome/css/all.min.css',
										'deps' 	=> array(),
										'ver'	=> '5.8.2',
									),
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
							'citadela-raty'		=> array(
									'file' 	=> 'raty/jquery.raty.css',
									'deps' 	=> array(),
									'ver'	=> '2.8.0'
								),
						),

						'js' => array(
							'citadela-modernizr-touch' 	=> array(
										'file' 	=> 'modernizr/modernizr.touch.min.js',
										'deps' 	=> array(),
										'ver'	=> '3.6.0'
									),
							'citadela-photoswipe' => array(
										'file' 	=> 'photoswipe/photoswipe.min.js',
										'deps' 	=> array(),
										'ver'	=> '4.1.3'
									),
							'citadela-photoswipe-ui' 		=> array(
										'file' 	=> 'photoswipe/photoswipe-ui-default.min.js',
										'deps' 	=> array(),
										'ver'	=> '4.1.3'
									),
							'citadela-overlapping-marker-spiderfier' => array(
										'file' => 'overlapping-marker-spiderfier/oms.min.js',
										'deps' => array( 'citadela-google-maps' ),
										'ver'  => '1.0',
									),
							'citadela-markerclusterer' => array(
										'file' => 'markerclusterer/markerclusterer.js',
										'deps' => array( 'citadela-google-maps' ),
										'ver'  => '1.0.3',
									),
							'citadela-markerwithlabel' 	=> array(
										'file' 	=> 'markerwithlabel/markerwithlabel-min.js',
										'deps' 	=> array( 'citadela-google-maps' ),
										'ver'	=> '1.2.3',
									),
							'citadela-raty'		=> array(
										'file' 	=> 'raty/jquery.raty.js',
										'deps' 	=> array( 'jquery' ),
										'ver'	=> '2.8.0'
									),
							'citadela-utils'	=> array(
										'file' => 'citadela/citadela-utils.js',
										'deps' => array( 'jquery' ),
										'ver'  => filemtime( self::$plugin->paths->dir->assets . '/citadela/citadela-utils.js' ),
									),
						),
					
				),

				'backend' => (object) array(

						'css' => array(
							'citadela-fontawesome'	=> array(
										'file'	=> 'fontawesome/css/all.min.css',
										'deps' 	=> array(),
										'ver'	=> '5.8.2',
									),
							'citadela-colorpicker' 	=> array(
										'file' 	=> 'colorpicker/colorpicker.css',
										'deps' 	=> array(),
										'ver'	=> '1.0'
									),
							'citadela-fontawesome-iconpicker' => array(
										'file' => 'fontawesome-iconpicker/fontawesome-iconpicker.css',
										'deps' => array(),
										'ver'  => filemtime( self::$plugin->paths->dir->assets . '/fontawesome-iconpicker/fontawesome-iconpicker.css' ),
									),
							'citadela-leaflet'
									=> array(
									   'file' 	=> 'leaflet/leaflet.css',
									   'deps' 	=> array( 'citadela-leaflet-markercluster' ),
									   'ver'	=> '1.6.0'
								   ),
						   'citadela-leaflet-markercluster'
								=> array(
								   'file' 	=> 'leaflet/MarkerCluster.css',
								   'deps' 	=> array( 'citadela-leaflet-markercluster-default' ),
								   'ver'	=> '1.4.1'
							   ),
						   'citadela-leaflet-markercluster-default'
								=> array(
								   'file' 	=> 'leaflet/MarkerCluster.Default.css',
								   'deps' 	=> array(),
								   'ver'	=> '1.4.1'
							   ),
						),

						'js' => array(
							'citadela-classnames' 	=> array(
												'file' 	=> 'classnames/classnames.js',
												'deps' 	=> array(),
												'ver'	=> '1.0'
											),				
							'citadela-colorpicker' 	=> array(
												'file' 	=> 'colorpicker/bootstrap-colorpicker.js',
												'deps' 	=> array(),
												'ver'	=> '1.0'
											),
							
							'citadela-fontawesome-iconpicker' => array(
										'file' => 'fontawesome-iconpicker/fontawesome-iconpicker.js',
										'deps' => array(),
										'ver'  => filemtime( self::$plugin->paths->dir->assets . '/fontawesome-iconpicker/fontawesome-iconpicker.js' ),
									),
							'citadela-utils' => array(
										'file' => 'citadela/citadela-utils.js',
										'deps' => array(),
										'ver'  => filemtime( self::$plugin->paths->dir->assets . '/citadela/citadela-utils.js' ),
									),
							'citadela-admin-controls' => array(
										'file' => 'citadela/admin-controls.js',
										'deps' => array(),
										'ver'  => filemtime( self::$plugin->paths->dir->assets . '/citadela/admin-controls.js' ),
									),
							'citadela-leaflet' 	=> array(
										'file' 	=> 'leaflet/leaflet.js',
										'deps' 	=> array(),
										'ver'	=> '1.6.0',
									),
							'citadela-leaflet-markercluster' 	=> array(
										'file' 	=> 'leaflet/leaflet.markercluster.js',
										'deps' 	=> array('citadela-leaflet'),
										'ver'	=> '1.4.1',
									),
						),
					
				),				
			);

			// main plugin CSS files
			$_citadelaConfiguration->styles = (object) array(
				'frontend' => array(
						'citadela-directory-frontend' => array(
								'file' 	=> 'style.css',
								'deps' 	=> array(),
								'ver'	=> filemtime( self::$plugin->paths->dir->css . '/style.css' ),
							),
				),
				
				'backend' => array(
						'citadela-directory-admin' => array(
								'file' 	=> 'admin/admin-style.css',
								'deps' 	=> array(),
								'ver'	=> filemtime( self::$plugin->paths->dir->css . '/admin/admin-style.css' ),
							),
				), 
				
			);

			// main plugin Javascript files
			$_citadelaConfiguration->scripts = (object) array(
				'frontend' => array(
						'citadela-directory-fancybox' 	=> array(
								'file' 	=> 'fancybox.js',
								'deps' 	=> array('jquery'),
								'ver'	=> filemtime( self::$plugin->paths->dir->js . '/fancybox.js' ),
							),
				),
				'backend' => array(
					
				),
			);
			
			//Frontend ajax classnames
			$_citadelaConfiguration->frontendAjax = array(
					'item-contact-form',
			);

			$_citadelaConfiguration->blocks = (object) array(
				
			);

			return $_citadelaConfiguration;
		}else{
			return $_citadelaConfiguration;
		}
	}

}

