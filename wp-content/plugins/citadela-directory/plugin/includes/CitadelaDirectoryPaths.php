<?php

// ===============================================
// Citadela Listing plugin paths
// -----------------------------------------------


class CitadelaDirectoryPaths {
	
	protected static $plugin;

	public function __construct(){
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}

	public static function getPaths(){
		
		self::$plugin = CitadelaDirectory::getInstance();
		
		static $_citadelaPaths;

		if(is_null($_citadelaPaths)){

			$_citadelaPaths = new stdClass;
			$plugin_url = self::$plugin->baseurl;
			$plugin_dir = self::$plugin->basedir;

			$_citadelaPaths->url = (object) array(
				'root'		=> home_url(),
				'tmp'		=> self::citadelaSetCachePath('url'),
				'design' 	=> $plugin_url . '/design',
				'css'	 	=> $plugin_url . '/design/css',
				'easyAdmin' => $plugin_url . '/design/css/easy-admin',
				'images' 	=> $plugin_url . '/design/images',
				'js' 		=> $plugin_url . '/design/js',
				'languages'	=> $plugin_url . '/languages',
				'assets'	=> $plugin_url . '/plugin/assets',
				'blocks'	=> $plugin_url . '/blocks',
				'cpt'		=> $plugin_url . '/plugin/cpt',
				'libs'		=> $plugin_url . '/plugin/libs',
				'parts'		=> $plugin_url . '/plugin/parts',

			);

			$_citadelaPaths->dir = (object) array(
				'root'		=> realpath(ABSPATH),
				'tmp'		=> self::citadelaSetCachePath('dir'),
				'plugin_dir'=> $plugin_dir,
				'design' 	=> $plugin_dir . '/design',
				'css'	 	=> $plugin_dir . '/design/css',
				'easyAdmin' => $plugin_dir . '/design/css/easy-admin',
				'images' 	=> $plugin_dir . '/design/images',
				'js' 		=> $plugin_dir . '/design/js',
				'languages'	=> $plugin_dir . '/languages',
				'assets'	=> $plugin_dir . '/plugin/assets',
				'blocks'	=> $plugin_dir . '/blocks',
				'controls'	=> $plugin_dir . '/plugin/controls',
				'cpt'		=> $plugin_dir . '/plugin/cpt',
				'item'		=> $plugin_dir . '/plugin/cpt/item',
				'includes'	=> $plugin_dir . '/plugin/includes',
				'libs'		=> $plugin_dir . '/plugin/libs',
				'parts'		=> $plugin_dir . '/plugin/parts',
			);

			return $_citadelaPaths;

		}else{
			return $_citadelaPaths;
		}
	}

	private static function citadelaSetCachePath($type) {
		$uploadsDir = wp_upload_dir();
		$citadelaCacheFolder = '/tmp/citadela-directory';
		$dir = $uploadsDir['basedir'] . $citadelaCacheFolder;
		$url = $uploadsDir['baseurl'] . $citadelaCacheFolder;
	
		if(!file_exists($dir)){
			wp_mkdir_p($dir);
		}
	
		return $type == 'dir' ? $dir : $url;
	}
	 

}

