<?php
namespace GDO\Language;
use GDO\Form\GDT_Select;
use GDO\Core\GDT_Template;

final class GDT_LangSwitch extends GDT_Select
{
	public function __construct()
	{
	}
	
	public function renderCell()
	{
	    return GDT_Template::php('Language', 'cell/langswitch.php',['field'=>$this]);
	}
}
