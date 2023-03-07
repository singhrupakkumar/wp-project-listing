<?php

namespace Citadela\Directory\Migration;

class Items
{

    const REPO_KEY = 'citadela_directory_migration_items_repo';
    const MIGRATED_META_KEY = '_migrated_to_citadela_directory';



    public static function get_not_migrated_ids_in_chunks_of($size)
    {
        global $wpdb;

        $migrated_meta_key = self::MIGRATED_META_KEY;

        $ids = $wpdb->get_col("
            SELECT
                {$wpdb->posts}.ID
            FROM
                {$wpdb->posts}
            LEFT JOIN {$wpdb->postmeta} ON (
                {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
                AND {$wpdb->postmeta}.meta_key = '{$migrated_meta_key}'
            )
            WHERE
                {$wpdb->postmeta}.post_id IS NULL
                AND {$wpdb->posts}.post_type = 'ait-item'
                AND {$wpdb->posts}.post_status = 'publish'
            GROUP BY
                {$wpdb->posts}.ID
            ORDER BY
                {$wpdb->posts}.ID ASC;
        ");

        return array_chunk($ids, $size);
    }



    public static function get_not_migrated_by_ids($ids)
    {
        global $wpdb;

        $count = count($ids);

        $ids_in = implode(', ', $ids);

        $migrated_meta_key = self::MIGRATED_META_KEY;

        return $wpdb->get_results("
            SELECT
                {$wpdb->posts}.ID,
                {$wpdb->posts}.post_author,
                {$wpdb->posts}.post_date,
                {$wpdb->posts}.post_date_gmt,
                {$wpdb->posts}.post_content,
                {$wpdb->posts}.post_title,
                {$wpdb->posts}.post_excerpt,
                {$wpdb->posts}.post_status,
                {$wpdb->posts}.comment_status,
                {$wpdb->posts}.ping_status,
                {$wpdb->posts}.post_password,
                {$wpdb->posts}.post_name,
                {$wpdb->posts}.to_ping,
                {$wpdb->posts}.pinged,
                {$wpdb->posts}.post_modified,
                {$wpdb->posts}.post_modified_gmt,
                {$wpdb->posts}.post_content_filtered,
                {$wpdb->posts}.post_parent,
                {$wpdb->posts}.menu_order,
                {$wpdb->posts}.post_mime_type,
                {$wpdb->posts}.comment_count
            FROM
                {$wpdb->posts}
            LEFT JOIN {$wpdb->postmeta} ON (
                {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND
                {$wpdb->postmeta}.meta_key = '{$migrated_meta_key}'
            )
            WHERE {$wpdb->posts}.ID IN ($ids_in)
            ORDER BY {$wpdb->posts}.ID
            LIMIT 0, $count;
        ");
    }



    public static function get_not_migrated_meta_by_ids($ids)
    {
        global $wpdb;

        $in = implode(', ', $ids);

        $rows = $wpdb->get_results("
            SELECT
                {$wpdb->postmeta}.post_id,
                {$wpdb->postmeta}.meta_key,
                {$wpdb->postmeta}.meta_value
            FROM
                {$wpdb->postmeta}
            WHERE
                {$wpdb->postmeta}.post_id IN ($in)
            ORDER BY meta_id ASC;
        ");

        $meta = [];
        foreach($rows as $row){
            $meta[$row->post_id][$row->meta_key] = $row->meta_value;
        }

        unset($rows);

        return $meta;
    }



    public static function has_not_migrated()
    {
        global $wpdb;

        $migrated_meta_key = self::MIGRATED_META_KEY;

        return (bool) $wpdb->get_var("
            SELECT
                count({$wpdb->posts}.ID)
            FROM
                {$wpdb->posts}
            LEFT JOIN {$wpdb->postmeta} ON (
                {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
                AND {$wpdb->postmeta}.meta_key = '{$migrated_meta_key}'
            )
            WHERE
                {$wpdb->postmeta}.post_id IS NULL
                AND {$wpdb->posts}.post_type = 'ait-item'
                AND {$wpdb->posts}.post_status = 'publish'
            GROUP BY
                {$wpdb->posts}.ID
            LIMIT 1;
        ");
    }



    public static function insert_item($item_obj)
    {
        global $wpdb;

        $item = (array) $item_obj;

        unset($item['ID']);

        $item['post_type'] = 'citadela-item';

        foreach(['post_title', 'post_content', 'post_excerpt'] as $emoji_field){
            if(isset($item[$emoji_field])){
                $charset = $wpdb->get_col_charset($wpdb->posts, $emoji_field);
                if('utf8' === $charset){
                    $item[$emoji_field] = wp_encode_emoji($item[$emoji_field]);
                }
            }
        }

        if(false === $wpdb->insert($wpdb->posts, $item)){
            return new WP_Error( 'db_insert_error', esc_html__( 'Could not insert post into the database' ), $wpdb->last_error );
        }

        $new_item_id = (int) $wpdb->insert_id;

        $wpdb->insert($wpdb->postmeta, ['post_id' => $item_obj->ID, 'meta_key' => self::MIGRATED_META_KEY, 'meta_value' => maybe_serialize(['when' => current_time('mysql'), 'new_item_id' => $new_item_id])]);

        return $new_item_id;;
    }



    public static function add_meta($new_item_id, $not_migrated_meta)
    {
        global $wpdb;

        $sql_multi_insert = "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES ";

        $directory_meta = maybe_unserialize($not_migrated_meta['_ait-item_item-data']);

        $sql_values = [];

        foreach([
            '_citadela_subtitle'                  => isset($directory_meta['subtitle']) ? $directory_meta['subtitle'] : '',
            '_citadela_featured'                  => isset($directory_meta['featuredItem']) ? self::falsy_to_empty_string($directory_meta['featuredItem']) : '',
            '_citadela_address'                   => isset($directory_meta['map']['address']) ? $directory_meta['map']['address'] : '',
            '_citadela_latitude'                  => isset($directory_meta['map']['latitude']) ? $directory_meta['map']['latitude'] : '',
            '_citadela_longitude'                 => isset($directory_meta['map']['longitude']) ? $directory_meta['map']['longitude'] : '',
            '_citadela_streetview'                => isset($directory_meta['map']['streetview']) ? self::falsy_to_empty_string($directory_meta['map']['streetview']) : '',
            '_citadela_swheading'                 => isset($directory_meta['map']['swheading']) ? $directory_meta['map']['swheading'] : '',
            '_citadela_swpitch'                   => isset($directory_meta['map']['swpitch']) ? $directory_meta['map']['swpitch'] : '',
            '_citadela_swzoom'                    => isset($directory_meta['map']['swzoom']) ? $directory_meta['map']['swzoom'] : '',
            '_citadela_telephone'                 => isset($directory_meta['telephone']) ? $directory_meta['telephone'] : '',
            '_citadela_email'                     => isset($directory_meta['email']) ? $directory_meta['email'] : '',
            '_citadela_show_email'                => isset($directory_meta['showEmail']) ? self::falsy_to_empty_string($directory_meta['showEmail']) : '',
            '_citadela_use_contact_form'          => isset($directory_meta['contactOwnerBtn']) ? self::falsy_to_empty_string($directory_meta['contactOwnerBtn']) : '',
            '_citadela_web_url'                   => isset($directory_meta['web']) ? self::fix_url($directory_meta['web']) : '',
            '_citadela_web_url_label'             => isset($directory_meta['webLinkLabel']) ? $directory_meta['webLinkLabel'] : '',
            '_citadela_show_opening_hours'        => isset($directory_meta['displayOpeningHours']) ? self::falsy_to_empty_string($directory_meta['displayOpeningHours']) : '',
            '_citadela_opening_hours_monday'      => isset($directory_meta['openingHoursMonday']) ? $directory_meta['openingHoursMonday'] : '',
            '_citadela_opening_hours_tuesday'     => isset($directory_meta['openingHoursTuesday']) ? $directory_meta['openingHoursTuesday'] : '',
            '_citadela_opening_hours_wednesday'   => isset($directory_meta['openingHoursWednesday']) ? $directory_meta['openingHoursWednesday'] : '',
            '_citadela_opening_hours_thursday'    => isset($directory_meta['openingHoursThursday']) ? $directory_meta['openingHoursThursday'] : '',
            '_citadela_opening_hours_friday'      => isset($directory_meta['openingHoursFriday']) ? $directory_meta['openingHoursFriday'] : '',
            '_citadela_opening_hours_saturday'    => isset($directory_meta['openingHoursSaturday']) ? $directory_meta['openingHoursSaturday'] : '',
            '_citadela_opening_hours_sunday'      => isset($directory_meta['openingHoursSaturday']) ? $directory_meta['openingHoursSaturday'] : '',
            '_citadela_opening_hours_note'        => isset($directory_meta['openingHoursNote']) ? $directory_meta['openingHoursNote'] : '',
            '_yoast_wpseo_focuskw' => $not_migrated_meta['_yoast_wpseo_focuskw'],
            '_yoast_wpseo_metadesc' => $not_migrated_meta['_yoast_wpseo_metadesc'],
            '_yoast_wpseo_linkdex' => $not_migrated_meta['_yoast_wpseo_linkdex'],
            '_yoast_wpseo_content_score' => $not_migrated_meta['_yoast_wpseo_content_score'],
            '_yoast_wpseo_estimated-reading-time-minutes' => $not_migrated_meta['_yoast_wpseo_estimated-reading-time-minutes'],
            '_yoast_wpseo_title' => $not_migrated_meta['_yoast_wpseo_title'],
            '_yoast_wpseo_is_cornerstone' => $not_migrated_meta['_yoast_wpseo_is_cornerstone'],
            '_yoast_wpseo_meta-robots-noindex' => $not_migrated_meta['_yoast_wpseo_meta-robots-noindex'],
            '_yoast_wpseo_meta-robots-nofollow' => $not_migrated_meta['_yoast_wpseo_meta-robots-nofollow'],
            '_yoast_wpseo_meta-robots-adv' => $not_migrated_meta['_yoast_wpseo_meta-robots-adv'],
            '_yoast_wpseo_opengraph-title' => $not_migrated_meta['_yoast_wpseo_opengraph-title'],
            '_yoast_wpseo_opengraph-description' => $not_migrated_meta['_yoast_wpseo_opengraph-description'],
            '_yoast_wpseo_opengraph-image' => $not_migrated_meta['_yoast_wpseo_opengraph-image'],
            '_yoast_wpseo_opengraph-image-id' => $not_migrated_meta['_yoast_wpseo_opengraph-image-id'],
            '_yoast_wpseo_twitter-title' => $not_migrated_meta['_yoast_wpseo_twitter-title'],
            '_yoast_wpseo_twitter-description' => $not_migrated_meta['_yoast_wpseo_twitter-description'],
            '_yoast_wpseo_twitter-image' => $not_migrated_meta['_yoast_wpseo_twitter-image'],
            '_yoast_wpseo_twitter-image-id' => $not_migrated_meta['_yoast_wpseo_twitter-image-id'],
            '_yoast_wpseo_schema_page_type' => $not_migrated_meta['_yoast_wpseo_schema_page_type'],
            '_yoast_wpseo_schema_article_type' => $not_migrated_meta['_yoast_wpseo_schema_article_type']
        ] as $meta_key => $meta_value){
            if(!empty($meta_value)){
                $sql_values[] = $wpdb->prepare("(%d, %s, %s)", $new_item_id, $meta_key, maybe_serialize($meta_value));
            }
        }

        if(!empty($sql_values)){
            $wpdb->query($sql_multi_insert . implode(',', $sql_values));
        }

        $sql_values = [];

        foreach([
            '_edit_last'    => isset($not_migrated_meta['_edit_last']) ? $not_migrated_meta['_edit_last'] : null,
            '_edit_lock'    => isset($not_migrated_meta['_edit_lock']) ? $not_migrated_meta['_edit_lock'] : null,
            '_thumbnail_id' => isset($not_migrated_meta['_thumbnail_id']) ? $not_migrated_meta['_thumbnail_id'] : null,
        ] as $meta_key => $meta_value){
            if(!empty($meta_value)){
                $sql_values[] = $wpdb->prepare("(%d, %s, %s)", $new_item_id, $meta_key, maybe_serialize($meta_value));
            }
        }
        if(!empty($sql_values)){
            $wpdb->query($sql_multi_insert . implode(',', $sql_values));
        }
    }



    public static function assign_to_terms($new_item_id, $not_migrated_terms)
    {
        global $wpdb;

        $terms_repo = Terms::get_repo();

        $sql_values = [];
        $new_tt_ids = [];

        foreach($not_migrated_terms as $term){
            $new_term_taxonomy_id = $terms_repo[$term->taxonomy][$term->term_id]['term_taxonomy_id'];

            $new_tt_ids[Terms::tax_map($term->taxonomy)][] = $new_term_taxonomy_id;

            $sql_values[] = $wpdb->prepare('(%d, %d)', $new_item_id, $new_term_taxonomy_id);
        }

        if(!empty($sql_values)){
            $wpdb->query("INSERT INTO {$wpdb->term_relationships} (object_id, term_taxonomy_id) VALUES " . join(',', $sql_values));
        }

        DB_Transaction::start();

        foreach($new_tt_ids as $new_taxonomy => $tt_ids){
            if(!empty($tt_ids)){
                foreach((array) $tt_ids as $tt_id){
                    $count = $wpdb->get_var("SELECT COUNT(term_taxonomy_id) FROM {$wpdb->term_relationships} WHERE term_taxonomy_id = {$tt_id}");
                    $wpdb->update($wpdb->term_taxonomy, ['count' => $count], ['term_taxonomy_id' => $tt_id]);
                }
            }
        }

        DB_Transaction::commit();
    }



    public static function get_repo()
    {
        return get_option(self::REPO_KEY, []);
    }



    public static function update_repo($value)
    {
        return update_option(self::REPO_KEY, $value, 'no');
    }



    public static function reset()
    {
        global $wpdb;

        $implode = function($v){ return implode(', ', $v); };

        $migrated_chunks = array_map($implode, array_chunk(array_values(self::get_repo()), 250));

        foreach($migrated_chunks as $ids){
            $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id IN ($ids)");
            $wpdb->query("DELETE FROM {$wpdb->posts} WHERE ID IN ($ids)");
        }

        $not_migrated_chunks = array_map($implode, array_chunk(array_keys(self::get_repo()), 250));

        foreach($not_migrated_chunks as $ids){
            $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id IN ($ids)");
        }

        delete_option(self::REPO_KEY);
    }



    protected static function falsy_to_empty_string($value)
    {
        // conversion for butterbean checkboxes
        if(
            (is_string($value) and (strtolower($value) === 'off' or strtolower($value) === 'false'))
            or !$value
        ){
            return '';
        }
        return $value;
    }



    protected static function fix_url($url)
    {
        $url = ltrim(trim($url), '/');

        if(empty($url)){
            return '';
        }

        $url = str_replace('http:///', 'http://', $url);

        if(self::is_valid_url($url) and !self::starts_with($url, ['https://', 'http://'])){
            return "http://$url";
        }

        return $url;
    }



    protected static function is_valid_url($url)
    {
        $alpha = "a-z\x80-\xFF";

        return preg_match( "(^
                    (https://|http://)?(
                        (([-_0-9$alpha]+\\.)*                       # subdomain
                            [0-9$alpha]([-0-9$alpha]{0,61}[0-9$alpha])?\\.)  # domain
                            [$alpha]([-0-9$alpha]{0,17}[$alpha])   # top domain
                        |\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}  # IPv4
                    )(:\\d{1,5})?                                   # port
                    (/\\S*)?                                        # path
                \\z)ix", $url) === 1;
    }



    protected static function starts_with($haystack, $needles)
    {
        foreach((array) $needles as $needle){
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }
}
