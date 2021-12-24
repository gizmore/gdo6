<?php
namespace GDO\UI;

use GDO\Core\Website;

/**
 * The back button points to your origin.
 * It has a default icon and label.
 * 
 * @author gizmore
 * @version 6.11.2
 * @since 6.3.0
 */
final class GDT_Back extends GDT_Link
{
	public function defaultLabel() { return $this->label('btn_back'); }

	protected function __construct()
	{
		parent::__construct();
		$this->name('back');
		$this->icon('back');
		$this->href(Website::hrefBack());
	}
	
	public function htmlClass()
	{
		return ' gdt-link gdt-back';
	}
	
}
