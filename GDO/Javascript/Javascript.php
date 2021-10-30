<?php
namespace GDO\Javascript;

use GDO\Core\Application;

/**
 * Add JS here, it calls minify on it, if enabled.
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.0.0
 */
final class Javascript
{
	###################################
	### Asset loader and obfuscator ###
	###################################
	private static $_javascripts = [];
	private static $_javascript_pre_inline = '';
	private static $_javascript_post_inline = '';
	
	###########
	### Add ###
	###########
	public static function addJavascript($path)
	{
		self::$_javascripts[] = $path;
	}
	
	public static function addJavascriptPreInline($script_html)
	{
	    self::$_javascript_pre_inline .= $script_html . "\n";
	}
	
	public static function addJavascriptPostInline($script_html)
	{
	    self::$_javascript_post_inline .= $script_html . "\n";
	}
	
// 	public static function addBowerJavascript($path)
// 	{
// 		self::addJavascript("bower_components/$path");
// 	}
	
	##############
	### Render ###
	##############
	public static function displayJavascripts($minfied=false)
	{
		$back = '';
	    if (Application::instance()->allowJavascript())
	    {
	        $back .= self::displayJavascriptPreInline();
    		$javascripts = $minfied ? MinifyJS::minified(self::$_javascripts) : self::$_javascripts;
    		foreach ($javascripts as $js)
    		{
    			$back .= sprintf('<script src="%s"></script>'."\n", $js);
    		}
    		$back .= self::displayJavascriptPostInline();
	    }
		return $back;
	}
	
	###############
	### Private ###
	###############
	private static function displayJavascriptPreInline()
	{
	    return self::displayJavascriptInline(self::$_javascript_pre_inline);
	}
	
	private static function displayJavascriptPostInline()
	{
	    return self::displayJavascriptInline(self::$_javascript_post_inline);
	}
	
	private static function displayJavascriptInline($inline)
	{
	    return $inline ? sprintf("<script>\n%s\n</script>\n", $inline) : '';
	}
	
}
