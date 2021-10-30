<?php
namespace GDO\Date\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Date\GDT_Timezone;
use GDO\Form\GDT_Submit;
use GDO\User\GDO_User;
use GDO\Core\Website;
use GDO\Core\GDT_Response;
use GDO\Session\GDO_Session;

/**
 * Change a user's timezone.
 * 
 * @author gizmore
 */
final class Timezone extends MethodForm
{
    public function isTransactional() { return false; }
    public function isUserRequired() { return false; }
    
    public function formName() { return 'tzform'; }
    
    public function createForm(GDT_Form $form)
    {
        $tz = GDO_User::current()->getTimezone();
        $form->action(href('Date', 'Timezone'));
        $form->slim()->noTitle();
        $form->addFields([
            GDT_Timezone::make('timezone')->positional()->notNull()->initial($tz),
        ]);
        $form->actions()->addField(
            GDT_Submit::make()->label('btn_set'));
    }

    public function formValidated(GDT_Form $form)
    {
        $this->setTimezone($form->getFormValue('timezone'), false);
    }
    
    public function setTimezone($timezone, $redirect=true)
    {
        $user = GDO_User::current();
        $old = $user->getTimezone();
        $new = $timezone;
        if ($old !== $new)
        {
            $user->setVar('user_timezone', $new);
            if ($user->isUser())
            {
                $user->save();
            }
            else
            {
                if (class_exists('\\GDO\\Session\\GDO_Session', false))
                {
                    GDO_Session::set('timezone', $new);
                }
                else
                {
                    $user->tempSet('timezone', $new);
                }
            }
            if ($redirect)
            {
                Website::redirectMessage('msg_timezone_changed', [$new]);
            }
        }
        else
        {
            if ($redirect)
            {
                Website::redirectError('err_nothing_happened');
            }
        }
        
        return GDT_Response::make();
    }
    
}
