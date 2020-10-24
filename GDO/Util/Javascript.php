<?php
namespace GDO\Util;

use GDO\Core\Application;

/**
 * Add JS here, it calls minify on it, if enabled.
 * 
 * @author gizmore
 * @version 6.11
 * @since 6.00
 */
final class Javascript
{
	###################################
	### Asset loader and obfuscator ###
	###################################
	private static $_javascripts = [];
	private static $_javascript_inline = '';
	
	###########
	### Add ###
	###########
	public static function addJavascript($path)
	{
		self::$_javascripts[] = $path;
	}
	
	public static function addJavascriptInline($script_html)
	{
		self::$_javascript_inline .= $script_html;
	}

	public static function addBowerJavascript($path)
	{
		self::addJavascript("bower_components/$path");
	}
	
	##############
	### Render ###
	##############
	public static function displayJavascripts($minfied=false)
	{
		$back = '';
	    if (Application::instance()->allowJavascript())
	    {
    		$javascripts = $minfied ? MinifyJS::minified(self::$_javascripts) : self::$_javascripts;
    		foreach ($javascripts as $js)
    		{
    			$back .= sprintf('<script type="text/javascript" src="%s"></script>'."\n", $js);
    		}
    		$back .= self::displayJavascriptInline();
	    }
		return $back;
	}
	
	###############
	### Private ###
	###############
	private static function displayJavascriptInline()
	{
	    $inline = self::displayJavascriptOnload();
	    return $inline ? sprintf('<script type="text/javascript">%s</script>', $inline) : '';
	}
	
	private static function displayJavascriptOnload()
	{
		return self::$_javascript_inline ? sprintf('; %s;', self::$_javascript_inline) : '';
	}

}
