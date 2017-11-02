<?php
namespace GDO\Core;
/**
 * Admin method decorator trait.
 * @TODO: Add superuser pw check to admin methods.
 * @author gizmore
 * @version 6.00
 */
trait MethodAdmin
{
    public function getPermission()
    {
        return 'admin';
    }
    
    public function renderNavBar($module=null)
	{
		return GDT_Template::responsePHP('Admin', 'navbar.php', ['moduleName' => $module]);
	}

	public function renderPermTabs($module=null)
	{
		return $this->renderNavBar($module)->add(GDT_Template::responsePHP('Admin', 'perm_tabs.php'));
	}
}
