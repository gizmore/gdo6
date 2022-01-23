<?php
namespace GDO\Crypto;

use GDO\DB\GDT_Char;

/**
 * A combined hash. SHA1+MD5
 * @author gizmore
 */
final class GDT_GDOHash extends GDT_Char
{
	protected function __construct()
	{
		parent::__construct();
		$this->length(40 + 32);
	}
	
	public function digest($data)
	{
		return sha1($data) . md5($data);
	}
	
}
