<?php

namespace Citadela\Directory\Migration;

class DB_Transaction
{

    public static function start()
    {
        global $wpdb;
        $wpdb->hide_errors();
        $wpdb->query('START TRANSACTION');
    }



    public static function commit()
    {
        global $wpdb;
        $wpdb->hide_errors();
        $wpdb->query('COMMIT');
        $wpdb->show_errors();
    }
}
