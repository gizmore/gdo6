<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * A popup shown once after the page has loaded.
 * 
 * @author gizmore
 * @since 6.10.4
 */
final class GDT_Popup extends GDT
{
    use WithText;
    use WithPHPJQuery;
    
    ##############
    ### Render ###
    ##############
    public function renderCell()
    {
        return GDT_Template::php('UI', 'cell/popup.php', [
            'field' => $this,
        ]);
    }
    
    public function renderJSON()
    {
        return $this->renderText();
    }
    
    public function renderCLI()
    {
        # Echo instead of return... kinda popup
        echo $this->renderText();
    }
    
}
