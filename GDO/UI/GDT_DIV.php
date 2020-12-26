<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Simple html DIV element.
 * @author gizmore
 */
final class GDT_DIV extends GDT
{
    use WithText;
    use WithPHPJQuery;
    
    public function renderCell()
    {
        return sprintf("<div %s>%s</div>",
            $this->htmlAttributes(), $this->renderText());
    }
    
    public function renderForm() { return $this->renderCell(); }
    
    public function renderCard() { return $this->renderCell(); }

}
