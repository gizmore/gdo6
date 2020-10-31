<?php
namespace GDO\Date;

use GDO\DB\GDT_UInt;
use GDO\Core\GDT_Template;

/**
 * Duration field int in seconds.
 * @author gizmore
 * @version 6.10
 * @since 6.00
 */
class GDT_Duration extends GDT_UInt
{
	public function defaultLabel() { return $this->label('duration'); }

	public function __construct()
	{
		$this->icon('time');
	}

	public function toValue($var)
	{
		return Time::humanToSeconds($var);
	}
	
	public function toVar($value)
	{
	    return Time::humanDuration($value);
	}
	
	public function displayVar()
	{
	    return $this->getVar();
	}
	
	
	public function renderCell()
	{
		return $this->renderCellSpan($this->displayVar());
	}
	
	public function renderForm()
	{
		return GDT_Template::php('Date', 'form/duration.php', ['field' => $this]);
	}
	
}
