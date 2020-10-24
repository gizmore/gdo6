<?php
namespace GDO\UI;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
/**
 * Very simple field that only has custom html content.
 * you can pass ->gdt($gdt) to render a gdt as well.
 * @author gizmore
 * @see \GDO\UI\GDT_Panel
 * @version 6.11
 * @since 6.03
 */
final class GDT_HTML extends GDT
{
	use WithHTML;
	use WithPHPJQuery;
	
	private $gdt;
	public function gdt(GDT $gdt)
	{
	    $this->gdt = $gdt;
	    return $this;
	}
	
	##############
	### Render ###
	##############
	public function render() { return $this->renderCell(); }
	public function renderCard() { return $this->renderCell(); }
	public function renderCell()
	{
	    if ($this->gdt)
	    {
	        $this->html .= $this->gdt->renderCell();
	    }
	    return GDT_Template::php('UI', 'cell/html.php', ['field'=>$this]);
	}

}
