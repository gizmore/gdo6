<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;

/**
 * 
 * @author gizmore
 * @version 6.10.4
 */
class GDT_DateTime extends GDT_Date
{
	public function gdoColumnDefine()
	{
		return "{$this->identifier()} DATETIME(3) {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
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
	        return $value->format('Y-m-d H:i:s.u');
	    }
	}
	
	public function displayValue($var)
	{
	    return Time::displayDate($var);
	}
	
}
