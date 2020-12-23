<?php
namespace GDO\Core;

use GDO\DB\GDT_Enum;
use GDO\File\GDT_Path;
use GDO\UI\GDT_Divider;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\Util\Javascript;
use GDO\User\GDT_User;
use GDO\Language\Trans;
use GDO\DB\GDT_Version;
use GDO\DB\GDT_Checkbox;
use GDO\User\GDO_Permission;
use GDO\UI\GDT_Page;

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
 * @version 6.10
 * @since 6.00
 */
final class Module_Core extends GDO_Module
{
	public $gdo_revision = '6.10-r9413'; # 6.11 will be the first stable. 6.12 will be the Gi2 edition :)

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
	    ];
	}
	
	public function onInstall()
	{
		touch(GDO_PATH.'temp/ipc.socket');
	}
	
	##############
	### Config ###
	##############
	public function getConfig()
	{
		return [
			GDT_User::make('system_user')->editable(false)->initial($this->env('system_user')),
		    GDT_Checkbox::make('show_impressum')->initial($this->env('show_impressum', '0')),
		    GDT_Checkbox::make('show_privacy')->initial($this->env('show_privacy', '0')),
		    GDT_Divider::make('div_javascript')->label('div_javascript'),
			GDT_Enum::make('minify_js')->enumValues('no', 'yes', 'concat')->initial($this->env('minify_js', 'no')),
			GDT_Path::make('nodejs_path')->initial($this->env('nodejs_path', 'nodejs'))->label('nodejs_path'),
		    GDT_Path::make('uglifyjs_path')->initial($this->env('uglifyjs_path', 'uglifyjs'))->label('uglifyjs_path'),
		    GDT_Path::make('ng_annotate_path')->initial($this->env('ng_annotate_path', 'ng-annotate'))->label('ng_annotate_path'),
			GDT_Link::make('link_node_detect')->href(href('Core', 'DetectNode')),
			GDT_Version::make('asset_revision')->initial($this->module_version), # append this version to asset include urls?v=.
		];
	}
	
	/**
	 * @return GDO_User
	 */
	public function cfgSystemUser() { return $this->getConfigValue('system_user'); }
	public function cfgSystemUserID() { return $this->getConfigVar('system_user'); }
	public function cfgShowImpressum() { return $this->getConfigValue('show_impressum'); }
	public function cfgShowPrivacy() { return $this->getConfigValue('show_privacy'); }
	public function cfgMinifyJS() { return $this->getConfigVar('minify_js'); }
	public function cfgNodeJSPath() { return $this->getConfigVar('nodejs_path'); }
	public function cfgUglifyPath() { return $this->getConfigVar('uglifyjs_path'); }
	public function cfgAnnotatePath() { return $this->getConfigVar('ng_annotate_path'); }
	public function cfgAssetVersion() { return sprintf('%.02f', $this->getConfigVar('asset_revision')); }
	
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
	
	public function gdoConfigJS()
	{
		return sprintf("window.GDO_CONFIG = {}; window.GWF_PROTOCOL = '%s'; window.GWF_DOMAIN = '%s'; window.GWF_WEB_ROOT = '%s'; window.GWF_LANGUAGE = '%s';",
			GWF_PROTOCOL, GWF_DOMAIN, GWF_WEB_ROOT, Trans::$ISO);
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
