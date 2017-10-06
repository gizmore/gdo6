<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;

class GDT_Tooltip extends GDT_Icon
{
    use WithTooltip;

	public function renderCell() { return GDT_Template::php('UI', 'cell/tooltip.php', ['field'=>$this]); }
}
