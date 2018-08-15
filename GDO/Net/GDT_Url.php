<?php
namespace GDO\Net;
use GDO\DB\GDT_String;
/**
 * HTTP Url field.
 * Features link checking.
 * 
 * @author gizmore
 * @since 5.0
 * @version 5.0
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
	
	public $min = 0;
	public $max = 255;
	public $pattern = "#(?:https?://|/).*#i";
	
	public function __construct()
	{
		$this->icon('url');
	}
	
	public function allowLocal($allowLocal=true)
	{
		$this->allowLocal = $allowLocal;
		return $this;
	}
	
	public function reachable($reachable=true)
	{
		$this->reachable = $reachable;
		return $this;
	}

	public function validate($value)
	{
		return parent::validate($value) ? $this->validateUrl($value) : false;
	}
	
	public function validateUrl($value)
	{
		if ($value !== null)
		{
			if ( (!$this->allowLocal) && ($value[0] === '/') )
			{
				return $this->error('err_local_url_not_allowed', [htmlspecialchars($value)]);
			}
			if ( ($this->reachable) && (!HTTP::pageExists($value)) )
			{
				return $this->error('err_url_not_reachable', [htmlspecialchars($value)]);
			}
		}
		return true;
	}
}
