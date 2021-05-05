<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A simple paragraph.
 * Should render well everywhere as a paragraph might be important text.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 6.0.0
 */
class GDT_Paragraph extends GDT
{
    use WithText;

    public function render() { return $this->renderCell(); }
    public function renderCard() { return $this->renderCell(); }
	public function renderCell() { return sprintf('<p class="gdt-paragraph">%s</p>', $this->renderText()); }
	public function renderCLI() { return $this->renderText(); }
	public function renderJSON() { return $this->renderCLI(); }
	public function renderForm() { return sprintf('<div>%s</div>', $this->renderCell()); }

}
