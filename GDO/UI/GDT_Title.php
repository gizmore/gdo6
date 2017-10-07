<?php
namespace GDO\UI;
use GDO\DB\GDT_String;
/**
 * A short utf8 title.
 * Pretty common.
 * @author gizmore
 */
class GDT_Title extends GDT_String
{
	public function defaultLabel() { return $this->label('title'); }
	public $min = 3;
	public $max = 64;
}
