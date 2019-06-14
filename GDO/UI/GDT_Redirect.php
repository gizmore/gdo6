<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\Website;

final class GDT_Redirect extends GDT
{
	use WithHREF;
	
	public $redirectTime = 0;
	public function redirectTime($redirectTime=8) { $this->redirectTime = $redirectTime; return $this; }
	
	
	public function hrefBack()
	{
		return $this->href(Website::hrefBack());
	}
	
	public function renderCell()
	{
		Website::redirect($this->href, $this->redirectTime);
		return t('gdt_redirect_to', [html($this->href)]);
	}
}
