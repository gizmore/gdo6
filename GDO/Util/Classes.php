<?php
namespace GDO\Util;

/**
 * Helper for reflection tasks.
 * 
 * @author gizmore
 * @since 6.10
 */
final class Classes
{
    public static function class_uses($class)
    {
        if (is_object($class))
        {
            $class = get_class($class);
        }
        
        $results = [];
        
        foreach (array_reverse(class_parents($class)) + [$class => $class] as $class)
        {
            $results += self::trait_uses($class);
        }
        
        return array_unique($results);
    }
    
    public static function trait_uses($trait)
    {
        $traits = class_uses($trait);
        
        foreach ($traits as $trait)
        {
            $traits += self::trait_uses($trait);
        }
        
        return $traits;
    }
    
    public static function class_uses_trait($class, $trait)
    {
        $traits = self::class_uses($class);
        return in_array($trait, $traits, true);
    }
    
}
