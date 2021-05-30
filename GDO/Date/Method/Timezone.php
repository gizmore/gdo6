<?php
namespace GDO\Date\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Date\GDT_Timezone;
use GDO\Form\GDT_Submit;
use GDO\User\GDO_User;
use GDO\Core\Website;

/**
 * Change a user's timezone.
 * @author gizmore
 */
final class Timezone extends MethodForm
{
    public function isUserRequired() { return false; }
    
    public function formName() { return 'tzform'; }
    
    public function createForm(GDT_Form $form)
    {
        $tz = GDO_User::current()->getTimezone();
        $form->slim()->noTitle();
        $form->addFields([
            GDT_Timezone::make('timezone')->positional()->notNull()->initial($tz),
        ]);
        $form->actions()->addField(
            GDT_Submit::make()->label('btn_set'));
    }

    public function formValidated(GDT_Form $form)
    {
        $user = GDO_User::current();
        $old = $user->getTimezone();
        $new = $form->getFormVar('timezone');
        if ($old !== $new)
        {
            $user->tempUnset('timezone');
            $user->persistent()->saveVar('user_timezone', $new);
            Website::redirectMessage('msg_timezone_changed', [$new],
                Website::hrefBack());
        }
        else
        {
            Website::redirectBack();
        }
    }
    
}
