<?php

namespace app\Core;

class Event
{
    protected static $listeners = [];

    public static function on($event, callable $callback)
    {
        self::$listeners[$event][] = $callback;
    }

    public static function trigger($event, ...$args)
    {
        if (isset(self::$listeners[$event])) {
            foreach (self::$listeners[$event] as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }
}
