<?php

namespace Citadela\Pro;

use Citadela\Pro\Plugin;

class Less_Compiler {

	static $themeDefaultStyleParameter = 'compile-theme-default';

	static function compile($frontendCompile = false, $themeDefault = false)
	{
		$compileThemeDefault = isset($_GET[self::$themeDefaultStyleParameter]);
		
		$lessFiles = self::files();
		
		$runCompiler = false;

		if (is_customize_preview()) {
			$runCompiler = true;
		} elseif ((!is_admin() && self::check_files_modified_time($lessFiles)) || !file_exists(self::cache_path('/citadela-theme-tmp-style.css'))) {
			$runCompiler = true;
			$frontendCompile = true;
		} elseif ($compileThemeDefault) {
			$runCompiler = true;
			$frontendCompile = true;
		}

		if (!$runCompiler) {
			return false;
		}

		try {
			require_once \ctdl\pro\path( '/vendor/less/Less.inc' );
			$parser = new \Less_Parser( [ 'compress' => false ] );
			$lessVars = self::variables();

			$parser->ModifyVars($lessVars);

			foreach ($lessFiles as $key => $data) {
				if ($data['compile']) {
					$parser->parseFile($data['sourceFile'], $data['sourceFileDirUrl']);
				}
			}

			$css = $parser->getCss();
			file_put_contents( self::cache_path( $frontendCompile ? '/citadela-theme-tmp-style.css' : '/citadela-theme-tmp-preview-style.css' ), $css );

			if ($compileThemeDefault) {
				file_put_contents( self::theme_asset_path( '/css/theme-default-style.css' ), str_replace( "\r\n", "\n", $css ) );
			}
		} catch (\Exception $e) {
			echo esc_html( $e->getMessage() );
		}
	}

	static function compile_custom_header( $id = false, $lessVars = [] ){
		
		$file = Asset::path( '/css/custom-header.less' );
		
		if( ! $id && ! file_exists( $file ) ) return;

		$lessFile = [
			'sourceFile' => $file,
			'sourceFileDirUrl' => Asset::url( '/css' ),
		];

		$lessVars = array_merge( $lessVars, self::variables() );
		
		try {
			require_once \ctdl\pro\path( '/vendor/less/Less.inc' );
			$parser = new \Less_Parser( [ 'compress' => false ] );
			$parser->ModifyVars($lessVars);
			$parser->parseFile($lessFile['sourceFile'], $lessFile['sourceFileDirUrl']);

			$css = $parser->getCss();

			file_put_contents( self::cache_path( "/citadela-custom-header-{$id}.css" ), $css );

		} catch (\Exception $e) {
			echo esc_html( $e->getMessage() );
		}

	}


	static function files()
	{

		$compileThemeDefault = isset($_GET[self::$themeDefaultStyleParameter]);

		$layout = \Citadela_Theme::get_instance()->get_layout('themeLayout');
		$design = 'default';

		//the main theme style.less.file
		// TODO: theme will contain only compiled style.css, less filess will be in pro plugin itself - building theme less will require Pro plugin
		$lessFiles[] =  array(
			'sourceFile' => self::theme_asset_path( '/css/style.less' ),
			'sourceFileDirUrl' => self::theme_asset_url( '/css' ),
			'compile' => true,
		);

		// TODO: load from db as wp option, or from env file or god knows from where
		if ( $compileThemeDefault ) {
			$layoutsPath = self::theme_asset_path( '/css/layouts' );
			$layoutsUrl = self::theme_asset_url( '/css' );
			$designsPath = self::theme_asset_path( '/css/designs' );
			$designsUrl = self::theme_asset_url( '/css' );

			//add Layout file
			$lessFiles[] =  array(
				'sourceFile' => "$layoutsPath/classic.less",
				'sourceFileDirUrl' => $layoutsUrl,
				'compile' => true,
			);
			//add design file
			$lessFiles[] =  array(
				'sourceFile' => "$designsPath/default.less",
				'sourceFileDirUrl' => $designsUrl,
				'compile' => true,
			);

		} else {
			$lessFiles[] = [
				'sourceFile' => Asset::path( '/css/style.less' ),
				'sourceFileDirUrl' => Asset::url( '/css' ),
				'compile' => true,
			];

			//get paths to files based on selected layouts
			if($layout === 'classic'){
				//get file from theme
				$layoutsPath = self::theme_asset_path( '/css/layouts' );
				$layoutsUrl = self::theme_asset_url( '/css' );
			}else{
				//get file from plugin
				$layoutsPath = Asset::path( '/css/layouts' );
				$layoutsUrl = Asset::url( '/css' );
			}

			if($design === 'default'){
				//get file from theme
				$designsPath = self::theme_asset_path( '/css/designs' );
				$designsUrl = self::theme_asset_url( '/css' );
			}else{
				//get file from plugin
				$designsPath = Asset::path( '/css/design' );
				$designsUrl = Asset::url( '/css' );
			}
			
			//add Layout file
			$lessFiles[] =  array(
				'sourceFile' => "$layoutsPath/$layout.less",
				'sourceFileDirUrl' => $layoutsUrl,
				'compile' => true,
			);
			//add design file
			$lessFiles[] =  array(
				'sourceFile' => "$designsPath/$design.less",
				'sourceFileDirUrl' => $designsUrl,
				'compile' => true,
			);

			// Pro features file
			$lessFiles[] =  array(
				'sourceFile' => Asset::path( '/css/profeatures.less' ),
				'sourceFileDirUrl' => Asset::url( '/css' ),
				'compile' => true,
			);
		}


		return $lessFiles;
	}



	static function check_files_modified_time( $files )
	{
		$mod = get_theme_mod('citadela_less_files_modified_time', false);
		if ($mod) {
			foreach ($files as $key => $data) {
				$currentModifiedTime = filemtime($data['sourceFile']);
				if (!isset($mod[$data['sourceFile']]) || $mod[$data['sourceFile']] < $currentModifiedTime) {
					self::save_files_modified_times($files, $mod);
					return true;
				}
			}
			return false;
		} else {
			self::save_files_modified_times($files);
			return true;
		}
	}



	static function save_files_modified_times($files, $mod = [])
	{
		foreach ($files as $key => $data) {
			$mod[$data['sourceFile']] = filemtime($data['sourceFile']);
		}
		set_theme_mod('citadela_less_files_modified_time', $mod);
	}



	static function variables()
	{
		// TODO: load from db as wp option
		$compileThemeDefault = isset($_REQUEST[self::$themeDefaultStyleParameter]);


		$vars = [];
		foreach ( Customize::config( 'controls' ) as $section => $controls) {
			foreach ($controls as $controlName => $controlData) {
				if(isset($controlData['less_var']) && $controlData['less_var'] != ""){
					$data = [];
					$data['type'] = $controlData['control_type'];

					if ($controlData['control_type'] == 'checkbox') {
						$data['default'] = $controlData['values']['default'];
						$data['saved'] = $controlData['values']['saved'];
					} else {
						$data['default'] = $compileThemeDefault ? ( isset( $controlData['theme_default'] ) ? $controlData['theme_default'] : $controlData['default'] ) : $controlData['default'] ;
					}

					$vars[$controlData['less_var']] = $data;
				}
			}
		}
		

		return self::validate_variables($vars);
	}



	static function validate_variables($variables = [])
	{
		if (!is_array($variables)) {
			return $variables;
		}
		$validated = [];

		// TODO: load from db as wp option
		$compileThemeDefault = isset($_REQUEST[self::$themeDefaultStyleParameter]);

		if ($compileThemeDefault) {
			foreach ($variables as $key => $value) {
				$validated[$key] = $value['default'];
			}
		} else {
			foreach ($variables as $key => $value) {
				$mod = get_theme_mod( 'citadela_setting_'.$key, $value['default'] );
				switch ($value['type']) {
					case 'select':
						$validated[$key] = $mod;
						break;
					case 'color':
						$validated[$key] = ($mod == '') ? $value['default'] : $mod;
						break;
					case 'image':
						$validated[$key] = ($mod == '') ? "''" : "\"{$mod}\"";
						break;

					case 'checkbox':
						// get text value on the base of boolean value stored in db
						if( $mod === true ){
							$validated[$key] = $value['saved'];
						}else{
							$validated[$key] = $value['default'];
						}
						break;
					case 'number':
						$validated[$key] = strval( $mod );
						break;
					case 'google_font_select':
						$validated[$key] = "\"{$mod}\"";
						break;
					default:
						$validated[$key] = $mod;
						break;
				}
			}
		}
		return $validated;
	}



	static function delete_cache($path = null)
	{
		$path = $path ? : self::cache_path();
		$files = array_diff( scandir($path), ['.', '..']);

		foreach ($files as $file) {
			wp_delete_file("$path/$file");
		}
	}



	protected static function theme_asset_path( $relative_path = '/' ) {
		return get_theme_file_path( '/design' . ( ( $p = trim( $relative_path, '/' ) ) ? "/$p" : '' ) );
	}



	protected static function theme_asset_url( $relative_uri = '/' ) {
		return get_theme_file_uri( '/design' . ( ( $p = trim( $relative_uri, '/' ) ) ? "/$p" : '' ) );
	}



	static function cache_path( $relative_path = '/' ) {
		return wp_upload_dir()['basedir'] . '/tmp/citadela-theme' . ( ( $p = trim( $relative_path, '/' ) ) ? "/$p" : '' );
	}



	static function cache_url( $relative_url = '/' ) {
		return wp_upload_dir()['baseurl'] . '/tmp/citadela-theme' . ( ( $p = trim( $relative_url, '/' ) ) ? "/$p" : '' );
	}
}
