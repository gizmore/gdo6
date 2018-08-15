<?php
namespace GDO\UI;
use GDO\Core\GDT_Template;
final class GDT_Page extends GDT_Panel
{
	public function render() { return GDT_Template::php('UI', 'page.php', ['page'=>$this]); }
}
