<?php
namespace GDO\Util;
/**
 * Add JS here, it calls minify on it, if enabled.
 * @author gizmore
 */
final class Javascript
{
	###################################
	### Asset loader and obfuscator ###
	###################################
	private static $_javascripts = [];
	private static $_javascript_inline = '';
	
	public static function addJavascriptInline($script_html)
	{
		self::$_javascript_inline .= $script_html;
	}

	public static function addJavascript($path)
	{
		self::$_javascripts[] = $path;
	}
	
	public static function addBowerJavascript($path)
	{
		self::addJavascript("bower_components/$path");
	}
	
	public static function displayJavascripts($minfied=false)
	{
		$back = '';
		$javascripts = $minfied ? MinifyJS::minified(self::$_javascripts) : self::$_javascripts;
		foreach ($javascripts as $js)
		{
			$back .= sprintf('<script type="text/javascript" src="%s"></script>'."\n", htmlspecialchars($js));
		}
		return $back . self::displayJavascriptInline();
	}
	
	public static function displayJavascriptInline()
	{
		$inline_defines = sprintf('var GWF_DOMAIN = \'%s\';', GWF_DOMAIN);
		return sprintf('<script type="text/javascript">%s</script>', $inline_defines.self::displayJavascriptOnload());
	}
	
	private static function displayJavascriptOnload()
	{
		return self::$_javascript_inline ? sprintf('; %s;', self::$_javascript_inline) : '';
	}

// 	############
// 	### JSON ###
// 	############
// 	public static function jsonEncodeSingleQuote($object)
// 	{
// 		return str_replace("\"", "'", json_encode($object));
// 	}
}