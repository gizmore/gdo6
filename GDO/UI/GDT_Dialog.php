<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;

/**
 * A dialog.
 * Very simple JS is used to display it.
 * Should almost work with CSS only.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.10.4
 */
class GDT_Dialog extends GDT
{
    use WithTitle;
    use WithFields;
    use WithPHPJQuery;
    use WithActions;
    
	public function renderCell()
	{
		return GDT_Template::php('UI', 'cell/dialog.php', ['field' => $this]);
	}
	
	##############
	### Opened ###
	##############
	public $opened = false;
	
	/**
	 * Start dialog in open mode?
	 * @param boolean $opened
	 * @return \GDO\UI\GDT_Dialog
	 */
	public function opened($opened=true)
	{
	    $this->opened = $opened;
	    return $this;
	}
	
	#############
	### Modal ###
	#############
	/**
	 * Start dialog in modal mode?
	 * @var boolean
	 */
	public $modal = false;
	public function modal($modal=true)
	{
	    $this->modal = $modal;
	    return $this;
	}
	
	public function okButton($key='btn_ok', array $args=null)
	{
		$btn = GDT_IconButton::make('ok')->label($key, $args);
		$btn->attr('onclick', "GDO.closeDialog('{$this->id()}', 'ok')");
		$this->actions()->addField($btn);
		return $this;
	}

	public function cancelButton($key='btn_cancel', array $args=null)
	{
		$btn = GDT_IconButton::make('cancel')->label($key, $args);
		$btn->attr('onclick', "GDO.closeDialog('{$this->id()}', 'cancel')");
		$this->actions()->addField($btn);
		return $this;
	}

}
