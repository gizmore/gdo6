<?php
namespace GDO\Core;

use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\Util\Javascript;
use GDO\User\GDT_User;
use GDO\Language\Trans;
use GDO\DB\GDT_Version;
use GDO\DB\GDT_Checkbox;
use GDO\User\GDO_Permission;
use GDO\UI\GDT_Page;
use GDO\File\FileUtil;
use GDO\User\GDO_UserPermission;

/**
 * The first module by priority, and it *HAS* to be installed for db driven sites,
 * simply because it installs the module table.
 * 
 * Also this module provides the default theme,
 * which is almost empty and is using the default tpl of the modules.
 * 
 * Very basic vanilla JS is loaded.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.0.0
 */
final class Module_Core extends GDO_Module
{
    /**
     * GDO6 revision string.
     * 6.11.0 will be the first stable version.
     * 6.12.0 will be the GIZ edition.
     * @var string
     */
	public static $GDO_REVISION = '6.10.4-r1177';

	##############
	### Module ###
	##############
	public $module_priority = 1;

	public function isCoreModule() { return true; }
	
	public function getTheme() { return 'default'; }
	
	public function onLoadLanguage() { return $this->loadLanguage('lang/core'); }
	
	public function thirdPartyFolders() { return ['/protected/', '/htmlpurifier/', '/bin/']; }
	
	public function getClasses()
	{
	    return [
	        GDO_Hook::class,
	        GDO_Module::class,
	        GDO_ModuleVar::class,
	        GDO_Permission::class,
	        GDO_User::class,
	        GDO_UserPermission::class,
	    ];
	}
	
	public function onInstall()
	{
	    FileUtil::createDir(GDO_PATH.'temp');
	    FileUtil::createDir(GDO_PATH.'temp/cache');
	    touch(GDO_PATH.'temp/ipc.socket');
	}
	
	##############
	### Config ###
	##############
	public function getConfig()
	{
		return [
			GDT_User::make('system_user')->editable(false)->initial('1'),
		    GDT_Checkbox::make('show_impressum')->initial($this->env('show_impressum', '0')),
		    GDT_Checkbox::make('show_privacy')->initial($this->env('show_privacy', '0')),
		    GDT_Checkbox::make('allow_guests')->initial($this->env('allow_guests', '1')),
			GDT_Version::make('asset_revision')->initial($this->module_version), # append this version to asset include urls?v=.
			GDT_Checkbox::make('siteshort_title_append')->initial('1'),
		    GDT_Checkbox::make('mail_404')->initial('1'),
		    GDT_Checkbox::make('load_sidebars')->initial('1'),
		];
	}
	
	/**
	 * @return GDO_User
	 */
	public function cfgSystemUser() { return $this->getConfigValue('system_user'); }
	public function cfgSystemUserID() { return $this->getConfigVar('system_user'); }
	public function cfgShowImpressum() { return $this->getConfigValue('show_impressum'); }
	public function cfgShowPrivacy() { return $this->getConfigValue('show_privacy'); }
	public function cfgAssetVersion() { return sprintf('%.02f', $this->getConfigVar('asset_revision')); }
	public function cfgAllowGuests() { return $this->getConfigValue('allow_guests'); }
	public function cfgSiteShortTitleAppend() { return $this->getConfigValue('siteshort_title_append'); }
	public function cfgMail404() { return $this->getConfigValue('mail_404'); }
	public function cfgLoadSidebars() { return $this->getConfigValue('load_sidebars'); }
	
	#############
	### Hooks ###
	#############
	public function onInitSidebar()
	{
	    $navbar = GDT_Page::$INSTANCE->bottomNav;
		if ($this->cfgShowImpressum())
		{
			$navbar->addField(GDT_Link::make('link_impressum')->href(href('Core', 'Impressum')));
		}
		if ($this->cfgShowPrivacy())
		{
		    $navbar->addField(GDT_Link::make('link_privacy')->href(href('Core', 'Privacy')));
		}
	}
	
	public function hookIgnoreDocsFiles(GDT_Array $ignore)
	{
	    $ignore->data[] = 'GDO/UI/htmlpurifier/**/*';
	}
	
	##################
	### Javascript ###
	##################
	public function onIncludeScripts()
	{
		$this->addCSS('css/gdo6-core.css');
		$this->addJavascript('js/gdo-string-util.js');
		$this->addJavascript('js/gdo-user.js');
		$this->addJavascript('js/gdo-core.js');
		Javascript::addJavascriptInline($this->gdoConfigJS());
		Javascript::addJavascriptInline($this->gdoUserJS());
	}

	/**
	 * Pretty print gdo config to JS.
	 * @return string
	 */
	public function gdoConfigJS()
	{
		return sprintf(
		    "window.GDO_CONFIG = {};
window.GDO_PROTOCOL = '%s';
window.GDO_DOMAIN = '%s';
window.GDO_WEB_ROOT = '%s';
window.GDO_LANGUAGE = '%s';",
			GDO_PROTOCOL, GDO_DOMAIN, GDO_WEB_ROOT, Trans::$ISO);
	}
	
	public function gdoUserJS()
	{
		$json = json_encode($this->gdoUserJSON(), JSON_PRETTY_PRINT);
		return "window.GDO_USER = new GDO_User($json);";
	}
	
	public function gdoUserJSON()
	{
		$user = GDO_User::current();
		return $user->renderJSON();
	}
	
}
