<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * An HTML <pre> element.
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.10.4
 */
final class GDT_Pre extends GDT
{
    use WithText;
    
    public function renderCell()
    {
    	return sprintf('<pre>%s</pre>', $this->renderText());
    }
    
    public function renderCard()
    {
    	return $this->renderText();
    }
    
    public function renderJSON()
    {
        return $this->renderText();
    }
    
    public function renderCLI()
    {
        return $this->renderText();
    }
    
}
