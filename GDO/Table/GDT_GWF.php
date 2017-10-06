<?php
namespace GDO\Table;
use GDO\Core\GDT;

final class GDT_GWF extends GDT
{
	public function renderCard() { return $this->gdo->renderCard(); }
	public function renderCell() { return $this->gdo->renderCell(); }
	public function renderList() { return $this->gdo->renderList(); }
}
