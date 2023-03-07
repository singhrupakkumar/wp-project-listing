<?php

namespace Citadela\Directory;

use Citadela\Directory\Migration\Admin_Notices;
use Citadela\Directory\Migration\Items_Migrator;
use Citadela\Directory\Migration\Terms_Migrator;

class Migration
{

    const DB_VERSION = '1.0';

    protected static $migrator_classes = [
        Terms_Migrator::class,
        Items_Migrator::class,
    ];

    protected static $migrators = [];

    protected static $deactivation = false;



    public static function run()
    {
        if(!is_admin() or self::is_ajax_request()) return;

        require_once __DIR__ . '/functions.php';

        register_activation_hook('citadela-directory/citadela-directory.php', [__CLASS__, 'activation']);
        register_deactivation_hook('citadela-directory/citadela-directory.php', [__CLASS__, 'deactivation']);

        add_action('after_setup_theme', function () {
			if (\Citadela::$allowed) {

                add_action('init', [__CLASS__, 'init_migrators']);
                add_action('admin_init', [__CLASS__, 'admin_init']);
                add_action('admin_notices', [Admin_Notices::class, 'show']);
                add_action('admin_action_citadela-directory-migration-reset', [__CLASS__, 'reset']);

                // 'current_screen' action is not fired in admin-ajax.php
                add_action('current_screen', [__CLASS__, 'run_migrators']);

            }
		}, 100);
    }



    public static function activation()
    {
        add_option('citadela_directory_migration_db_version', self::DB_VERSION);
        update_option('citadela_directory_migration_has_something_to_migrate', self::migrators_have_something_to_migrate());
    }



    public static function deactivation()
    {
        self::$deactivation = true;

        foreach(self::$migrators as $migrator){
            $migrator->clear_incomplete();
        }
        delete_option('citadela_directory_migration_has_something_to_migrate');
    }



    public static function admin_init()
    {
        if(self::is_old_version()){
            update_option('citadela_directory_migration_db_version', self::DB_VERSION);
            update_option('citadela_directory_migration_has_something_to_migrate', self::migrators_have_something_to_migrate());
        }
    }



    public static function run_migrators()
    {
        if(self::$deactivation or !self::has_something_to_migrate()){
            return;
        }

        \ctdl\log(__METHOD__);

        self::$migrators[0]->run();
    }



    public static function init_migrators()
    {
        $migrators = array_map(function($class) { return new $class; }, self::$migrator_classes);

        $c = count($migrators);

        for($i = 0; $i < $c; ++$i){
            if($i + 1 < $c){
                $migrators[$i]->when_done($migrators[$i + 1]);
            }
            if(self::is_old_version()){
                $migrators[$i]->not_done();
            }
        }

        self::$migrators = $migrators;
    }



    public static function reset()
    {
        check_admin_referer('citadela-directory-migration-reset');

        foreach(array_reverse(self::$migrators) as $migrator){
            $migrator->reset();
        }
        update_option('citadela_directory_migration_has_something_to_migrate', self::migrators_have_something_to_migrate());

        return wp_safe_redirect(add_query_arg([['migration-reset' => 1]], wp_get_referer()));
    }



    public static function reset_url()
    {
        return wp_nonce_url(admin_url('admin.php?action=citadela-directory-migration-reset'), 'citadela-directory-migration-reset');
    }



    public static function has_something_to_migrate()
    {
        return (bool) get_option('citadela_directory_migration_has_something_to_migrate', false);
    }



    public static function migrators_have_something_to_migrate()
    {
        foreach(self::$migrator_classes as $class){
            if($class::has_not_migrated()) return 1;
        }
        return 0;
    }



    public static function is_old_version()
    {
        $current_db_version = get_option('citadela_directory_migration_db_version', '1.0');

        return version_compare($current_db_version, self::DB_VERSION, '<');
    }



    public static function is_in_progress()
    {
        foreach(self::$migrators as $migrator){
            if(!$migrator->is_done()) return true;
        }
        return false;
    }



    public static function is_done()
    {
        $done = [];
        foreach(self::$migrators as $migrator){
            $done[] = $migrator->is_done();
        }

        return (count(self::$migrators) === count(array_filter($done)));
    }



    protected static function is_ajax_request()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
}
