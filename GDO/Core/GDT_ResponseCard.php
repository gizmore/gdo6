<?php
namespace GDO\Core;

/**
 * @author gizmore
 * @version 6.10
 * @since 6.05
 */
final class GDT_ResponseCard extends GDT_Response
{
	##############
	### Render ###
	##############
	public function renderHTML()
	{
	    return $this->gdo->renderCard() . parent::renderHTML();
	}

	public function renderJSON()
	{
		return array(
			'code' => $this->code,
		    'data' => parent::renderJSON(),
			'card' => $this->gdo->renderJSON(),
		);
	}
	
}
