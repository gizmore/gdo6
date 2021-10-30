<?php
namespace GDO\Core;

/**
 * @TODO: Is quirky and does not like other responses added. Remove entirely?
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.0.5
 */
final class GDT_ResponseCard extends GDT_Response
{
	use WithFields;
	
	##############
	### Render ###
	##############
	public function renderCell()
	{
		return $this->renderHTML();
	}
	
	public function renderCard()
	{
		return $this->renderHTML();
	}
	
	public function renderHTML()
	{
	    return
	    	$this->gdo->renderCard() .
	    	parent::renderHTML();
	}
	
	public function renderJSON()
	{
		return [
			'code' => $this->code,
		    'data' => parent::renderJSON(),
			'card' => $this->gdo->renderJSON(),
		];
	}
	
}
