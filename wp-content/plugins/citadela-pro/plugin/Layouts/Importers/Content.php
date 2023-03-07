<?php

namespace Citadela\Pro\Layouts\Importers;

class Content
{
	protected static $urls;



	static function urls($urls)
	{
		self::$urls = $urls;
	}



	static function import($table, $rows)
	{
		global $wpdb;
		$name = isset($wpdb->$table) ? $wpdb->$table : $wpdb->prefix . $table;
		$wpdb->query("TRUNCATE TABLE {$name}");
		self::{$table}($rows);
	}



	protected static function posts($rows)
	{
		global $wpdb;
		foreach ($rows as $row) {
			self::$urls->remap($row);
			$row['post_author'] = get_current_user_id();
			$wpdb->insert($wpdb->posts, $row);
			$wpdb->update($wpdb->posts, ['guid' => get_permalink((object) $row)], ['ID' => $row['ID']]);
		}
		\ctdl\pro\log(__METHOD__);
	}



	protected static function postmeta($rows)
	{
		global $wpdb;
		foreach ($rows as $row) {
			self::$urls->remap($row);
			$wpdb->insert($wpdb->postmeta, $row);
		}
		\ctdl\pro\log(__METHOD__);
	}



	protected static function terms($rows)
	{
		global $wpdb;
		foreach ($rows as $row) {
			$wpdb->insert($wpdb->terms, $row);
		}
		\ctdl\pro\log(__METHOD__);
	}



	protected static function termmeta($rows)
	{
		global $wpdb;
		foreach ($rows as $row) {
			$wpdb->insert($wpdb->termmeta, $row);
		}
		\ctdl\pro\log(__METHOD__);
	}



	protected static function term_taxonomy($rows)
	{
		global $wpdb;
		foreach ($rows as $row) {
			$wpdb->insert($wpdb->term_taxonomy, $row);
		}
		\ctdl\pro\log(__METHOD__);
	}



	protected static function term_relationships($rows)
	{
		global $wpdb;
		foreach ($rows as $row) {
			$wpdb->insert($wpdb->term_relationships, $row);
		}
		\ctdl\pro\log(__METHOD__);
	}



	protected static function comments($rows)
	{
		global $wpdb;
		foreach ($rows as $row) {
			$wpdb->insert($wpdb->comments, $row);
		}
		\ctdl\pro\log(__METHOD__);
	}



	protected static function commentmeta($rows)
	{
		global $wpdb;
		foreach ($rows as $row) {
			$wpdb->insert($wpdb->commentmeta, $row);
		}
		\ctdl\pro\log(__METHOD__);
	}



	protected static function wc_product_meta_lookup($rows)
	{
		global $wpdb;
		foreach ($rows as $row) {
			$wpdb->insert($wpdb->prefix . 'wc_product_meta_lookup', $row);
		}
		\ctdl\pro\log(__METHOD__);
	}



	protected static function wc_category_lookup($rows)
	{
		global $wpdb;
		foreach ($rows as $row) {
			$wpdb->insert($wpdb->prefix . 'wc_category_lookup', $row);
		}
		\ctdl\pro\log(__METHOD__);
	}



	protected static function woocommerce_attribute_taxonomies($rows)
	{
		global $wpdb;
		foreach ($rows as $row) {
			$wpdb->insert($wpdb->prefix . 'woocommerce_attribute_taxonomies', $row);
		}
		delete_transient('wc_attribute_taxonomies');
		\ctdl\pro\log(__METHOD__);
	}

	static function post_import()
	{
		flush_rewrite_rules();
		delete_option('citadela-item-category_children');
		delete_option('citadela-item-location_children');
		delete_option('citadela-post-location_children');
		delete_option('category_children');
		if (class_exists('Elementor\Plugin')) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
		\ctdl\pro\log(__METHOD__);
	}
}
