<?php
namespace GDO\User;
use GDO\Util\BCrypt;
final class OnInstall
{
    public static function onInstall()
    {
        if (!GDO_User::getByName('system'))
        {
            $user = GDO_User::blank(array(
                'user_id' => '1',
                'user_name'=>'system',
                'user_email' => GWF_BOT_EMAIL,
                'user_type' => 'bot',
                'user_password' => BCrypt::create('system')->__toString(),
            ))->insert();
        }
    }
}
