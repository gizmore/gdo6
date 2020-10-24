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
use GDO\UI\GDT_Bar;

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
	##############
	### Module ###
	##############
	public $module_priority = 1;

	public $gdo_revision = '6.10-r9304'; # unused.
	
	public function isCoreModule() { return true; }
	
	public function getThemes() { return ['default']; }
	
	public function onLoadLanguage() { return $this->loadLanguage('lang/core'); }
	
	public function getClasses()
	{
		return array(
			'GDO\Core\GDO_Hook',
			'GDO\Core\GDO_Module',
			'GDO\Core\GDO_ModuleVar',
			'GDO\User\GDO_Permission',
		);
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
		return array(
			GDT_User::make('system_user')->editable(false),
			GDT_Checkbox::make('show_impressum')->initial('1'),
			GDT_Divider::make('div_javascript')->label('div_javascript'),
			GDT_Enum::make('minify_js')->enumValues('no', 'yes', 'concat')->initial('no'),
			GDT_Path::make('nodejs_path')->initial('nodejs')->label('nodejs_path'),
			GDT_Path::make('uglifyjs_path')->initial('uglifyjs')->label('uglifyjs_path'),
			GDT_Path::make('ng_annotate_path')->initial('ng-annotate')->label('ng_annotate_path'),
			GDT_Link::make('link_node_detect')->href(href('Core', 'DetectNode')),
			GDT_Version::make('asset_revision')->initial($this->module_version), # append this version to asset include urls?v=.
		);
	}
	
	/**
	 * @return GDO_User
	 */
	public function cfgSystemUser() { return $this->getConfigValue('system_user'); }
	public function cfgSystemUserID() { return $this->getConfigVar('system_user'); }
	public function cfgShowImpressum() { return $this->getConfigValue('show_impressum'); }
	public function cfgMinifyJS() { return $this->getConfigVar('minify_js'); }
	public function cfgNodeJSPath() { return $this->getConfigVar('nodejs_path'); }
	public function cfgUglifyPath() { return $this->getConfigVar('uglifyjs_path'); }
	public function cfgAnnotatePath() { return $this->getConfigVar('ng_annotate_path'); }
	public function cfgAssetVersion() { return sprintf('%.02f', $this->getConfigVar('asset_revision')); }
	
	#############
	### Hooks ###
	#############
	public function HookBottomBar(GDT_Bar $navbar)
	{
		if ($this->cfgShowImpressum())
		{
			$navbar->addField(GDT_Link::make()->label(t('link_impressum'))->href(href('Core', 'Impressum')));
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
		return sprintf("
window.GDO_CONFIG = {};
window.GWF_PROTOCOL = '%s'; window.GWF_DOMAIN = '%s'; window.GWF_WEB_ROOT = '%s';
window.GWF_LANGUAGE = '%s';",
			GWF_PROTOCOL, GWF_DOMAIN, GWF_WEB_ROOT, Trans::$ISO);
	}
	
	public function gdoUserJS()
	{
		$json = json_encode($this->gdoUserJSON());
		return "window.GDO_USER = new GDO_User($json);";
	}
	
	public function gdoUserJSON()
	{
		$user = GDO_User::current();
		return $user->renderJSON();
	}
}
