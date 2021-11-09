<?php
namespace GDO\Table;

use GDO\Core\GDT;

/**
 * Rendering wrapper for GDOs.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.2.0
 */
final class GDT_GWF extends GDT
{
	public function renderCard() { return $this->gdo->renderCard(); }
	public function renderCell() { return $this->gdo->renderCell(); }
// 	public function renderChoice() { return $this->gdo->renderChoice(); }
	public function renderList() { return $this->gdo->renderList(); }

}
