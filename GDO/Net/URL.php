<?php
namespace GDO\Net;

use GDO\Util\Common;
use GDO\Core\Application;

/**
 * This class holds url parts and the raw url.
 * It is the return value of GDT_Url->toValue().
 * 
 * @author gizmore
 * @version 6.10
 * @sinve 6.02
 * 
 * @see GDT_Url
 */
final class URL
{
    ##############
    ### Static ###
    ##############
	public static function localScheme()
	{
	    if (Application::instance()->isCLI())
	    {
	        return GDO_PROTOCOL;
	    }
	    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
	}
	
	###############
	### Members ###
	###############
	public $raw;
	public $parts;
	
	public function __construct($url)
	{
		$this->raw = $url;
		$this->parts = parse_url($url);
	}
	
	public function getScheme()
	{
	    return isset($this->parts['scheme']) ? $this->parts['scheme'] : self::localScheme();
	}
	
	public function getHost()
	{
		return $this->parts['host'];
	}
	
	public function getPort()
	{
		return $this->parts['port'];
	}
	
	public function getTLD()
	{
	    return Common::regex('/([^.]\\.[^.])$/ui', $this->getHost());
	}

}
