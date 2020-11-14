<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;

/**
 * 
 * @author gizmore
 * @version 6.00
 */
class GDT_DateTime extends GDT_Timestamp
{
	public function gdoColumnDefine()
	{
		return "{$this->identifier()} DATETIME(3) {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}

	public function renderForm()
	{
		return GDT_Template::php('Date', 'form/datetime.php', ['field'=>$this]);
	}
	
}
