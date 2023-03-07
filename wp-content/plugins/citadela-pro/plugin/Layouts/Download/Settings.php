<?php

namespace Citadela\Pro\Layouts\Download;

use Citadela\Pro\Asset;
use Citadela\Pro\Template;
use Citadela\Pro\Less_Compiler;
use Citadela\Pro\Layouts\Importer;


class Settings {
	function __construct()
	{
		Importer::instance()->register();
		add_action('wp_ajax_citadela-pro-layout-download', function() {
			$response = [
				'requirements' => null,
				'urls' => [
					'requirements' => add_query_arg(['action' => 'citadela-pro-layout-install-requirements'], admin_url('admin-ajax.php')),
					'content' => add_query_arg(['action' => 'citadela-pro-layout-content-import'], admin_url('admin-ajax.php')),
					'options' => add_query_arg(['action' => 'citadela-pro-layout-options-import'], admin_url('admin-ajax.php')),
					'images' => add_query_arg(['action' => 'citadela-pro-layout-images-import'], admin_url('admin-ajax.php')),
					'complete' => add_query_arg(['action' => 'citadela-pro-layout-import-complete'], admin_url('admin-ajax.php')),
					'cancel' => add_query_arg(['action' => 'citadela-pro-layout-import-cancel'], admin_url('admin-ajax.php'))
				]
			];
			try {
				$path = \Citadela::downloadProduct($_GET['layout']);
			} catch (\Exception $exception) {
				wp_send_json_error(isset($exception->response) ? \Citadela::getResponseMessage($exception->response) : ['title' => __('Error', 'citadela-pro'), 'message' => __('Tehre was an error with downloading layout', 'citadela-pro')]);
			}
			$response['requirements'] = Importer::instance()->check_requirements($path);
			set_transient('citadela-pro-layout-package', $path);
			\ctdl\pro\log(__METHOD__);
			wp_send_json_success($response);
		});
	}
	function slug()
	{
		return 'layouts';
	}
	function tab()
	{
		return [
			'label' => __('Layouts', 'citadela-pro')
		];
	}
	function display()
	{
		Template::load('/layouts/download', [
			'url' => \Citadela::$url,
			'layouts' => json_decode(wp_remote_get(\Citadela::$url . '/core/products?parameters={"where":{"citadela_layout":1,"coming_soon":0}}')['body'], true)
		]);
	}
	function admin_enqueue()
	{
		Asset::enqueue('citadela-pro-layouts', '/js/layouts.js', ['wp-plupload']);
		Asset::enqueue('citadela-pro-layouts', '/css/layouts.css');
	}
}