<?php
namespace GDO\CSS;

use MatthiasMullie\Minify\CSS;
use GDO\Core\Module_Core;
use GDO\File\FileUtil;
use GDO\Util\Strings;
use GDO\DB\Database;

final class Minifier
{
    public static $FILES = [];
    public static $EXTERNAL = [];
    public static $INLINE = '';
    
    private static $HASH = null;
    
    public static function addFile($path)
    {
        self::$FILES[] = $path;
    }
    
    public static function addExternal($url)
    {
        self::$EXTERNAL[] = $url;
    }
    
    public static function addInline($css)
    {
        self::$INLINE .= $css;
    }
    
    public static function assetPath($append='')
    {
        return GDO_PATH . 'assets/' . self::getHash() . $append;
    }
    
    public static function assetHref($append='')
    {
        return GDO_WEB_ROOT . 'assets/' . self::getHash() . '/' . $append;
    }
    
    public static function renderMinified()
    {
        if (!is_file(self::assetPath('/css.css')))
        {
            try
            {
                Database::instance()->lock('MINIFY_CSS');
                if (!is_file(self::assetPath('/css.css')))
                {
                    self::minify();
                }
            }
            catch (\Throwable $e)
            {
                throw $e;
            }
            finally
            {
                Database::instance()->unlock('MINIFY_CSS');
            }
        }
        
        $back = '';

        foreach (self::$EXTERNAL as $path)
        {
            $back .= sprintf("\t<link rel=\"stylesheet\" href=\"%s\" /> \n", $path);
        }
        
        $v = Module_Core::instance()->nocacheVersion();
        $href = self::assetHref('css.css?'.$v);
        $back .= '<link rel="stylesheet" href="'.$href.'" />' . "\n";
        return $back;
    }
    
    public static function renderOriginal()
    {
        $back = '';
        
        foreach (self::$EXTERNAL as $path)
        {
            $back .= sprintf("\t<link rel=\"stylesheet\" href=\"%s\" /> \n", $path);
        }
        
        foreach (self::$FILES as $path)
        {
            $back .= sprintf("\t<link rel=\"stylesheet\" href=\"%s\" /> \n", $path);
        }
        
        if (self::$INLINE)
        {
            $back .= sprintf("\t<style><!--\n\t%s\n\t--></style>\n",
                self::$INLINE);
        }
        
        return $back;
    }
    
    private static function minify()
    {
        Module_CSS::instance()->includeMinifier();
        
        $dir = self::assetPath();
        FileUtil::createDir($dir);
        
        $minifier = new CSS();
        foreach (self::$FILES as $file)
        {
            $file = self::hrefToPath($file);
            $minifier->addFile($file);
        }
        $minifier->add(self::$INLINE);
        $minifier->minify(self::assetPath('/css.css'));
    }
    
    private static function hrefToPath($href)
    {
        $path = Strings::substrFrom($href, GDO_WEB_ROOT);
        $path = Strings::substrTo($path, '?', $path);
        return GDO_PATH . $path;
    }
    
    private static function getHash()
    {
        if (self::$HASH === null)
        {
            $data = '';
            foreach (self::$FILES as $path)
            {
                $data .= $path;
            }
            $data .= self::$INLINE;
            $data .= Module_Core::instance()->nocacheVersion();
            self::$HASH = md5($data);
        }
        return self::$HASH;
    }
    
}
