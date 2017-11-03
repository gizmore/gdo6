<?php
namespace GDO\UI;
use GDO\DB\GDT_String;
/**
 * A short utf8 title.
 * Pretty common.
 * @author gizmore
 */
class GDT_SearchField extends GDT_String
{
	public function defaultLabel() { return $this->label('search'); }
	public $min = 3;
	public $max = 128;
	public function __construct()
	{
		$this->icon('search');
	}
}
