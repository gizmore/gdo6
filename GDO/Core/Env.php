<?php
namespace GDO\Core;

use GDO\File\FileUtil;

/**
 * Parses env.php for wholesome config.
 * @author gizmore
 */
final class Env
{
    /**
     * Load env.php
     */
    public static function init()
    {
        $envPath = GDO_PATH . 'env.php';
        if (FileUtil::isFile($envPath))
        {
            $env = require $envPath;
            foreach ($env as $k => $v)
            {
                $_ENV[strtoupper($k)] = $v;
            }
        }
    }
    
    /**
     * Get an ENV var.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default=null)
    {
        $key = strtoupper($key);
        return isset($_ENV[$key]) ? $_ENV[$key] : $default;
    }
    
}

Env::init();
