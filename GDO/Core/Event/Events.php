<?php
namespace GDO\Core\Event;

final class Events
{
    private static $EVENTS = [];
    
    private static function &getSubscribers($eventName)
    {
        if (!isset(self::$EVENTS[$eventName]))
        {
            self::$EVENTS[$eventName] = [];
        }
        return self::$EVENTS[$eventName];
    }
    
    public static function subscribe($eventName, $callable)
    {
        $subscribers = self::getSubscribers($eventName);
        $subscribers[] = $callable;
    }
}
