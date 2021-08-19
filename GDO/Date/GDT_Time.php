<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;
use GDO\DB\GDT_String;

class GDT_Time extends GDT_String
{
// 	public function defaultLabel() { return $this->label('date'); }

	public function gdoColumnDefine()
	{
		return "{$this->identifier()} TIME {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}

	public function renderForm()
	{
		return GDT_Template::php('Date', 'form/time.php', ['field'=>$this]);
	}
}
