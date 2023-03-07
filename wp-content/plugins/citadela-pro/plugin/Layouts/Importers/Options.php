<?php

namespace Citadela\Pro\Layouts\Importers;

use Citadela\Pro\Customize;

class Options 
{
	protected static $options;
	protected static $urls;



	static function urls($urls)
	{
		self::$urls = $urls;
	}



	static function import($options)
	{
		self::$options = $options;
		self::theme_mods();
		self::wp_options();
		self::widgets();
		self::plugins();
	}



	protected static function theme_mods()
	{
		// customizer settings from theme
		$old_mods = [
			'citadela_setting_hideTaglineSitetitle',
			'citadela_setting_footerText',
			'citadela_less_files_modified_time',
		];
		foreach (Customize::config('controls') as $section => $settings) {
			$old_mods = array_merge( $old_mods, array_map( function( $mod ) {
				return "citadela_setting_$mod";
			}, array_keys( $settings ) ) );
		}
		// reset previous mods
		foreach ($old_mods as $old_mod) {
			remove_theme_mod( $old_mod );
		}
		self::$urls->remap( self::$options['theme_mods'] );
		foreach (self::$options['theme_mods'] as $mod => $value) {
			set_theme_mod( $mod, $value );
		}
	}



	protected static function wp_options()
	{
		foreach (self::$options['wp_options'] as $key => $value) {
			update_option($key, $value);
		}
	}



	protected static function widgets()
	{
		foreach (self::$options['widgets'] as $widget => $values) {
			self::$urls->remap($values);
			update_option($widget, $values);
		}
		update_option('sidebars_widgets', self::$options['sidebars']);
	}



	protected static function plugins()
	{

		$excluded_options = self::excluded_options();

		foreach (self::$options['plugins']['citadela-directory']['special_pages'] as $option => $id) {
			if (empty($id)) {
				delete_option("citadela_{$option}");
			} else{
				update_option("citadela_{$option}", $id);
			}
		}
		if (!empty(self::$options['plugins']['citadela-pro']['special_pages'])) {
			foreach (self::$options['plugins']['citadela-pro']['special_pages'] as $option => $id) {
				if (empty($id)) {
					delete_option("citadela_{$option}");
				} else {
					update_option("citadela_{$option}", $id);
				}
			}
		}
		foreach (self::$options['plugins']['citadela-directory']['settings'] as $option => $value) {
			update_option($option, $value);
		}
		foreach (self::$options['plugins']['citadela-pro']['settings'] as $option => $value) {
			update_option($option, $value);
		}
		if (!empty(self::$options['plugins']['elementor']['settings'])) {
			foreach (self::$options['plugins']['elementor']['settings'] as $option => $value) {
				update_option($option, $value);
			}
		}
		if (!empty(self::$options['woocommerce'])) {
			foreach (self::$options['woocommerce'] as $option) {
				if( isset( $excluded_options['woocommerce'] ) && ! empty( $excluded_options['woocommerce'] ) ) {
					if( ! in_array($option[1], $excluded_options['woocommerce']) ) {
						update_option($option[1], $option[2]);
					}
				}else{
					update_option($option[1], $option[2]);
				}
			}
		}
	}



	protected static function excluded_options(){
		return [
			'woocommerce' => [
				// options are currently excluded directly in sql query of exporter
				//'woocommerce_task_list_tracked_completed_tasks',
			],
		];
	}
}
