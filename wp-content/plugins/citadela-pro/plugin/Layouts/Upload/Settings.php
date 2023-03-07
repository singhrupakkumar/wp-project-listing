<?php

namespace Citadela\Pro\Layouts\Upload;

use Citadela\Pro\Asset;
use Citadela\Pro\Template;
use Citadela\Pro\Less_Compiler;
use Citadela\Pro\Layouts\Importer;



class Settings {
	function __construct()
	{
		add_action('admin_action_citadela-pro-layout-upload', function () {
			check_ajax_referer('citadela-pro-layout-upload');
			send_nosniff_header();
			nocache_headers();
			if (!current_user_can('upload_files')) {
				wp_send_json_error([
					'message'  => __( 'You do not have permission to upload files.')
				]);
			}
			$upload = wp_handle_upload($_FILES['layout'], ['test_form' => false, 'test_type' => false]);
			if (is_wp_error($upload)) {
				wp_send_json_error([
					'message'  => $upload->get_error_message(),
				]);
			}
			Importer::instance()->ensure_requirements($upload['file']);
			set_transient('citadela-pro-layout-package', $upload['file']);
			\ctdl\pro\log(__METHOD__);
			wp_send_json_success([
				'urls' => [
					'content'  => add_query_arg(['action' => 'citadela-pro-layout-content-import'], admin_url('admin-ajax.php')),
					'options'  => add_query_arg(['action' => 'citadela-pro-layout-options-import'], admin_url('admin-ajax.php')),
					'images'   => add_query_arg(['action' => 'citadela-pro-layout-images-import'], admin_url('admin-ajax.php')),
					'complete' => add_query_arg(['action' => 'citadela-pro-layout-import-complete'], admin_url('admin-ajax.php')),
					'cancel'   => add_query_arg(['action' => 'citadela-pro-layout-import-cancel'], admin_url('admin-ajax.php'))
				]
			]);
		});
	}
	function slug()
	{
		return 'layout-import';
	}
	function tab()
	{
		return [
			'label' => __('Import layout from file', 'citadela-pro')
		];
	}
	function admin_enqueue()
	{
		Asset::enqueue('citadela-pro-layouts', '/js/layouts.js', ['wp-plupload']);
		Asset::enqueue('citadela-pro-layouts', '/css/layouts.css' );
		Asset::localize('citadela-pro-layouts',  '_citadelaProPluploadOptions', [
			'plupload' => [
				'filters' => [
					'max_file_size' => apply_filters( 'import_upload_size_limit', wp_max_upload_size() ) . 'b',
					'mime_types' => [
						[
							'title' => esc_html__( 'ZIP files', 'citadela-pro' ),
							'extensions' => 'zip',
						],
					],
				],
				'file_data_name' => 'layout',
				'multipart_params' => [
					'action'   => 'citadela-pro-layout-upload',
					'_wpnonce' => wp_create_nonce('citadela-pro-layout-upload')
				]
			]
		] );
	}
	function display()
	{
		wp_plupload_default_settings();
		Template::load('/layouts/upload');
	}
}
