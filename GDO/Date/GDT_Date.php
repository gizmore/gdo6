<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;
/**
 * A Date is like a Datetime, but a bit older, so we start with year selection.
 * An example is the release date of a book, or a birthdate.
 * 
 * @author gizmore
 * @version 6.10
 * @since 5.0
 * 
 * @see GDT_Time
 * @see GDT_Timestamp
 * @see GDT_Date
 * @see GDT_DateTime
 * @see GDT_Duration
 * @see Time for conversion
 */
class GDT_Date extends GDT_Timestamp
{
	public $dateStartView = 'year';
	
	public $icon = 'calendar';
	
	public function gdoColumnDefine()
	{
		return "{$this->identifier()} DATE {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}

	public function displayVar() { return Time::displayDate($this->getVar(), 'day'); }
	public function renderCell() { return $this->renderCellSpan($this->displayVar()); }
	public function renderForm() { return GDT_Template::php('Date', 'form/date.php', ['field'=>$this]); }
	
	public function toVar($value)
	{
		return substr(parent::toVar($value), 0, 10);
	}
	
	public function htmlValue()
	{
	    $seconds = $this->getValue();
	    $isodate = date('Y-m-d', $seconds);
	    return sprintf(' value="%s"', $isodate);
	}
	
	public function displayValue($var)
	{
	    return Time::displayDate($var, 'day');
	}

}
