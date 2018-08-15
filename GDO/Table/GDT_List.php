<?php
namespace GDO\Table;
use GDO\Core\GDT_Template;
use GDO\Core\GDT;

/**
 * Similiar to a table, a list displays multiple cards.
 * 
 * @author gizmore
 * @since 5.0
 * @version 5.0
 */
class GDT_List extends GDT_Table
{
	const MODE_CARD = 1;
	const MODE_LIST = 2;
	private $listMode= self::MODE_CARD;
	public function listMode($mode)
	{
		$this->listMode = $mode;
		return $this;
	}
	
	public $itemTemplate;
	public function itemTemplate(GDT $gdoType)
	{
		$this->itemTemplate = $gdoType;
		return $this;
	}
	
	public function getItemTemplate()
	{
		return $this->itemTemplate ? $this->itemTemplate : GDT_GWF::make();
	}
	
	##############
	### Render ###
	##############
	public function renderCell()
	{
		$template = $this->listMode === self::MODE_CARD ? 'cell/list_card.php' : 'cell/list.php';
		return GDT_Template::php('Table', $template, ['field'=>$this]);
	}
	
	public function initJSON()
	{
		$pagemenu = $this->getPageMenu();
		return array(
			'tableName' => $this->getResult()->table->gdoClassName(),
			'pagemenu' => $pagemenu ? $pagemenu->initJSON() : null,
// 			'sortable' => $this->sortable,
// 			'sortableURL' => $this->sortableURL,
		);
	}

	public function renderJSON()
	{
		$pagemenu = $this->getPageMenu();
		return array(
			'pagemenu' => $pagemenu ? $pagemenu->renderJSON() : null,
			'result' => $this->getResult()->renderJSON($this->getFields()),
		);
	}
	
}
