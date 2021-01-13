<?php
namespace GDO\User\Test;

use GDO\User\GDO_User;
use GDO\Core\Module_Core;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertEquals;

final class UserTest extends TestCase
{
    public function testSystemUser()
    {
        $u1 = Module_Core::instance()->cfgSystemUser();
        $u2 = GDO_User::system();
        $id = Module_Core::instance()->cfgSystemUserID();
        assertTrue($u1 === $u2);
        assertEquals($id, $u1->getID(), 'Test single identity cache with system user');
    }
    
    public function testGuestCreation()
    {
        $user = GDO_User::blank([
            'user_guest_name' => 'Wolfgang',
            'user_type' => 'guest',
        ])->insert();
        assertFalse($user->isMember(), 'Test if guests are non members.');
    }
    
}
