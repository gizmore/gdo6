<?php
namespace GDO\UI;

/**
 * @author gizmore
 */
final class GDT_BackButton extends GDT_IconButton
{
	protected function __construct()
	{
		$this->icon('back');
		$this->label('btn_back');
		$this->href('javascript:window.history.back()');
	}
	
}
