<?php
namespace GDO\Install\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Core\GDT_Template;
use GDO\Form\GDT_Submit;

/**
 * Optionally copy the main htaccess file.
 * @author gizmore
 */
final class CopyHTAccess extends MethodForm
{
    public function renderPage()
    {
        return GDT_Template::responsePHP('Install', 'page/copyhtaccess.php', ['form' => $this->getForm()]);
    }

    public function createForm(GDT_Form $form)
    {
        $form->actions()->addField(GDT_Submit::make()->label('copy_htaccess'));
    }
    
    public function formValidated(GDT_Form $form)
    {
        copy(GDO_PATH . '.htaccess.example', GDO_PATH . '.htaccess');
        return parent::formValidated($form)->addField($this->renderPage());
    }
    
}
