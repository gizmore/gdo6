<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\User\GDO_User;
use GDO\User\GDT_Username;
use GDO\Core\GDT_Response;

/**
 * Print your username.
 * @author gizmore
 * @version 6.10.4
 * @since 6.10.1
 */
final class WhoAmI extends Method
{
    public function showInSitemap() { return false; }
    
    public function execute()
    {
        return GDT_Response::makeWith(
            GDT_Username::make()->gdo(GDO_User::current()));
    }
    
}
