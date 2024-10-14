<?php
namespace bpmj\wpidea\wolverine\event;

class Events
{

    protected static $events = [];

    public static function on($event, EventHandler $callback)
    {
        if (!isset(self::$events[$event])) {
            self::$events[$event] = [];
        }

        self::$events[$event][] = $callback;
    }

    public static function trigger($event, $data = [])
    {
        if (isset(self::$events[$event])) {
            foreach (self::$events[$event] as $callback) {
                $callback->run($data);
            }
        }
    }
}
