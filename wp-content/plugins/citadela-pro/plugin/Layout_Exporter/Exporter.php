<?php

namespace Citadela\Pro\Layout_Exporter;

class Exporter
{
	function export()
	{
		wp_raise_memory_limit('admin');

		$this->content();
		$this->options();

		return $this;
	}
	protected function content()
	{
		global $wpdb;
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
		foreach ($tables as $table) {
			$rows = $this->{"table_{$table}"}();
			self::fs()->put_contents(
				$this->temp_dir( "/$table.json" ),
				$rows ? json_encode( $rows, JSON_PRETTY_PRINT ) : '[]'
			);
		}
		unset($rows);
		$this->wxr();
		$this->zip_attachments();
	}
	protected function table_posts()
	{
		global $wpdb;
		$postTypes = implode("', '", [
			'post',
			'page',
			'product',
			'product_variation',
			'attachment',
			'nav_menu_item',
			'citadela-item',
			'special_page',
			'elementor_library',
			'custom_css',
			'wp_block'
		]);
		$results = $wpdb->get_results("SELECT * FROM `{$wpdb->posts}` WHERE `post_status` IN ('publish', 'inherit') AND `post_type` IN ('$postTypes') ORDER BY `ID` ASC;", ARRAY_A);
		$resultsFiltered = array_map(function($post) {
			unset($post['guid'], $post['post_author']);
			return $post;
		}, $results);
		unset($results);
		$this->post_ids = wp_list_pluck($resultsFiltered, 'ID');
		return $resultsFiltered;
	}
	protected function table_postmeta()
	{
		global $wpdb;
		$in = implode( ', ', $this->post_ids );
		unset( $this->post_ids );
		return $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE post_id IN ($in) ORDER BY meta_id ASC;" );
	}
	protected function table_terms()
	{
		global $wpdb;

		$taxonomies = implode("', '", array_merge([
			'category',
			'post_tag',
			'nav_menu',
			'citadela-item-category',
			'citadela-item-location',
			'citadela-post-location',
			'elementor_library_type'
		], get_object_taxonomies('product')));

		$results = $wpdb->get_results("SELECT  t.* FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('{$taxonomies}') ORDER BY t.term_id ASC;", ARRAY_A);
		$this->term_ids = wp_list_pluck($results, 'term_id');

		return $results;
	}
	protected function table_termmeta()
	{
		global $wpdb;
		$in = implode( ', ', $this->term_ids );
		return $wpdb->get_results( "SELECT * FROM {$wpdb->termmeta} WHERE term_id IN ($in) ORDER BY meta_id ASC;", ARRAY_A );
	}
	protected function table_term_taxonomy()
	{
		global $wpdb;

		$in = implode( ', ', $this->term_ids );
		unset( $this->term_ids );

		$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->term_taxonomy} WHERE term_id IN ($in) ORDER BY term_id ASC;", ARRAY_A );

		$this->term_tax_ids = wp_list_pluck( $rows, 'term_taxonomy_id' );

		return $rows;
	}
	protected function table_term_relationships()
	{
		global $wpdb;

		$in = implode( ', ', $this->term_tax_ids );
		unset( $this->term_tax_ids );

		return $wpdb->get_results( "SELECT * FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ($in) ORDER BY term_taxonomy_id ASC;", ARRAY_A );
	}
	protected function table_comments()
	{
		global $wpdb;
		$results = $wpdb->get_results("SELECT * FROM `{$wpdb->comments}` WHERE `comment_approved` = 1 ORDER BY `comment_ID` ASC;", ARRAY_A);
		$resultsFiltered = array_map(function($post) {
			unset($post['comment_author_IP']);
			return $post;
		}, $results);
		unset($results);
		$this->comment_ids = wp_list_pluck($resultsFiltered, 'comment_ID');
		return $resultsFiltered;
	}
	protected function table_commentmeta()
	{
		global $wpdb;
		$in = implode(', ', $this->comment_ids);
		unset($this->comment_ids);
		return $wpdb->get_results("SELECT * FROM {$wpdb->commentmeta} WHERE comment_id IN ($in) ORDER BY meta_id ASC;");
	}
	protected function table_wc_product_meta_lookup()
	{
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wc_product_meta_lookup;", ARRAY_A);
	}
	protected function table_wc_category_lookup()
	{
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wc_category_lookup;", ARRAY_A);
	}
	protected function table_woocommerce_attribute_taxonomies()
	{
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies;", ARRAY_A);
	}
	protected function attachments()
	{
		global $wpdb;

		$meta_values = array_map( function( $value ) {
				return maybe_unserialize( $value );
			},
			$wpdb->get_col("
				SELECT
					m.meta_value
				FROM
					{$wpdb->posts} AS p
				INNER JOIN {$wpdb->postmeta} AS m ON (
					p.ID = m.post_id
				)
				WHERE
					p.post_type = 'attachment'
					AND m.meta_key = '_wp_attachment_metadata'
				ORDER BY
					p.ID ASC;
			")
		);

		$base_url = wp_upload_dir()['baseurl'];
		$attachments = [];

		foreach ( $meta_values as $meta ) {
			$subdir = dirname( $meta['file'] );
			$attachments[] = "$base_url/{$meta['file']}";
			foreach( $meta['sizes'] as $_ => $size ) {
				$attachments[] = "$base_url/$subdir/{$size['file']}";
			}
		}

		return $attachments;
	}
	protected function zip_attachments()
	{
		global $wpdb;

		$meta_values = array_map(
			function( $value ) { return maybe_unserialize( $value ); },
			$wpdb->get_col( "SELECT m.meta_value FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS m ON (p.ID = m.post_id) WHERE p.post_type = 'attachment' AND m.meta_key = '_wp_attachment_metadata' ORDER BY p.ID ASC;" )
		);
		$meta_files = $wpdb->get_col( "SELECT m.meta_value FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS m ON (p.ID = m.post_id) WHERE p.post_type = 'attachment' AND m.meta_key = '_wp_attached_file' ORDER BY p.ID ASC;" );

		$base_dir = wp_upload_dir()['basedir'];
		$base_url = wp_upload_dir()['baseurl'];

		$name = 'images-' . date('Y-m-d-His');

		$zip_temp_dir = $base_dir . "/$name";

		wp_mkdir_p( $zip_temp_dir );

		foreach ( $meta_values as $meta ) {
			if (($index = array_search($meta['file'], $meta_files)) !== false) {
				unset($meta_files[$index]);
			}
			$subdir = dirname( $meta['file'] );
			wp_mkdir_p( "$zip_temp_dir/$subdir" );
			self::fs()->copy( "$base_dir/{$meta['file']}", "$zip_temp_dir/{$meta['file']}" );
			foreach( $meta['sizes'] as $_ => $size ) {
				self::fs()->copy( "$base_dir/$subdir/{$size['file']}", "$zip_temp_dir/$subdir/{$size['file']}" );
			}
		}
		foreach ( $meta_files as $file ) {
			$subdir = dirname( $file );
			wp_mkdir_p( "$zip_temp_dir/$subdir" );
			self::fs()->copy( "$base_dir/$file", "$zip_temp_dir/$file" );
		}

		$this->images_zip_url = "$base_url/$name.zip";

		$this->zip( $zip_temp_dir, "$base_dir/$name.zip" );

		self::fs()->delete( $zip_temp_dir, true );
	}
	protected function options()
	{
		global $wpdb;
		$options = [
			'wp_options' => [
				'show_on_front' => get_option('show_on_front'),
				'page_on_front' => get_option('page_on_front'),
				'page_for_posts' => get_option('page_for_posts'),
				'posts_per_page' => get_option('posts_per_page'),
				'blogdescription' => get_option('blogdescription'),
				'site_icon' => get_option('site_icon'),
				'sticky_posts' => get_option('sticky_posts')
			],
			'version' => '3',
			'site_url' => home_url(),
			'uploads_url' => wp_upload_dir()['baseurl'],
			'images_zip_url' => $this->images_zip_url,
			'theme_mods' => $this->theme_mods(),
			'sidebars' => $this->sidebars(),
			'widgets' => $this->widgets(),
			'required_plugins' => $this->required_plugins(),
			'plugins' => $this->plugins(),
			'woocommerce' => $wpdb->get_results("SELECT * FROM {$wpdb->prefix}options WHERE option_name LIKE 'woocommerce_%' AND option_name NOT LIKE 'woocommerce_task_list_%'", ARRAY_N)
		];
		self::fs()->put_contents( $this->temp_dir( '/options.json' ), json_encode( $options, JSON_PRETTY_PRINT ) );
	}
	protected function wxr()
	{
		require_once( ABSPATH . 'wp-admin/includes/export.php' );

		add_filter( 'get_terms_defaults', function( $query_vars) {
			$query_vars['orderby'] = 'term_id';
			return $query_vars;
		} );

		ob_start();

		export_wp( [ 'status' => 'publish' ] );

		header_remove();

		$xml = ob_get_clean();

		self::fs()->put_contents( $this->temp_dir( '/content.xml' ), $xml );
	}
	function zip( $source_dir = null, $zip_file = null )
	{
		if ( empty( $source_dir ) ) {
			$source_dir = $this->temp_dir();
		}
		if ( empty( $zip_file ) ) {
			$zip_file = $this->zip_file();
		}

		$zip = new \ZipArchive;
		$zip->open( $zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE );

		$files = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $source_dir ), \RecursiveIteratorIterator::LEAVES_ONLY );

		foreach ( $files as $name => $file ) {
			if ( ! $file->isDir() ) {
				$file_path = $file->getRealPath();
				$relative_path = substr( $file_path, strlen( realpath($source_dir) ) + 1 );
				$zip->addFile( $file_path, $relative_path );
			}
		}

		$zip->close();

		return $this;
	}
	function download()
	{
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Transfer-Encoding: Binary' );
		header( 'Content-Disposition: attachment; filename=' . $this->zip_name() );
		header( 'Content-Length:  ' . filesize( $this->zip_file() ) );

		ob_clean();

		flush();

		readfile( $this->zip_file() );

		self::fs()->delete( $this->temp_dir(), true );
	}
	function zip_name()
	{
		return sprintf( 'citadela-%s-layout.zip', basename( site_url() ) );
	}
	protected function zip_file()
	{
		return $this->temp_dir( '/' . $this->zip_name() );
	}
	protected function theme_mods()
	{
		$mods = get_theme_mods();

		unset( $mods['citadela_less_files_modified_time'], $mods['0'] );

		return $mods;
	}
	protected function sidebars()
	{
		return get_option( 'sidebars_widgets' );
	}
	protected function widgets()
	{
		global $wpdb;

		$results = $wpdb->get_results( "SELECT `option_name`, `option_value` FROM `{$wpdb->options}` WHERE `option_name` LIKE 'widget_%';" );
		$widgets = [];

		foreach( $results as $result ) {
			$widgets[ $result->option_name ] = maybe_unserialize( $result->option_value );
		}

		return $widgets;
	}
	protected function required_plugins()
	{
		$return = [];

		$pluginsToCheck = [
			'citadela-directory/citadela-directory.php',
			'citadela-pro/citadela-pro.php',
			'block-builder/block-builder.php',
			'elementor/elementor.php',
			'woocommerce/woocommerce.php'
		];

		foreach(get_option('active_plugins', []) as $plugin) {
			if (in_array($plugin, $pluginsToCheck)) {
				$return[] = [
					'slug'   => dirname($plugin),
					'name'   => get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin)['Name'],
					'source' => substr($plugin, 0, 8) === 'citadela' ? 'aitthemes' : 'wporg',
				];
			}
		}

		return $return;
	}
	protected function plugins()
	{
		$plugins = [];
		$is_active = function( $plugin_slug ) {
			foreach( $this->required_plugins() as $required_plugin ){
				if ( $required_plugin['slug'] === $plugin_slug ) {
					return true;
				}
			}
			return false;
		};
		$plugins['citadela-pro'] = [
			'settings' => [
				'citadela_pro_announcements_bar' => array_diff_key( get_option( 'citadela_pro_announcements_bar', [] ), ['date_to' => '', 'date_from' => ''] ),
				'citadela_pro_infobar' => get_option( 'citadela_pro_infobar', [] ),
				'citadela_pro_comments_extension' => get_option( 'citadela_pro_comments_extension', [] ),
				'citadela_pro_events' => get_option( 'citadela_pro_events', [] )
			],
			'special_pages' => [
				'blog_page' => get_option( 'citadela_blog_page' ),
			],
		];

		// export only some of General Settings
		$export_general_settings = [
			'posts_simple_text_styles',
		];
		$integrations_option = get_option( 'citadela_pro_integrations', [] );
		$general_settings = [];
		foreach ($export_general_settings as $setting_name) {
			if( isset( $integrations_option[$setting_name] ) ){
				$general_settings[$setting_name] = $integrations_option[$setting_name];
			}
		}
		if( ! empty($general_settings) ){
			$plugins['citadela-pro']['settings']['citadela_pro_integrations'] = $general_settings;
		}
		
		$plugins['citadela-directory'] = [
			'settings' => [
				'citadela_directory_subscriptions' => get_option('citadela_directory_subscriptions', []),
				'citadela_directory_easyadmin' => get_option('citadela_directory_easyadmin', []),
				'citadela_directory_item_reviews' => get_option('citadela_directory_item_reviews', []),
				'citadela_directory_item_extension' => get_option('citadela_directory_item_extension', []),
				'citadela_directory_item_detail' => get_option('citadela_directory_item_detail', []),
				'citadela_directory_claim_listing' => get_option('citadela_directory_claim_listing', []),
			],
		];
		foreach( [
			'item_location_page',
			'item_category_page',
			'search_items_page',
			'single_item_page',
			'posts_search_results_page',
			'posts_category_page',
			'posts_tag_page',
			'posts_date_page',
			'posts_author_page',
			'default_search_results_page',
			'404_page'
		] as $option ) {
			$plugins['citadela-directory']['special_pages'][ $option ] = $is_active( 'citadela-directory' ) ? get_option( "citadela_{$option}" ) : null;
		}
		$plugins['elementor'] = [
			'settings' => [
				'elementor_container_width'             => get_option( 'elementor_container_width', null ),
				'elementor_cpt_support'                 => get_option( 'elementor_cpt_support', ['page', 'post'] ),
				'elementor_css_print_method'            => get_option( 'elementor_css_print_method', null ),
				'elementor_default_generic_fonts'       => get_option( 'elementor_default_generic_fonts', null ),
				'elementor_disable_color_schemes'       => get_option( 'elementor_disable_color_schemes', null ),
				'elementor_disable_typography_schemes'  => get_option( 'elementor_disable_typography_schemes', null ),
				'elementor_editor_break_lines'          => get_option( 'elementor_editor_break_lines', null ),
				'elementor_exclude_user_roles'          => get_option( 'elementor_exclude_user_roles', [] ),
				'elementor_global_image_lightbox'       => get_option( 'elementor_global_image_lightbox', null ),
				'elementor_page_title_selector'         => get_option( 'elementor_page_title_selector', null ),
				'elementor_scheme_color'                => get_option( 'elementor_scheme_color' , null ),
				'elementor_scheme_color-picker'         => get_option( 'elementor_scheme_color-picker', null ),
				'elementor_scheme_typography'           => get_option( 'elementor_scheme_typography', null ),
				'elementor_space_between_widgets'       => get_option( 'elementor_space_between_widgets', null ),
				'elementor_stretched_section_container' => get_option( 'elementor_stretched_section_container', null ),
				'elementor_load_fa4_shim'               => get_option( 'elementor_load_fa4_shim', null ),
				'elementor_active_kit'					=> get_option( 'elementor_active_kit', null )
			]
		];
		return $plugins;
	}
	protected function temp_dir( $path = '' )
	{
		static $_temp_dir;
		if (!$_temp_dir) {
			$_temp_dir = get_temp_dir() . uniqid('citadela-pro-layout-export-');
			wp_mkdir_p( $_temp_dir );
		}
		return $_temp_dir . ($path ? $path : '');
	}
	protected static function fs()
	{
		global $wp_filesystem;
		WP_Filesystem();
		return $wp_filesystem;
	}
}
