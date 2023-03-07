<?php
/**
 * Citadela Theme Paths
 *
 */

/**
 * Initializes all predefined paths on first use,
 * then is returning all those paths
 * @return stdClass
 */
function citadela_paths()
{
	static $_paths;

	if(is_null($_paths)){

		$_paths = new stdClass;
		$theme_url = get_template_directory_uri();
		$theme_dir = get_template_directory();

		$_paths->url = (object) array(
			'root'		=> home_url(),
			'tmp'		=> citadela_set_tmp_path('url'),
			'languages'	=> $theme_url . '/languages',
			'citadela'	=> $theme_url . '/citadela-theme',
			'admin'		=> $theme_url . '/citadela-theme/admin',
			'settings'	=> $theme_url . '/citadela-theme/admin/settings',
			'blocks'	=> $theme_url . '/citadela-theme/blocks',
			'config'	=> $theme_url . '/citadela-theme/config',
			'assets'	=> $theme_url . '/citadela-theme/assets',
			'design' 	=> $theme_url . '/design',
			'css'	 	=> $theme_url . '/design/css',
			'js' 		=> $theme_url . '/design/js',
			'fonts'		=> $theme_url . '/design/fonts',
			'faJSON'	=> $theme_url . '/design/css/assets/fontawesome/json',

		);

		$_paths->dir = (object) array(
			'root'		=> realpath(ABSPATH),
			'tmp'		=> citadela_set_tmp_path('dir'),
			'languages'	=> $theme_dir . '/languages',
			'citadela'	=> $theme_dir . '/citadela-theme',
			'admin'		=> $theme_dir . '/citadela-theme/admin',
			'settings'	=> $theme_dir . '/citadela-theme/admin/settings',
			'blocks'	=> $theme_dir . '/citadela-theme/blocks',
			'config'	=> $theme_dir . '/citadela-theme/config',
			'assets'	=> $theme_dir . '/citadela-theme/assets',
			'libs'		=> $theme_dir . '/citadela-theme/libs',
			'design' 	=> $theme_dir . '/design',
			'css'	 	=> $theme_dir . '/design/css',
			'js' 		=> $theme_dir . '/design/js',
			'fonts'		=> $theme_dir . '/design/fonts',
			'faJSON'	=> $theme_dir . '/design/css/assets/fontawesome/json'
		);

		return $_paths;

	}else{
		return $_paths;
	}
}

function citadela_set_tmp_path($type)
{
	$uploads_dir = wp_upload_dir();
	$tmp_folder = '/tmp/citadela-theme';
	$dir = $uploads_dir['basedir'] . $tmp_folder;
	$url = $uploads_dir['baseurl'] . $tmp_folder;
	$url = set_url_scheme($url);

	if(!file_exists($dir)){
		wp_mkdir_p($dir);
	}

	return $type == 'dir' ? $dir : $url;
}
