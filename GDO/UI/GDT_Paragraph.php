<?php
namespace GDO\UI;

use GDO\Core\GDT;

final class GDT_Paragraph extends GDT
{
	use WithHTML;
	
	public function renderCell() { return sprintf('<p>%s</p>', $this->html); }
	public function renderCard() { return $this->renderCell(); }
}
