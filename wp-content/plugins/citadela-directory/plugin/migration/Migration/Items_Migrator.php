<?php

namespace Citadela\Directory\Migration;

class Items_Migrator extends Migrator
{

    protected $action = 'citadela_directory_migrator_items';



    public function task($not_migrated_item_ids_chunk)
    {
        \ctdl\log(__METHOD__);
        Timer::start('Items_Migrator::task');

        $repo = Items::get_repo();
        $errors = [];

        $not_migrated_items = Items::get_not_migrated_by_ids($not_migrated_item_ids_chunk);
        $not_migrated_items_meta = Items::get_not_migrated_meta_by_ids($not_migrated_item_ids_chunk);
        $not_migrated_terms_by_item_ids = Terms::get_not_migrated_by_item_ids($not_migrated_item_ids_chunk);

        foreach($not_migrated_items as $not_migrated_item){
            $new_item_id = Items::insert_item($not_migrated_item);

            if(!is_wp_error($new_item_id)){
                $repo[$not_migrated_item->ID] = $new_item_id;

                if(isset($not_migrated_items_meta[$not_migrated_item->ID])){
                    Items::add_meta($new_item_id, $not_migrated_items_meta[$not_migrated_item->ID]);
                }

                if(isset($not_migrated_terms_by_item_ids[$not_migrated_item->ID])){
                    Items::assign_to_terms($new_item_id, $not_migrated_terms_by_item_ids[$not_migrated_item->ID]);
                }

            }else{
                \ctdl\log("Error Items::insert_item #$i", "{$term_id}#{$not_migrated_term['name']}");
                $errors[] = $new_item_id;
            }
        }

        Items::update_repo($repo);

        Timer::log_end('Items_Migrator::task');

        return false;
    }



    public function get_not_migrated_in_chunks()
    {
        \ctdl\log(__METHOD__, 30);
        return Items::get_not_migrated_ids_in_chunks_of(30);
    }



    public static function has_not_migrated()
    {
        return Items::has_not_migrated();
    }



    public function reset()
    {
        Timer::start(__METHOD__);
        Items::reset();
        delete_option("{$this->action}_queueing_done");
        delete_option("{$this->action}_migrating_done");
        Timer::log_end(__METHOD__);
    }
}

