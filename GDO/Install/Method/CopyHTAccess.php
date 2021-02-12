<?php
namespace GDO\Install\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Submit;

/**
 * Optionally copy the main htaccess file.
 * @author gizmore
 */
final class CopyHTAccess extends MethodForm
{
    public function createForm(GDT_Form $form)
    {
        $form->addField(GDT_AntiCSRF::make());
        $form->actions()->addField(GDT_Submit::make());
    }
    
    public function formValidated(GDT_Form $form)
    {
        
    }
    
}
