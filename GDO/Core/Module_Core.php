<?php
namespace GDO\Core;

use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\Javascript\Javascript;
use GDO\User\GDT_User;
use GDO\Language\Trans;
use GDO\DB\GDT_Version;
use GDO\DB\GDT_Checkbox;
use GDO\User\GDO_Permission;
use GDO\UI\GDT_Page;
use GDO\File\FileUtil;
use GDO\User\GDO_UserPermission;
use GDO\Date\GDO_Timezone;

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
 * @version 6.11.2
 * @since 6.0.0
 */
final class Module_Core extends GDO_Module
{
    /**
     * GDO6 revision string.
     * Sometimes just counts up to be in sync and poison some other module caches for updates.
     * Increase this value to poison all caches.
     * 
     * 6.11.2 will be the first stable version.
     * 6.12.0 will be the GIZ edition.
     * 
     * @var string
     */
	const GDO_REVISION = '6.11.2-r6288';

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
	    	GDO_Timezone::class,
	        GDO_User::class,
	        GDO_UserPermission::class,
	    ];
	}
	
	public function onInstall()
	{
	    FileUtil::createDir(GDO_PATH.'assets');
	    FileUtil::createDir(GDO_PATH.'temp');
	    FileUtil::createDir(GDO_PATH.'temp/cache');
	    FileUtil::createFile(GDO_PATH.'temp/ipc.socket');
	}
	
	##############
	### Config ###
	##############
	public function getConfig()
	{
		return [
			GDT_User::make('system_user')->editable(false)->initial('1'), # System user / id should be 1.
		    GDT_Checkbox::make('show_impressum')->initial($this->env('show_impressum', '0')), # show impressum in footer.
		    GDT_Checkbox::make('show_privacy')->initial($this->env('show_privacy', '0')), # show privacy link in footer.
		    GDT_Checkbox::make('allow_guests')->initial($this->env('allow_guests', '1')), # generally allow guests.
			GDT_Version::make('asset_revision')->initial($this->module_version), # append this version to asset include urls?v=.
			GDT_Checkbox::make('siteshort_title_append')->initial('1'),
		    GDT_Checkbox::make('mail_403')->initial('1'), # mail 403 error mails?
		    GDT_Checkbox::make('mail_404')->initial('1'), # mail 404 error mails?
		    GDT_Checkbox::make('load_sidebars')->initial('1'),
		    GDT_Checkbox::make('directory_indexing')->initial('0'),
		    GDT_Checkbox::make('module_assets')->initial('1'),
		];
	}
	
	/**
	 * @return GDO_User
	 */
	public function cfgSystemUser() { return $this->getConfigValue('system_user'); }
	public function cfgSystemUserID() { return $this->getConfigVar('system_user'); }
	public function cfgShowImpressum() { return $this->getConfigVar('show_impressum'); }
	public function cfgShowPrivacy() { return $this->getConfigVar('show_privacy'); }
	public function cfgAssetVersion() { return sprintf('%.02f', $this->getConfigVar('asset_revision')); }
	public function cfgAllowGuests() { return $this->getConfigVar('allow_guests'); }
	public function cfgSiteShortTitleAppend() { return $this->getConfigVar('siteshort_title_append'); }
	public function cfgMail403() { return $this->getConfigVar('mail_404'); }
	public function cfgMail404() { return $this->getConfigVar('mail_404'); }
	public function cfgLoadSidebars() { return $this->getConfigValue('load_sidebars'); }
	public function cfgDirectoryIndex() { return $this->getConfigVar('directory_indexing'); }
	public function cfgModuleAssets() { return $this->getConfigVar('module_assets'); }
	
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
		$this->addJS('js/gdo-string-util.js');
		$this->addJS('js/gdo-user.js');
		$this->addJS('js/gdo-core.js');
		Javascript::addJSPreInline($this->gdoConfigJS());
		Javascript::addJSPostInline($this->gdoUserJS());
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
window.GDO_LANGUAGE = '%s';
window.GDO_REVISION = '%s';
", GDO_PROTOCOL, GDO_DOMAIN,
   GDO_WEB_ROOT, Trans::$ISO,
   $this->nocacheVersion());
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

	public function checkAssetAllowance($url)
	{
	    if (stripos($url, 'GDO/') !== false)
	    {
    	    if (!$this->cfgModuleAssets())
    	    {
    	        $this->errorModuleAssetNotAllowed();
    	        return false;
    	    }
	    }
	    return true;
	}
	
	private function errorModuleAssetNotAllowed()
	{
	    return $this->error('err_properitary_asset_code');
	}
	
}
