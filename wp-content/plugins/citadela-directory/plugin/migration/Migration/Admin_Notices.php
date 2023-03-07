<?php

namespace Citadela\Directory\Migration;

use Citadela\Directory\Migration;

class Admin_Notices
{

    public static function show()
    {
        if(!empty($_GET['migration-reset']) and Migration::is_in_progress()){
            self::reset_notice();
        }

        if(get_option('citadela_directory_migration_show_done_notice', false)){
            self::migration_done_notice();
        }

        if(Migration::has_something_to_migrate() and Migration::is_in_progress()){
            self::migration_in_progress_notice();
        }
    }



    protected static function reset_notice()
    {
        ?>
        <div class="notice notice-success notice-large">
            <p>
                <strong class="notice-title"><?php esc_html_e('Migration restarted', 'citadela-directory'); ?></strong><br>
                <?php esc_html_e('All automatically migrated items, categories and locations were deleted from your database. New migration process started.', 'citadela-directory'); ?>
            </p>
        </div>
        <?php
    }



    protected static function migration_done_notice()
    {
        ?>
        <div class="notice notice-success notice-large">
            <p>
                <strong class="notice-title"><?php esc_html_e('Migration finished', 'citadela-directory'); ?></strong><br>
                <?php 
                    // translators: %s plugin's name
                    printf(esc_html__('%s has finished migration. All items, categories and locations from your previous AIT directory theme are now available for use with Citadela Listing plugin.', 'citadela-directory' ), '<strong>Citadela Listing plugin</strong>'); ?>
            </p>
        </div>
        <?php
        delete_option('citadela_directory_migration_show_done_notice');
    }



    protected static function migration_in_progress_notice()
    {
        ?>
        <div class="notice notice-info notice-large">
            <p>
                <strong class="notice-title"><?php esc_html_e('Migration in progress', 'citadela-directory'); ?></strong><br>
                <?php 
                    // translators: %s plugin's name
                    printf(esc_html__( '%s is automatically migrating all items, categories and locations from your previous AIT directory theme in the background. Migration process may take several minutes. We will let you know when the migration is finished. You can continue to use WordPress as usual.', 'citadela-directory' ), '<strong>Citadela Listing plugin</strong>'); ?>
            </p>
        </div>
        <?php
    }
}
