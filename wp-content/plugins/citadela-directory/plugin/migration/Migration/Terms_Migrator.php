<?php

namespace Citadela\Directory\Migration;

class Terms_Migrator extends Migrator
{

    protected $action = 'citadela_directory_migrator_terms';



    public function task($not_migrated_term_ids_chunk)
    {
        \ctdl\log(__METHOD__);
        Timer::start('Terms_Migrator::task');

        $repo = Terms::get_repo();
        $errors = [];

        $not_migrated_terms = Terms::get_not_migrated_by_ids($not_migrated_term_ids_chunk);

        DB_Transaction::start();

        foreach($not_migrated_terms as $not_migrated_term){

            $tax = $not_migrated_term['taxonomy'];
            $term_id = $not_migrated_term['term_id'];

            if(!empty($repo[$tax][$term_id])) continue;

            $new_parent = 0;
            if($not_migrated_term['parent'] > 0 and isset($repo[$tax][$not_migrated_term['parent']]['term_id'])){
                $new_parent = $repo[$tax][$not_migrated_term['parent']]['term_id'];
            }

            $result = Terms::insert_term($not_migrated_term, $new_parent, $tax);

            if(!is_wp_error($result)){
                $repo[$tax][$term_id] = $result;

                if(($meta = Terms::get_meta_from_option($not_migrated_term))){
                    Terms::add_meta($result, $not_migrated_term, $meta);
                }

            }else{
                \ctdl\log("Error Terms::insert_term #$i", "{$term_id}#{$not_migrated_term['name']}");
                $errors[] = $result;
            }
        }

        DB_Transaction::commit();

        \ctdl\log('Terms::unautoload_old_meta');
        Terms::unautoload_old_meta($not_migrated_terms);

        Terms::update_repo($repo);

        Timer::log_end('Terms_Migrator::task');
        return false;
    }



    protected function complete()
    {
        \ctdl\log('Terms::delete_old_meta_orphans');
        Terms::delete_old_meta_orphans();
        parent::complete();
    }



    public function get_not_migrated_in_chunks()
    {
        \ctdl\log(__METHOD__, 100);
        return Terms::get_not_migrated_ids_in_chunks_of(100);
    }



    public static function has_not_migrated()
    {
        return Terms::has_not_migrated();
    }



    public function reset()
    {
        Timer::start(__METHOD__);
        Terms::reset();
        delete_option("{$this->action}_queueing_done");
        delete_option("{$this->action}_migrating_done");
        Timer::log_end(__METHOD__);
    }
}
