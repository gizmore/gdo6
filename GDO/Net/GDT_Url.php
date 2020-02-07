<?php
namespace GDO\Net;
use GDO\DB\GDT_String;
/**
 * HTTP Url field.
 * Features link checking.
 * Value is a @see URL.
 * 
 * @author gizmore
 * @since 5.0
 * @version 6.09
 */
class GDT_Url extends GDT_String
{
	public static function host() { return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : GWF_DOMAIN; }
	public static function protocol() { return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'; }
	public static function absolute($url) { return sprintf('%s://%s%s%s', self::protocol(), self::host(), GWF_WEB_ROOT, self::relative($url)); }
	public static function relative($url) { return $url; }
	
	public function defaultLabel() { return $this->label('url'); }
	
	public $reachable = false;
	public $allowLocal = false;
	public $allowExternal = true;
	
	public $min = 0;
	public $max = 1024;
// 	public $pattern = "#(?:https?://|/).*#i";
	
	public function __construct()
	{
		$this->icon('url');
	}
	
	public function toValue($var)
	{
		return new URL($var);
	}
	
	public function toVar($value)
	{
	    return $value ? $value->raw : null;
	}
	
	public function allowLocal($allowLocal=true)
	{
		$this->allowLocal = $allowLocal;
		return $this;
	}
	
	public function allowExternal($allowExternal=true)
	{
		$this->allowExternal = $allowExternal;
		return $this;
	}
	
	public function reachable($reachable=true)
	{
		$this->reachable = $reachable;
		return $this;
	}

	################
	### Validate ###
	################
	public function validate($value)
	{
		if (!parent::validate($value?$value->raw:null))
		{
			return false;
		}
		return $this->validateUrl($value);
	}
	
	public function validateUrl($value)
	{
		# null seems allowed
		if (null === ($value = $value->raw))
		{
			return true;
		}

		# Check local
		if ( (!$this->allowLocal) && ($value[0] === '/') )
		{
			return $this->error('err_local_url_not_allowed', [html($value)]);
		}
		
		if ( (!$this->allowExternal) && ($value[0] !== '/') )
		{
			return $this->error('err_external_url_not_allowed', [html($value)]);
		}
		
		# Check reachable
		if ( ($this->reachable) && (!HTTP::pageExists($value)) )
		{
			if ($value[0] === '/')
			{
				return true;
			}
			return $this->error('err_url_not_reachable', [html($value)]);
		}
		
		return true;
	}

}
