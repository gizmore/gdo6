<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;

/**
 * A Date is like a Datetime, but a bit older, so we start with year selection.
 * An example is the release date of a book, or a birthdate.
 * 
 * @author gizmore
 * @version 6.11.1
 * @since 5.0.0
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
	public $format = Time::FMT_DAY;
	
	public $icon = 'calendar';
	
	public function gdoColumnDefine()
	{
		return "{$this->identifier()} DATE {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}

	public function renderCell() { return $this->renderCellSpan($this->display()); }
	public function renderForm() { return GDT_Template::php('Date', 'form/date.php', ['field'=>$this]); }
	
	public function toVar($value)
	{
	    if ($value)
	    {
    	    /** @var $value \DateTime **/
    	    return $value->format('Y-m-d');
	    }
	}
	
	public function toValue($var)
	{
	    return empty($var) ? null : Time::parseDateTime($var);
	}
	
	public function htmlValue()
	{
	    return sprintf(' value="%s"', $this->getVar());
	}
	
	public function displayValue($value)
	{
		return Time::displayDateTime($value, $this->format);
	}

	public function inputToVar($input)
	{
	    if ($input)
	    {
            $input = str_replace('T', ' ', $input);
            $input = str_replace('Z', '', $input);
            $d = Time::parseDateTime($input, Time::UTC);
            return $d->format('Y-m-d');
	    }
	}

}
