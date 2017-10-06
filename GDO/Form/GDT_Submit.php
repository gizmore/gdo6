<?php
namespace GDO\Form;
use GDO\Core\GDT_Template;
use GDO\Core\GDT;
use GDO\UI\WithIcon;
use GDO\UI\WithLabel;
/**
 * Form submit button.
 * @author gizmore
 * @since 1.00
 * @version 7.00
 */
class GDT_Submit extends GDT
{
    use WithIcon;
    use WithLabel;
    use WithFormFields;

    public function name($name=null) { $this->name = $name ? $name : 'submit'; return $this->defaultLabel(); }
    public function renderCell() { return GDT_Template::php('Form', 'form/submit.php', ['field'=>$this]); }
}
