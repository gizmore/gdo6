<?php
namespace GDO\Core;

use GDO\UI\GDT_Page;

/**
 * Admin method decorator trait.
 * 
 * @TODO: Add superuser pw check to admin methods.
 * @author gizmore
 * @version 6.10.3
 * @since 6.0.0
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
	
	public function renderNavBar()
	{
	    GDT_Page::$INSTANCE->topTabs->addField(
	        GDT_Template::templatePHP('Admin', 'navbar.php',
	            ['moduleName' => $this->getModuleName()]));
	}
	
	public function renderPermTabs()
	{
        GDT_Page::$INSTANCE->topTabs->addField(
            GDT_Template::templatePHP('Admin', 'perm_tabs.php'));
	}
	
}
