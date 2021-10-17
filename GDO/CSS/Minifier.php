<?php
namespace GDO\CSS;

final class Minifier
{
    public static $FILES = [];
    
    public static $ASSETS = [];
    
    public static $INLINE = '';
    
    public static function addFile($path)
    {
        self::$FILES[] = $path;
    }
    
    public static function minify()
    {
        
    }
    
    public static function renderMinified()
    {
        
    }
    
    public static function renderOriginal()
    {
        $back = '';
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
    
}
