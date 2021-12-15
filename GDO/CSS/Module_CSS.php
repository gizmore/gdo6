<?php
namespace GDO\CSS;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_Checkbox;
use GDO\Util\Strings;

/**
 * CSS related settings and utilities.
 * @author gizmore
 * @version 6.10.5
 */
final class Module_CSS extends GDO_Module
{
    public function onLoadLanguage() { return $this->loadLanguage('lang/css'); }
    
    public function isCoreModule() { return true; }
    
    public function getConfig()
    {
        return [
            GDT_Checkbox::make('minify_css')->initial('0'),
        ];
    }
    
    public function cfgMinify() { return $this->getConfigVar('minify_css'); }
    
    public function includeMinifier()
    {
        spl_autoload_register([$this, 'psr']);
    }

    /**
     * Not psr but gizmore bullshit autoloader :)
     * @param string $classname
     */
    public function psr($classname)
    {
        $prefix = 'MatthiasMullie\\Minify\\';
        if (str_starts_with($classname, $prefix))
        {
            $classname = substr($classname, strlen($prefix));
            $path = str_replace('\\', '/', $classname);
            $path = GDO_PATH . 'GDO/CSS/minify/src/' . $path . '.php';
            require $path;
        }
        
        $prefix = 'MatthiasMullie\\PathConverter\\';
        if (str_starts_with($classname, $prefix))
        {
            $classname = substr($classname, strlen($prefix));
            $path = str_replace('\\', '/', $classname);
            $path = GDO_PATH . 'GDO/CSS/path-converter/src/' . $path . '.php';
            require $path;
        }
    }
    
}
