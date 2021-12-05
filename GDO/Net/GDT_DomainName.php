<?php
namespace GDO\Net;

use GDO\DB\GDT_String;

final class GDT_DomainName extends GDT_String
{
	public $pattern = "/[\\.a-z]+\\.[a-z]+/D";
	
	public $tldonly = false;
	public function tldonly($tldonly=true)
	{
		$this->tldonly = $tldonly;
		return $this;
	}
	
	public function validate($value)
	{
		if (!parent::validate($value))
		{
			return false;
		}
		if ($value === null)
		{
			return true;
		}
		
		$parts = explode('.', $value);
		if ($this->tldonly && count($parts) !== 2)
		{
			return $this->error('err_domain_no_tld');
		}
		
		return true;
	}
	
}
