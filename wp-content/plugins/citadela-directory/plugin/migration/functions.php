<?php

namespace ctdl
{
    function log($label, $msg = '')
    {
        error_log(
            sprintf("[%s] %s%s\n", date('j.n.Y H:i:s'), $label, $msg ? ": $msg" : ''),
            3,
            ((is_string(WP_DEBUG_LOG) && realpath(dirname(WP_DEBUG_LOG))) ? dirname(WP_DEBUG_LOG) : WP_CONTENT_DIR) . '/.ht-ctdl-migration.log'
        );
    }
}
