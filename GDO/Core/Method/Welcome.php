<?php
namespace GDO\Core\Method;
use GDO\Core\Method;
use GDO\User\GDO_User;
/**
 * Default method that prints a hello world message.
 * @author gizmore
 * @since 3.00
 * @version 6.00
 */
final class Welcome extends Method
{
    public function execute()
    {
        return $this->message('core_welcome_box_info', [GDO_User::current()->displayName()]);
    }
}
