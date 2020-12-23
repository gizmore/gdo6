<?php
namespace GDO\Core;
use GDO\DB\GDT_Char;
/**
 * @author gizmore
 */
final class GDT_MD5 extends GDT_Char
{
	public $encoding = self::BINARY;
	public $caseSensitive = true;
	
	protected function __construct()
	{
		$this->length(16);
	}
	
	public function renderCell()
	{
		return GDT_Template::php('Type', 'cell/md5', ['field'=>$this]);
	}
}
