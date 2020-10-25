<?php
namespace GDO\Table;

use GDO\Core\GDT_Template;
use GDO\Core\GDT;

/**
 * Similiar to a table, a list displays multiple cards or list items.
 * 
 * Control ->itemTemplate(GDT) which defaults to GDT_GWF.
 * Control ->listMode(1|2) for cards or list items.
 * Control ->quicksort(true) for a quicksearch and order form.
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
	
	#################
	### Quicksort ###
	#################
	public $quicksort = true;
	public function quicksort($quicksort=true) { $this->quicksort = $quicksort; return $this; }
	
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
	        'quicksort' => $this->quicksort,
	        'listMode' => $this->listMode,
	    ]);
	}
	
}
