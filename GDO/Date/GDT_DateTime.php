<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;

/**
 * A datetime column has a bigger range of dates compared to a GDT_Timestamp.
 * 
 * @author gizmore
 * @version 6.11.2
 */
class GDT_DateTime extends GDT_Date
{
	public function gdoColumnDefine()
	{
		return "{$this->identifier()} DATETIME({$this->millis}) {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}

	public function renderForm()
	{
		return GDT_Template::php('Date', 'form/datetime.php', ['field'=>$this]);
	}
	
	public function htmlValue()
	{
	    $seconds = $this->getValue();
	    $isodate = date('Y-m-d H:i:s', $seconds);
	    return sprintf(' value="%s"', $isodate);
	}
	
	public function toVar($value)
	{
	    if ($value)
	    {
	        /** @var $value \DateTime **/
	    	$value->setTimezone(Time::$UTC);
	        return $value->format('Y-m-d H:i:s.u');
	    }
	}
	
	public function displayValue($var)
	{
	    return Time::displayDate($var);
	}
	
	public function _inputToVar($input)
	{
	    $input = str_replace('T', ' ', $input);
	    $input = str_replace('Z', '', $input);
	    $d = Time::parseDateTime($input);
	    $d->setTimezone(Time::$UTC);
	    $var = $d->format('Y-m-d H:i:s.u');
	    return $var;
	}

	public function htmlClass()
	{
		return ' gdt-datetime';
	}
	
}
