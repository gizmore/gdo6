<?php
namespace GDO\Mail\Method;

use GDO\Core\GDT;
use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\User\GDT_User;
use GDO\UI\GDT_Message;
use GDO\UI\GDT_Title;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Validator;
use GDO\User\GDO_User;
use GDO\Mail\Module_Mail;
use GDO\Mail\Mail;

/**
 * Send a mail to a user that allows it.
 * 
 * @todo implement
 * @author gizmore
 *
 */
final class Send extends MethodForm
{
    public function createForm(GDT_Form $form)
    {
        if (GDO_User::current()->hasMail())
        {
            $form->info(t('info_send_mail'));
            $form->addFields([
                GDT_User::make('user')->withCompletion()->notNull(),
                GDT_Title::make('title')->notNull(),
                GDT_Message::make('message')->notNull(),
                GDT_AntiCSRF::make(),
                GDT_Validator::make('validate_allowance')->validator('user', [$this, 'validateAllowance']),
            ]);
            $form->actions()->addField(GDT_Submit::make());
        }
        else
        {
            $form->info(t('info_need_mail'));
            $form->actions()->addField(GDT_Submit::make()->disabled());
        }
    }
    
    /**
     * Validate if the user allows sending them an email.
     * 
     * @param GDT_Form $form
     * @param GDT $field
     * @param GDO_User $target
     * @return boolean
     */
    public function validateAllowance(GDT_Form $form, GDT $field, GDO_User $target)
    {
        if (!Module_Mail::instance()->userSettingValue($target, 'allow_email'))
        {
            return $field->error('err_user_does_not_want_mail');
        }
        if (!$target->hasMail())
        {
            return $field->error('err_user_does_not_has_mail');
        }
        if ($target === GDO_User::current())
        {
            return $field->error('err_arbr_mail_self');
        }
        return true;
    }
    
    public function formValidated(GDT_Form $form)
    {
        $from = GDO_User::current();
        $to = $form->getFormValue('user');
        $title = $form->getFormValue('title');
        /** @var $to GDO_User **/

        $mail = Mail::botMail();
        $mail->setReturn($from->getMail());
        $mail->setReturnName($from->displayNameLabel());
        $mail->setReceiver($to->getMail());
        $mail->setReceiverName($to->displayNameLabel());
        $mail->setSubject(t('mail_send_arbr_subj',
            [sitename(), $title, $from->displayNameLabel()]));
        
        $bodyArgs = [
            $to->displayNameLabel(),
            $from->displayNameLabel(),
            sitename(),
            $title,
            $form->getFormValue('message'),
        ];
        $mail->setBody(t('mail_send_arbr_body', $bodyArgs));
        
        $mail->sendToUser($to);
        
        return $this->message('msg_arbr_mail_sent', [$to->displayNameLabel()]);
    }
    
}
