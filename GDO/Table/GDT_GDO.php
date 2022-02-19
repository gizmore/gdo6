<?php
namespace GDO\Table;

use GDO\Core\GDT;

/**
 * Rendering wrapper for GDOs.
 * 
 * @author gizmore
 * @version 6.11.4
 * @since 6.2.0
 */
final class GDT_GDO extends GDT
{
	public function renderCard() { return $this->gdo->renderCard(); }
	public function renderCell() { return $this->gdo->renderCell(); }
	public function renderCLI() { return $this->gdo->renderCLI(); }
	public function renderList() { return $this->gdo->renderList(); }
	public function renderJSON() { return $this->gdo->renderJSON(); }
	public function renderXML() { return $this->gdo->renderXML(); }
	
}
