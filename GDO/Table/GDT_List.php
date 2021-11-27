<?php
namespace GDO\Table;

use GDO\Core\GDT_Template;
use GDO\Core\GDT;

/**
 * Similiar to a table, a list displays multiple cards or list items.
 * 
 * Control ->itemTemplate(GDT) which defaults to GDT_GWF.
 * Control ->listMode(1|2) for cards or list items.
 * 
 * @author gizmore
 * @version 6.10
 * @since 5.0
 * 
 * @see GDT_Table
 */
class GDT_List extends GDT_Table
{
	const MODE_CARD = 1;
	const MODE_LIST = 2;
	
	private $listMode = self::MODE_LIST;
	public function listMode($mode)
	{
		$this->listMode = $mode;
		return $this;
	}
	
	################
	### Template ###
	################
	public $itemTemplate;
	public function itemTemplate(GDT $gdt)
	{
		$this->itemTemplate = $gdt;
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
	
	public function configJSON()
	{
	    return array_merge(parent::configJSON(), [
	        'listMode' => $this->listMode,
	    ]);
	}
	
	public static $CURRENT;
	public $data;
	
	protected function renderJSONData()
	{
	    self::$CURRENT = $this;
	    $this->data = [];
	    $result = $this->getResult();
	    $table = $result->table;
	    while ($gdo = $table->fetch($result))
	    {
	        $gdo->renderList();
	    }
	    return $this->data;
	}
	
}
