<?php
namespace GDO\Net;

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
}