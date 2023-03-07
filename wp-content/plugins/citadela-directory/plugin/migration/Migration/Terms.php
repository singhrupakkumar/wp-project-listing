<?php

namespace Citadela\Directory\Migration;

class Terms
{

    const REPO_KEY = 'citadela_directory_migration_terms_repo';
    const MIGRATED_META_KEY = '_migrated_to_citadela_directory';



    public static function get_not_migrated_ids_in_chunks_of($size)
    {
        global $wpdb;

        $migrated_meta_key = self::MIGRATED_META_KEY;
        $taxonomies = implode( "', '", esc_sql(array_keys(self::tax_map())));

        $ids = $wpdb->get_col("
            SELECT
                DISTINCT t.term_id
            FROM
                {$wpdb->terms} AS t
            LEFT JOIN
                {$wpdb->termmeta} ON (t.term_id = {$wpdb->termmeta}.term_id AND {$wpdb->termmeta}.meta_key = '{$migrated_meta_key}')
            INNER JOIN
                {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
            WHERE
                tt.taxonomy IN ('{$taxonomies}') AND
                ({$wpdb->termmeta}.term_id IS NULL)
            ORDER BY
                t.term_id ASC
        ");

        return array_chunk($ids, $size);
    }



    public static function get_not_migrated_by_ids($ids)
    {
        global $wpdb;

        $taxonomies = implode( "', '", esc_sql(array_keys(self::tax_map())));
        $migrated_meta_key = self::MIGRATED_META_KEY;
        $ids_in = implode(', ', $ids);

        $terms = $wpdb->get_results("
            SELECT
                DISTINCT t.term_id, t.name, t.slug, tt.taxonomy, tt.description, tt.parent
            FROM {$wpdb->terms} AS t
            LEFT JOIN {$wpdb->termmeta} ON (
                t.term_id = {$wpdb->termmeta}.term_id AND {$wpdb->termmeta}.meta_key = '{$migrated_meta_key}'
            )
            INNER JOIN {$wpdb->term_taxonomy} AS tt ON (
                t.term_id = tt.term_id
            )
            WHERE
                tt.taxonomy IN ('{$taxonomies}')
                AND t.term_id IN ($ids_in)
                AND ({$wpdb->termmeta}.term_id IS NULL)
            ORDER BY
                t.term_id ASC;
        ", ARRAY_A);

        return $terms;
    }



    public static function get_not_migrated_by_item_ids($item_ids)
    {
        global $wpdb;

        $taxonomies = implode( "', '", esc_sql(array_keys(self::tax_map())));
        $migrated_meta_key = self::MIGRATED_META_KEY;
        $ids_in = implode(', ', $item_ids);

        $terms = $wpdb->get_results("
            SELECT
                DISTINCT t.term_id, tt.taxonomy, tt.term_taxonomy_id, tr.object_id
            FROM
                {$wpdb->terms} AS t
            INNER JOIN {$wpdb->termmeta} ON
                t.term_id = {$wpdb->termmeta}.term_id
            INNER JOIN {$wpdb->term_taxonomy} AS tt ON
                t.term_id = tt.term_id
            INNER JOIN {$wpdb->term_relationships} AS tr ON
                tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE
                tt.taxonomy IN ('{$taxonomies}') AND
                tr.object_id IN ($ids_in) AND
                {$wpdb->termmeta}.meta_key = '$migrated_meta_key'
            ORDER BY
                t.term_id ASC
            "
        );

        $return = [];

        foreach($terms as $term){
            $return[$term->object_id][] = (object) [
                'term_id' => $term->term_id,
                'tt_id' => $term->term_taxonomy_id,
                'taxonomy' => $term->taxonomy,
            ];
        }

        unset($terms);

        return $return;
    }



    public static function has_not_migrated()
    {
        global $wpdb;

        $migrated_meta_key = self::MIGRATED_META_KEY;
        $taxonomies = implode( "', '", esc_sql(array_keys(self::tax_map())));

        return (bool) $wpdb->get_var("
            SELECT
                count(DISTINCT t.term_id)
            FROM
                {$wpdb->terms} AS t
            LEFT JOIN
                {$wpdb->termmeta} ON (t.term_id = {$wpdb->termmeta}.term_id AND {$wpdb->termmeta}.meta_key = '{$migrated_meta_key}')
            INNER JOIN
                {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
            WHERE
                tt.taxonomy IN ('{$taxonomies}') AND
                ({$wpdb->termmeta}.term_id IS NULL)
            LIMIT 1;
        ");
    }



    public static function tax_map($old_tax = null)
    {
        $tax_map = [
            'ait-items' => 'citadela-item-category',
            'ait-locations' => 'citadela-item-location',
        ];

        if($old_tax){
            return $tax_map[$old_tax];
        }
        return $tax_map;
    }



    public static function insert_term($not_migrated_term, $new_parent, $taxonomy)
    {
        global $wpdb;

        $new_term = [
            'name' => $not_migrated_term['name'],
            'slug' => $not_migrated_term['slug'],
            'term_group' => 0,
        ];

        if($wpdb->insert($wpdb->terms, $new_term) === false){
            return new WP_Error( 'db_insert_error', '', $wpdb->last_error);
        }

        $new_term_id = (int) $wpdb->insert_id;

        $wpdb->insert($wpdb->term_taxonomy, [
            'term_id'     => $new_term_id,
            'taxonomy'    => self::tax_map($taxonomy),
            'description' => $not_migrated_term['description'],
            'parent'      => $new_parent,
            'count'       => 0,
        ]);

        $new_tt_id = (int) $wpdb->insert_id;

        $wpdb->insert($wpdb->termmeta, ['term_id' => $not_migrated_term['term_id'], 'meta_key' => self::MIGRATED_META_KEY, 'meta_value' => maybe_serialize(['when' => current_time('mysql'), 'new_term_id' => $new_term_id])]);

        return [
            'term_id'          => $new_term_id,
            'term_taxonomy_id' => $new_tt_id,
        ];
    }



    public static function add_meta($new_term, $not_migrated_term, $meta)
    {
        global $wpdb;

        $new_taxonomy = self::tax_map($not_migrated_term['taxonomy']);
        $new_term_id = $new_term['term_id'];

        $new_meta = [];

        if($new_taxonomy === 'citadela-item-category'){
            $new_meta = [
                'category_icon' => 'fas fa-circle',
                'category_color' => !empty($meta['icon_color']) ? $meta['icon_color'] : '#0085ba',
            ];
        }

        if(!empty($new_meta)){
            $wpdb->insert($wpdb->termmeta, ['term_id' => $new_term_id, 'meta_key' => "{$new_taxonomy}-meta", 'meta_value' => maybe_serialize($new_meta)]);
        }
    }



    public static function get_meta_from_option($not_migrated_term)
    {
        return get_option("{$not_migrated_term['taxonomy']}_category_{$not_migrated_term['term_id']}");
    }



    public static function unautoload_old_meta($not_migrated_terms)
    {
        global $wpdb;

        $option_names = [];

        foreach($not_migrated_terms as $v){
            $option_names[] = "{$v['taxonomy']}_category_{$v['term_id']}";
        }

        $in = implode("', '", $option_names);

        $wpdb->query("UPDATE {$wpdb->options} SET autoload='no' WHERE option_name IN ('$in')");
    }



    public static function delete_old_meta_orphans()
    {
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE (option_name LIKE 'ait-items\_category\_%' OR option_name LIKE 'ait-locations\_category\_%') AND autoload='yes'");
    }



    public static function get_repo()
    {
        return get_option(self::REPO_KEY, []);
    }



    public static function update_repo($value)
    {
        update_option(self::REPO_KEY, $value, 'no');
    }



    public static function reset()
    {
        global $wpdb;

        $chunks = ['not_migrated_term_id' => [], 'migrated_term_id' => [], 'migrated_term_taxonomy_id' => []];

        $implode = function($v){ return implode(', ', $v); };

        foreach(self::get_repo() as $tax => $values){
            $chunks['not_migrated_term_id'] = array_merge($chunks['not_migrated_term_id'], array_map($implode, array_chunk(array_keys($values), 250)));
            $chunks['migrated_term_id'] = array_merge($chunks['migrated_term_id'], array_map($implode, array_chunk(wp_list_pluck($values, 'term_id'), 250)));
            $chunks['migrated_term_taxonomy_id'] = array_merge($chunks['migrated_term_taxonomy_id'], array_map($implode, array_chunk(wp_list_pluck($values, 'term_taxonomy_id'), 250)));
        }

        foreach($chunks['not_migrated_term_id'] as $ids){
            $wpdb->query("DELETE FROM {$wpdb->termmeta} WHERE term_id IN ($ids)");
        }

        foreach($chunks['migrated_term_id'] as $ids){
            $wpdb->query("DELETE FROM {$wpdb->term_taxonomy} WHERE term_id IN ($ids)");
            $wpdb->query("DELETE FROM {$wpdb->termmeta} WHERE term_id IN ($ids)");
            $wpdb->query("DELETE FROM {$wpdb->terms} WHERE term_id IN ($ids)");
        }

        foreach($chunks['migrated_term_taxonomy_id'] as $ids){
            $wpdb->query("DELETE FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ($ids)");
        }

        delete_option(self::REPO_KEY);
    }
}
