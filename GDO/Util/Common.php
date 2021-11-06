<?php
namespace GDO\Util;
/**
 * Often used stuff.
 * @author gizmore
 * @version 6.10
 * @since 1.00
 */
final class Common
{
	##################
	### Get / Post ###
	##################
	public static function getGet($var, $default=null) { return isset($_GET[$var]) ? $_GET[$var] : $default; }
	public static function getGetInt($var, $default=0) { return isset($_GET[$var]) && is_string($_GET[$var]) ? (int)$_GET[$var] : $default; }
	public static function getGetFloat($var, $default=0) { return isset($_GET[$var]) && is_string($_GET[$var]) ? (float)$_GET[$var] : $default; }
	public static function getGetString($var, $default=null) { return isset($_GET[$var]) && is_string($_GET[$var]) ? $_GET[$var] : $default; }
	public static function getGetArray($var, $default=[]) { return (isset($_GET[$var]) && is_array($_GET[$var])) ? $_GET[$var] : $default; }

	public static function getPost($var, $default=null) { return isset($_POST[$var]) ? ($_POST[$var]) : $default; }
	public static function getPostInt($var, $default=0) { return isset($_POST[$var]) ? (int)$_POST[$var] : $default; }
	public static function getPostFloat($var, $default=0) { return isset($_POST[$var]) ? (float)$_POST[$var] : $default; }
	public static function getPostString($var, $default=null) { return isset($_POST[$var]) ? (string)$_POST[$var] : $default; }
	public static function getPostArray($var, $default=[]) { return (isset($_POST[$var]) && is_array($_POST[$var])) ? $_POST[$var] : $default; }

	public static function getRequest($var, $default=null) { return isset($_REQUEST[$var]) ? ($_REQUEST[$var]) : $default; }
	public static function getRequestInt($var, $default=0) { return isset($_REQUEST[$var]) ? (int)$_REQUEST[$var] : $default; }
	public static function getRequestFloat($var, $default=0.0) { return isset($_REQUEST[$var]) ? (float)$_REQUEST[$var] : $default; }
	public static function getRequestString($var, $default=null) { return isset($_REQUEST[$var]) ? (string)$_REQUEST[$var] : $default; }
	public static function getRequestArray($var, $default=[]) { return (isset($_REQUEST[$var]) && is_array($_REQUEST[$var])) ? $_REQUEST[$var] : $default; }
	
// 	public static function getForm($var, $default=null) { $vars = self::getRequestArray('form'); return isset($vars[$var]) ? $vars[$var] : $default; }
// 	public static function getFormInt($var, $default=0) { return (int) self::getForm($var, $default); }
// 	public static function getFormString($var, $default=null) { return (string)self::getForm($var, $default); }
// 	public static function getFormFloat($var, $default=0) { return (int) self::getForm($var, $default); }
// 	public static function getFormArray($var, $default=[]) { $f = self::getRequestArray('form', null); return is_array($f) ? $f : $default; }
	
	/**
	 * Return the first match of capturing regex.
	 * @TODO Move to another file?
	 * @param string $pattern
	 * @param string $s
	 * @return string|false
	 */
	public static function regex($pattern, $s)
	{
	    $matches = null;
	    if (preg_match($pattern, $s, $matches))
	    {
	    	if (isset($matches[1]))
	    	{
	    		return $matches[1];
	    	}
	    	return true;
	    }
	    return false;
	}
	
	/**
	 * Clamp a numeric value.
	 * null as min or max disables a check.
	 * $val should be an int or float.
	 * No conversion is done when something is in range.
	 * @param $val number
	 * @param $min number
	 * @param $max number
	 * @return int|float
	 */
	public static function clamp($val, $min=null, $max=null)
	{
	    if ($min !== null && $val < $min)
	    {
	        return $min;
	    }
	    elseif ($max !== null && $val > $max)
	    {
	        return $max;
	    }
	    else
	    {
	        return $val;
	    }
	}
}
