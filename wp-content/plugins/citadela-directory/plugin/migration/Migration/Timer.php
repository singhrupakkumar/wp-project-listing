<?php

namespace Citadela\Directory\Migration;

class Timer
{

    protected static $timers = [];



    public static function start($timer)
    {
        self::$timers[$timer] = time();
    }



    public static function end($timer)
    {
        return (time() - self::$timers[$timer]);
    }



    public static function log_end($timer)
    {
        $s = (time() - self::$timers[$timer]);
        \ctdl\log("⏱ $timer: {$s}s");
    }
}
