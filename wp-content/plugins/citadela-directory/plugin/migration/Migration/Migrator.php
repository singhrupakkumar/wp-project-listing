<?php

namespace Citadela\Directory\Migration;

class Migrator extends Background_Process
{

    protected $cron_interval = 2;

    protected $next_migrator;


    public function __construct()
    {
        $this->prefix = 'wp_' . get_current_blog_id();

        $this->post_args = [
            'timeout'   => 0.01,
            'blocking'  => false,
            'body'      => [],
            'cookies'   => $_COOKIE,
            'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
        ];

        parent::__construct();
    }



    public function run()
    {
        $chunks = [];

        if($this->is_done()) return;

        if(!$this->is_queueing_done()){
            \ctdl\log(get_class($this), 'run');

            $chunks = $this->get_not_migrated_in_chunks();

            foreach($chunks as $chunk){
                $this->push_to_queue($chunk);
            }
            $this->save();

            $this->queueing_done();
        }

        if($this->is_queueing_done() and $this->is_done() and empty($chunks)){
            \ctdl\log(get_class($this), 'empty chunks');
            $this->run_next_migrator();
            return;
        }

        if($this->is_queueing_done() and !$this->is_done() and !$this->is_migrating()){
            \ctdl\log(get_class($this), 'dispatch');
            $this->dispatch();
        }
    }



    public function maybe_handle()
    {
        session_write_close();

        if($this->is_process_running()){
            wp_die();
        }

        if($this->is_queue_empty()){
            \ctdl\log('maybe_handle', 'queue_empty -> run_next_migrator');
            $this->run_next_migrator();
            wp_die();
        }

        check_ajax_referer( $this->identifier, 'nonce' );

        $this->handle();

        wp_die();
    }



    protected function complete()
    {
        \ctdl\log(get_class($this), 'complete');

        parent::complete();
        $this->done();
        $this->run_next_migrator();

        if($this->is_done() and !$this->next_migrator){
            // all migrations done
            add_option('citadela_directory_migration_show_done_notice', 1);
            delete_option('citadela_directory_migration_has_something_to_migrate');
        }
    }



    public function run_next_migrator()
    {
        if($this->next_migrator){
            \ctdl\log(get_class($this), 'run_next_migrator');
            $this->next_migrator->run();
        }
    }



    public function when_done($next_migrator = null)
    {
        $this->next_migrator = $next_migrator;
        return $this;
    }



    public function is_migrating()
    {
        return $this->is_process_running();
    }



    protected function done()
    {
        \ctdl\log(get_class($this), 'done');
        add_option("{$this->action}_migrating_done", 1);
    }



    public function is_done()
    {
        return (bool) get_option("{$this->action}_migrating_done", false);
    }



    public function queueing_done()
    {
        add_option("{$this->action}_queueing_done", 1);
    }



    public function is_queueing_done()
    {
        return (bool) get_option("{$this->action}_queueing_done", false);
    }



    public function not_done()
    {
        delete_option("{$this->action}_queueing_done");
        delete_option("{$this->action}_migrating_done");
    }



    public function clear_incomplete()
    {
        $this->unlock_process();
        $this->clear_scheduled_event();
        $this->not_done();
        $this->clear_batches();
    }



    protected function clear_batches()
    {
        global $wpdb;

        $table        = $wpdb->options;
        $column       = 'option_name';
        $key_column   = 'option_id';
        $value_column = 'option_value';

        if(is_multisite()){
            $table        = $wpdb->sitemeta;
            $column       = 'meta_key';
            $key_column   = 'meta_id';
            $value_column = 'meta_value';
        }

        $key = $wpdb->esc_like($this->identifier . '_batch_') . '%';

        $ids = $wpdb->get_col($wpdb->prepare("SELECT {$key_column} FROM {$table} WHERE {$column} LIKE %s ORDER BY {$key_column} ASC", $key));

        if(!empty($ids)){
            \ctdl\log(get_class($this), 'clear_batches');
            $in = implode(',', $ids);
            $wpdb->query("DELETE FROM {$table} WHERE {$key_column} IN ($in)");
        }
    }



    public function task($item){ }
}
