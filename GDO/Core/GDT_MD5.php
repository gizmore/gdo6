<?php
namespace GDO\Core;

use GDO\DB\GDT_Char;

/**
 * An md5 column. binary storage to safe space.
 * @author gizmore
 * @version 6.11.0
 */
final class GDT_MD5 extends GDT_Char
{
	public $encoding = self::BINARY;
	public $caseSensitive = true;
	
	public static function hash($data)
	{
		return md5($data, true);
	}
	
	protected function __construct()
	{
		parent::__construct();
		$this->length(16);
	}
	
	public function renderCell()
	{
	    return sprintf('%s', 'MD5'); 
	}
	
}
