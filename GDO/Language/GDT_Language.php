<?php
namespace GDO\Language;

use GDO\DB\GDT_ObjectSelect;

class GDT_Language extends GDT_ObjectSelect
{
    public function defaultLabel() { return $this->label('language'); }
    
    public function __construct()
    {
        $this->table(GDO_Language::table());
        $this->min = $this->max = 2;
    }

    ###############
    ### Choices ###
    ###############
    private $all = false;
    public function all($all=true)
    {
        $this->all = $all;
        return $this;
    }
    
    public function initChoices()
    {
        return $this->choices ? $this : $this->choices($this->languageChoices());
    }
    
    private function languageChoices()
    {
        $languages = GDO_Language::table();
        return $this->all ? $languages->all() : $languages->allSupported();
    }
    
    ##################
    ### Completion ###
    ##################
    public function withCompletion()
    {
        return $this->completionHref(href('Language', 'Completion'));
    }
    
    ##############
    ### Render ###
    ##############
//     public function render()
//     {
//         if ($this->completionHref)
//         {
//             return GDT_Template::php('GWF', 'form/object_completion.php', ['field'=>$this]);
//         }
//         else
//         {
//             $this->choices = $this->languageChoices();
//             return GDT_Template::php('form/language.php', ['field'=>$this]);
//         }
//     }
    
//     public function renderCell()
//     {
//         return GDT_Template::php('Language', 'cell/language.php', ['language'=>$this->gdo]);
//     }
    
//     public function renderChoice()
//     {
//         return GDT_Template::php('Language', 'choice/language.php', ['language'=>$this->gdo]);
//     }
    
}

