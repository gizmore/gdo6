<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;

class GDT_DateTime extends GDT_Timestamp
{
// 	public function defaultLabel() { return $this->label('date'); }
	
	public function gdoColumnDefine()
	{
		return "{$this->identifier()} DATETIME {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}

	public function renderForm()
	{
		return GDT_Template::php('Date', 'form/datetime.php', ['field'=>$this]);
	}
}
