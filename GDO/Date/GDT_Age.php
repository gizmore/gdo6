<?php
namespace GDO\Date;

final class GDT_Age extends GDT_Duration
{
	public $unsigned = true;

	public function defaultLabel() { return $this->label('age'); }
	
	public function renderCell() { return Time::displayAgeTS($this->getValue()); }
	
}
