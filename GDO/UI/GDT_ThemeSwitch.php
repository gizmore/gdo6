<?php
namespace GDO\UI;

use GDO\Form\GDT_Select;

final class GDT_ThemeSwitch extends GDT_Select
{
    public function initChoices()
    {
        if (!$this->choices)
        {
            $this->choices = $this->generateChoices();
        }
        return $this;
    }
    
    private function  generateChoices()
    {
    }

}

