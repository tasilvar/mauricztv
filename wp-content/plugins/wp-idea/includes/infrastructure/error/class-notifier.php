<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\infrastructure\error;

class Notifier
{

    private static $handler;

    public static function set_handler(Interface_Handler $handler): void
    {
        self::$handler = $handler;
    }

    public static function notify(string $message): void
    {
        if (!self::$handler) {
            return;
        }
        self::$handler->notify(new \Exception($message));
    }
}
