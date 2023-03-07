<?php

namespace Citadela\Directory;

class Less_Compiler {

	
	static function compile_easy_admin()
	{
				
		$paths = \CitadelaDirectory::getInstance()->paths;

		$lessFiles[] =  array(
			'sourceFile' => $paths->dir->easyAdmin . '/admin-colors.less',
			'sourceFileDirUrl' => '',
			'compile' => true,
		);

		try {

			require_once $paths->dir->libs . '/less/Less.inc';

			$parser = new \Less_Parser( [ 'compress' => false ] );
			$lessVars = self::easy_admin_vars();

			$parser->ModifyVars($lessVars);

			foreach ($lessFiles as $key => $data) {
				if ($data['compile']) {
					$parser->parseFile($data['sourceFile'], $data['sourceFileDirUrl']);
				}
			}

			$css = $parser->getCss();
			file_put_contents( self::cache_path( '/citadela-easy-admin.css' ), $css );

		} catch (\Exception $e) {
			echo esc_html( $e->getMessage() );
		}
	}

	static function easy_admin_vars()
	{
		$settings = \CitadelaDirectorySettingsEasyAdmin::settings();
		$saved = get_option('citadela_directory_easyadmin', false);
		
		$vars = [];
		foreach ( $settings as $setting => $data) {
			if( isset( $data['less'] ) && $data['less'] != '' ){
				$var_name = $data['less'];
				
				if( $saved && isset( $saved[$var_name] ) ){
					$var_value = $saved[$var_name];
				}else{
					$var_value = $data['default'];
				}

				//validate variable
				switch ($data['type']) {
					case 'background':
						//save separatedd background less vars
						$image = ($var_value['image'] == '') ? '""' : '"'.$var_value['image'].'"';
						$vars[$var_name.'Image'] 		= $image;
						$vars[$var_name.'Color'] 		= $var_value['color'];
						$vars[$var_name.'Repeat'] 		= $var_value['repeat'];
						$vars[$var_name.'Position'] 	= $var_value['position'];
						$vars[$var_name.'Attachment'] 	= $var_value['scroll'];
						$vars[$var_name.'Size'] 		= $var_value['size'];
						break;
					case 'image':
						$var_value = ($var_value == '') ? '""' : '"'.$var_value.'"';
						$vars[$var_name] = $var_value;
						break;
					case 'checkbox':
						// get text value on the base of boolean value stored in db
						$validated[$key] = ( $var_value == 0 ) ? $value['less_values']['off'] : $value['less_values']['on'];
						break;
					case 'number':
						$var_value = isset($data['less_unit']) ? strval($var_value) . $data['less_unit']  : strval( $var_value );
						$vars[$var_name] = $var_value;
						break;
					default:
						$vars[$var_name] = $var_value;
						break;
				}

			}
		}
		
		return $vars;
	}


	static function delete_cache($path = null)
	{
		$path = $path ? : self::cache_path();
		$files = array_diff( scandir($path), ['.', '..']);

		foreach ($files as $file) {
			wp_delete_file("$path/$file");
		}
	}

	protected static function cache_path( $relative_path = '/' ) {
		return wp_upload_dir()['basedir'] . '/tmp/citadela-directory' . ( ( $p = trim( $relative_path, '/' ) ) ? "/$p" : '' );
	}
}
