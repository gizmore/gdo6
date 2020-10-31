<?php
namespace GDO\Core;
use GDO\UI\GDT_Page;

/**
 * Admin method decorator trait.
 * @TODO: Add superuser pw check to admin methods.
 * @author gizmore
 * @version 6.10
 */
trait MethodAdmin
{
	public function getPermission()
	{
		return 'admin';
	}
	
	public function beforeExecute()
	{
	    $this->renderNavBar();
	}
	
	public function renderNavBar($module=null)
	{
	    GDT_Page::$INSTANCE->topTabs->addField(GDT_Template::responsePHP('Admin', 'navbar.php', ['moduleName' => $module]));
	}

	public function renderPermTabs($module=null)
	{
	    GDT_Page::$INSTANCE->topTabs->addField(GDT_Template::responsePHP('Admin', 'perm_tabs.php'));
	}
	
}
