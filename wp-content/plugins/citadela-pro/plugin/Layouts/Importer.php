<?php

namespace Citadela\Pro\Layouts;

use Citadela\Pro\Asset;
use Citadela\Pro\Template;
use Citadela\Pro\Less_Compiler;


class Importer {

	static $instance;



	static function instance() {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}



	function register() {
		add_action('wp_ajax_citadela-pro-layout-install-requirements', [$this, 'install_requirements']);
		add_action('wp_ajax_citadela-pro-layout-content-import', [$this, 'handle_content_import']);
		add_action('wp_ajax_citadela-pro-layout-options-import', [$this, 'handle_options_import']);
		add_action('wp_ajax_citadela-pro-layout-images-import', [$this, 'handle_images_import']);
		add_action('wp_ajax_citadela-pro-layout-import-complete', [$this, 'handle_import_complete']);
		add_action('wp_ajax_citadela-pro-layout-import-cancel', [$this, 'handle_import_cancelation']);
	}



	function get_options($file, $array = false) {
		$result = $this->from_zip_get('options.json', $file);
		if ($result === false) {
			wp_delete_file($file);
			wp_send_json_error([
				'title'   => __("It's not Citadela Layout package", 'citadela-pro'),
				'message' => __('Uploaded ZIP file is not Citadela Layout package or it is old incompatible Layout package. Please download the newest package from your AitThemes account.', 'citadela-pro'),
			]);
		}
		return json_decode($result, $array);
	}



	function check_requirements($file) {
		$options = $this->get_options($file, true);
		// remove Citadela Blocks plugin from required plugins
		$options['required_plugins'] = $this->remove_required_plugin( $options['required_plugins'], 'citadela-blocks' );
		$plugins = [
			'install' => $options['required_plugins'],
			'activate' => []
		];
		foreach ($plugins['install'] as $index => $plugin) {
			if ($p = \Citadela::checkPlugin($plugin['slug'])) {
				if (!$p['active']) {
					$plugins['activate'][] = $plugin;
				}
				unset($plugins['install'][$index]);
			}
		}
		$plugins['install'] = array_values($plugins['install']);
		return $plugins;
	}



	function install_requirements() {
		$options = $this->get_options(get_transient('citadela-pro-layout-package'), true);
		foreach ($options['required_plugins'] as $plugin) {
			try {
				\Citadela::installAndActivatePlugin($plugin['slug']);
			} catch (\Exception $exception) {
				wp_send_json_error(isset($exception->response) ? \Citadela::getResponseMessage($exception->response) : ['title' => __('Error', 'citadela-pro'), 'message' => __('There was an error with installing plugins', 'citadela-pro')]);
			}
		}
		wp_send_json_success();
	}



	function ensure_requirements($file) {
		$options = $this->get_options($file, true);
		$active_citadela_plugins = array_map(
			function( $item ) { return dirname( $item ); },
			array_filter(
				get_option( 'active_plugins', [] ),
				function ( $plugin ) {
					return (
						substr($plugin, 0, 8) === 'citadela'
						or substr($plugin, 0, 9) === 'elementor'
						or substr($plugin, 0, 13) === 'block-builder'
						or substr($plugin, 0, 11) === 'woocommerce'
					);
				} )
		);

		$required_plugins = [ 'aitthemes' => [], 'wporg' => [] ];
		$options['required_plugins'] = $this->remove_required_plugin( $options['required_plugins'], 'citadela-blocks' );

		foreach ( $options['required_plugins'] as $required_plugin ) {
			if ( ! in_array( $required_plugin['slug'], $active_citadela_plugins ) ) {
				$required_plugins[ $required_plugin['source'] ][] = $required_plugin['name'];
			}
		}

		if ( ! empty( $required_plugins['aitthemes'] ) or ! empty( $required_plugins['wporg'] ) ) {
			wp_delete_file( $file );
			$title = esc_html__( 'Install required plugins', 'citadela-pro' );
			$message = esc_html__( 'The Citadela Layout requires these plugins. The required plugins will be installed and activated automatically. In case you don\'t have access to one of the plugins, some pages might be incomplete.' );
			$message .= '<ul>';
			if ( ! empty( $required_plugins['aitthemes'] ) ) {
				$message .=  '<li>' .
					// translators: %s - list of required plugins
					sprintf( __( 'From AitThemes: %s', 'citadela-pro' ), '<strong>' . implode( ', ', $required_plugins['aitthemes'] ) . '</strong> ' ) .
					__( '(You can download them from your AitThemes account)', 'citadela-pro' ) .
				'</li>';
			}
			if ( ! empty( $required_plugins['wporg'] ) ) {
				// translators: %s - list of required plugins
				$message .=  '<li>' . sprintf( __( 'From WordPress.org: %s', 'citadela-pro' ), '<strong>' . implode( ', ', $required_plugins['wporg'] ) . '</strong>' ) . '</li>';
			}
			$message .= '</ul>';

			wp_send_json_error( compact( 'title', 'message' ) );
		}
	}



	function handle_content_import() {

		update_option("citadela_layout_import_progress", "wip");
		
		wp_raise_memory_limit('admin');
		set_time_limit(3600);

		\ctdl\pro\log(__METHOD__);

		$this->handle_image_files_delete();

		$results = [];

		Importers\Content::urls(new Urls($this->url_map()));

		$tables = [
			'posts',
			'postmeta',
			'terms',
			'termmeta',
			'term_taxonomy',
			'term_relationships',
			'comments',
			'commentmeta'
		];
		foreach (get_option('active_plugins', []) as $plugin) {
			if (strpos($plugin, 'woocommerce') === 0) {
				$tables = array_merge($tables, [
					'wc_product_meta_lookup',
					'wc_category_lookup',
					'woocommerce_attribute_taxonomies',
				]);
			}
		}
		foreach($tables as $table) {
			$rows = json_decode($this->from_zip_get("$table.json"), true);
			if( $rows ){
				$results[] = Importers\Content::import($table, $rows);
			}
			unset($rows);
		}

        $results[] = Importers\Content::post_import();

		if (!empty(array_filter($results))) {
			wp_send_json_error([
				'message'  => __( 'The content could not be imported.', 'citadela-pro' ),
			]);
		}

		wp_send_json_success();
	}



	function handle_image_files_delete() {
		$delete_extensions = [ 'jpg', 'png', 'gif', 'gpx' ];

		$uploads_dir = wp_upload_dir();
		$directory_iterator = new \RecursiveDirectoryIterator( $uploads_dir['basedir'] );
		$iterator = new \RecursiveIteratorIterator( $directory_iterator );
		
		foreach ($iterator as $data) {
			
			//exclude deletion of woocommerce images
			if( strtolower( substr( $data->getFilename(), 0, 11 ) ) === 'woocommerce') continue;

			$path_name = $data->getPathname();
		  	$ext = strtolower( pathinfo( $path_name )['extension'] );
		  	if( in_array( $ext, $delete_extensions ) ){
		  		@unlink( $path_name );
		  	}

		}
	}



	function handle_options_import() {
		wp_raise_memory_limit( 'admin' );
		set_time_limit(3600);

		\ctdl\pro\log(__METHOD__);

		Importers\Options::urls( new Urls( $this->url_map() ) );

		$result = Importers\Options::import( $this->options() );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [
				'message'  => $result->get_error_message(),
			] );
		}

		wp_send_json_success();
	}



	function handle_images_import() {
		wp_raise_memory_limit( 'admin' );
		set_time_limit(3600);

		\ctdl\pro\log(__METHOD__);

		$result = Importers\Images::import( $this->options()['images_zip_url'] );
		

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [
				'message'  => $result->get_error_message(),
			] );
		}

		wp_send_json_success();
	}



	function handle_import_complete() {
		wp_delete_file( get_transient( 'citadela-pro-layout-package' ) );
		delete_transient( 'citadela-pro-layout-package' );
		Less_Compiler::delete_cache();
		flush_rewrite_rules(false);
		
		update_option("citadela_layout_import_progress", "success");

		\ctdl\pro\log(__METHOD__);
	}



	function handle_import_cancelation() {
		wp_delete_file( get_transient( 'citadela-pro-layout-package' ) );
		delete_transient( 'citadela-pro-layout-package' );
		update_option("citadela_layout_import_progress", "cancelled");
	}



	protected function options() {
		static $options;
		if( ! $options ) {
			$options = json_decode( $this->from_zip_get( "options.json" ), true );
		}
		return $options;
	}



	protected function url_map() {
		return [
			trim( json_encode( $this->options()['uploads_url'] ), '"' ) => trim( json_encode( wp_upload_dir()['baseurl'] ), '"' ),
			$this->options()['uploads_url'] => wp_upload_dir()['baseurl'],
			trim( json_encode( $this->options()['site_url'] ), '"' ) => trim( json_encode( home_url() ), '"' ),
			$this->options()['site_url'] => home_url(),
		];
	}



	protected function from_zip_get( $desired_file, $zip_file = null ) {
		static $_f = [];
		if (!class_exists('ZipArchive')) {
			wp_delete_file($zip_file);
			wp_send_json_error([
				'title'   => __('PHP ZIP extension not found', 'citadela-pro'),
				'message' => __('Layout Importer needs to have the PHP ZIP extension installed. Please install it or contact your hosting provider.', 'citadela-pro'),
			]);
		}
		if ( ! $zip_file ) {
			$zip_file = get_transient( 'citadela-pro-layout-package' );
		}
		if ( empty ( $_f[ $desired_file ] ) ) {
			$_f[ $desired_file ] = @file_get_contents("zip://{$zip_file}#{$desired_file}");
		}
		return $_f[ $desired_file ];
	}



	protected function is_in_required_plugins( $plugin_slug ) {

		foreach ( $this->options()['required_plugins'] as $required_plugin ) {
			if( $required_plugin['slug'] === $plugin_slug ) {
				return true;
			}
		}
		return false;
	}

	// function to remove plugin from list of required plugins, ie. Citadela Blocks plugin which is no more available
	protected function remove_required_plugin( $required_plugins, $plugin_slug ){
		foreach ($required_plugins as $key => $plugin) {
			if( $plugin['slug'] == $plugin_slug ){
				unset( $required_plugins[$key] );
			}
		}
		return $required_plugins;
	}
}
