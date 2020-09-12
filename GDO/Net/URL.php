<?php
namespace GDO\Net;

use GDO\Util\Common;

final class URL
{
	public $raw;
	public $parts;
	
	public function __construct($url)
	{
		$this->raw = $url;
		$this->parts = parse_url($url);
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
