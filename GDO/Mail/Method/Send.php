<?php
namespace GDO\Mail\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\User\GDT_User;
use GDO\UI\GDT_Message;
use GDO\UI\GDT_Title;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_AntiCSRF;
use GDO\User\GDO_User;

final class Send extends MethodForm
{
    public function createForm(GDT_Form $form)
    {
        $form->info(t('info_send_mail'));
        $form->addFields([
            GDT_User::make('user')->withCompletion()->notNull(),
            GDT_Title::make('title')->notNull(),
            GDT_Message::make('message')->notNull(),
            GDT_AntiCSRF::make()
        ]);
        $form->actions()->addField(GDT_Submit::make());
    }
    
    public function formValidated(GDT_Form $form)
    {
        $from = GDO_User::current();
        $to = $form->getFormValue('user');
        die('NOT IMPLEMENTED YET'); # @todo implement mail sending from user to user
    }
    
}
