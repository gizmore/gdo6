<?php
namespace GDO\Install;

use GDO\UI\GDT_Link;
use GDO\UI\GDT_Divider;
use GDO\DB\GDT_Enum;
use GDO\Form\GDT_Select;
use GDO\Form\GDT_Hidden;
use GDO\Util\Strings;
use GDO\Date\Time;
use GDO\DB\GDT_Checkbox;
use GDO\Util\Random;
use GDO\DB\GDT_Int;
use GDO\Net\GDT_Port;
use GDO\User\GDT_Realname;
use GDO\Mail\GDT_Email;
use GDO\DB\GDT_String;
use GDO\Core\GDT_Template;
use GDO\Core\Logger;
use GDO\DB\GDT_UInt;

/**
 * Configuration helper during install wizard.
 * Holds a set of method names for the steps
 * Autoconfigures GDO6 for when no config exists.
 * Holds fields for a configuration form.
 * @author gizmore
 * @since 6.0
 */
class Config
{
	####################
	### Method Steps ###
	####################
	public static function hrefStep($step) { return $_SERVER['SCRIPT_NAME'] . '?step=' . $step; }
	public static function linkStep($step) { return self::linkStepGDT($step)->renderCell(); }
	public static function linkStepGDT($step) { return GDT_Link::make("step$step")->href(self::hrefStep($step))->label("install_title_$step"); }
	public static function steps()
	{
		return array(
			'Welcome',
			'SystemTest',
			'Configure',
			'InstallModules',
			'InstallCronjob',
			'InstallAdmins',
			'InstallJavascript',
			'ImportBackup',
			'Security',
		);
	}
	
	#############################
	### Config File Generator ###
	#############################
	private static function detectServerSoftware()
	{
	    if (!isset($_SERVER['SERVER_SOFTWARE']))
	    {
	        return 'none';
	    }
	    
		$software = $_SERVER['SERVER_SOFTWARE'];
		if (stripos($software, 'Apache') !== false)
		{
			if (strpos($software, '2.4') !== false)
			{
				return 'apache2.4';
			}
			if (strpos($software, '2.2') !== false)
			{
				return 'apache2.2';
			}
			return 'apache2.4';
		}
		if (stripos($software, 'nginx') !== false)
		{
			return 'nginx';
			
		}
		return 'other';
	}
	
	public static function configure()
	{
		# Site
		if (!defined('GWF_SITENAME')) define('GWF_SITENAME', 'GDO6');
		if (!defined('GWF_SITECREATED')) define('GWF_SITECREATED', Time::getDate(microtime(true)));
		if (!defined('GWF_LANGUAGE')) define('GWF_LANGUAGE', 'en');
		if (!defined('GWF_TIMEZONE')) define('GWF_TIMEZONE', ini_get('date.timezone'));
		if (!defined('GWF_THEMES')) define('GWF_THEMES', '[default]');
		if (!defined('GWF_MODULE')) define('GWF_MODULE', 'Core');
		if (!defined('GWF_METHOD')) define('GWF_METHOD', 'Welcome');
		if (!defined('GWF_IPC')) define('GWF_IPC', 'none');
		if (!defined('GWF_IPC_DEBUG')) define('GWF_IPC_DEBUG', false);
		# HTTP
		if (!defined('GWF_DOMAIN')) define('GWF_DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
		if (!defined('GWF_SERVER')) define('GWF_SERVER', self::detectServerSoftware());
		if (!defined('GWF_PROTOCOL')) define('GWF_PROTOCOL', @$_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
		if (!defined('GWF_WEB_ROOT')) define('GWF_WEB_ROOT', Strings::substrTo($_SERVER['SCRIPT_NAME'], 'install/wizard.php'));
		# Files
		if (!defined('GWF_CHMOD')) define('GWF_CHMOD', 0770);
		# Logging
		if (!defined('GWF_ERROR_LEVEL')) define('GWF_ERROR_LEVEL', Logger::_DEFAULT);
		if (!defined('GWF_ERROR_STACKTRACE')) define('GWF_ERROR_STACKTRACE', true);
		if (!defined('GWF_ERROR_DIE')) define('GWF_ERROR_DIE', true);
		if (!defined('GWF_ERROR_MAIL')) define('GWF_ERROR_MAIL', false);
		# Database
		if (!defined('GWF_SALT')) define('GWF_SALT', Random::randomKey(16));
		if (!defined('GWF_DB_ENABLED')) define('GWF_DB_ENABLED', true);
		if (!defined('GWF_DB_HOST')) define('GWF_DB_HOST', 'localhost');
		if (!defined('GWF_DB_USER')) define('GWF_DB_USER', '');
		if (!defined('GWF_DB_PASS')) define('GWF_DB_PASS', '');
		if (!defined('GWF_DB_NAME')) define('GWF_DB_NAME', '');
		if (!defined('GWF_DB_DEBUG')) define('GWF_DB_DEBUG', false);
		# Cache
		if (!defined('GWF_MEMCACHE')) define('GWF_MEMCACHE', false);
		if (!defined('GWF_MEMCACHE_PREFIX')) define('GWF_MEMCACHE_PREFIX', '1_');
		if (!defined('GWF_MEMCACHE_HOST')) define('GWF_MEMCACHE_HOST', '127.0.0.1');
		if (!defined('GWF_MEMCACHE_PORT')) define('GWF_MEMCACHE_PORT', 61221);
		if (!defined('GWF_MEMCACHE_TTL')) define('GWF_MEMCACHE_TTL', 1800);
		# Cookies
		if (!defined('GWF_SESS_NAME')) define('GWF_SESS_NAME', 'GDO6');
		if (!defined('GWF_SESS_DOMAIN')) define('GWF_SESS_DOMAIN', GWF_DOMAIN);
		if (!defined('GWF_SESS_TIME')) define('GWF_SESS_TIME', Time::ONE_DAY*2);
		if (!defined('GWF_SESS_JS')) define('GWF_SESS_JS', true);
		if (!defined('GWF_SESS_HTTPS')) define('GWF_SESS_HTTPS', false);
		# Email
		if (!defined('GWF_ENABLE_EMAIL')) define('GWF_ENABLE_EMAIL', false);
		if (!defined('GWF_BOT_NAME')) define('GWF_BOT_NAME', GWF_SITENAME . ' support');
		if (!defined('GWF_BOT_EMAIL')) define('GWF_BOT_EMAIL', 'support@'.GWF_DOMAIN);
		if (!defined('GWF_ADMIN_EMAIL')) define('GWF_ADMIN_EMAIL', 'administrator@'.GWF_DOMAIN);
		if (!defined('GWF_ERROR_EMAIL')) define('GWF_ERROR_EMAIL', 'administrator@'.GWF_DOMAIN);
		if (!defined('GWF_DEBUG_EMAIL')) define('GWF_DEBUG_EMAIL', true);
	}
	
	public static function fields()
	{
		$themes = array_diff(GDT_Template::themeNames(), ['install']);
		return array(
			GDT_Hidden::make('configured')->var('1'),

			# Site
			GDT_Divider::make()->label('install_config_section_site'),
			GDT_String::make('sitename')->initialValue(GWF_SITENAME)->max(16)->label('cfg_sitename'),
			GDT_Hidden::make('sitecreated')->var(GWF_SITECREATED),
		    GDT_Enum::make('language')->enumValues('en', 'de')->initialValue(GWF_LANGUAGE)->required(),
		    GDT_String::make('timezone')->initialValue(GWF_TIMEZONE)->required(),
		    GDT_Select::make('themes')->multiple()->choices(array_combine($themes, $themes))->required()->initialValue(array('default')),
			GDT_String::make('module')->required()->initialValue(GWF_MODULE),
			GDT_String::make('method')->required()->initialValue(GWF_METHOD),
			GDT_Select::make('ipc')->choices(['db' => 'Database', '1' => 'IPC', '0' => 'none'])->initialValue(GWF_IPC),
			GDT_Checkbox::make('ipc_debug')->initialValue(GWF_IPC_DEBUG),
			# HTTP
			GDT_Divider::make()->label('install_config_section_http'),
			GDT_String::make('domain')->required()->initialValue(GWF_DOMAIN),
			GDT_Enum::make('server')->required()->enumValues('none', 'apache2.2', 'apache2.4', 'nginx', 'other')->initialValue(GWF_SERVER),
			GDT_Enum::make('protocol')->required()->enumValues('http', 'https')->initialValue(GWF_PROTOCOL),
			GDT_String::make('web_root')->required()->initialValue(GWF_WEB_ROOT),
			# Files
			GDT_Divider::make()->label('install_config_section_files'),
			GDT_Enum::make('chmod')->enumValues((string)0700, (string)0770, (string)0777)->initialValue(GWF_CHMOD),
			# Logging
			GDT_Divider::make()->label('install_config_section_logging'),
			GDT_Hidden::make('error_level')->initialValue(GWF_ERROR_LEVEL),
			GDT_Checkbox::make('error_stacktrace')->initialValue(GWF_ERROR_STACKTRACE),
			GDT_Checkbox::make('error_die')->initialValue(GWF_ERROR_DIE),
			GDT_Checkbox::make('error_mail')->initialValue(GWF_ERROR_MAIL),
			# Database
			GDT_Divider::make()->label('install_config_section_database'),
			GDT_Hidden::make('salt')->initialValue(GWF_SALT),
		    GDT_Checkbox::make('db_enabled')->initialValue(GWF_DB_ENABLED),
		    GDT_String::make('db_host')->initialValue(GWF_DB_HOST),
			GDT_String::make('db_user')->initialValue(GWF_DB_USER),
			GDT_String::make('db_pass')->initialValue(GWF_DB_PASS),
			GDT_String::make('db_name')->initialValue(GWF_DB_NAME),
//			 Text::make('db_prefix')->initialValue(GWF_DB_PREFIX)->required(),
			GDT_Checkbox::make('db_debug')->initialValue(GWF_DB_DEBUG),
			# Cache
			GDT_Divider::make()->label('install_config_section_cache'),
			GDT_Checkbox::make('memcache')->initialValue(GWF_MEMCACHE),
			GDT_String::make('memcache_prefix')->initialValue(GWF_MEMCACHE_PREFIX)->required(),
			GDT_String::make('memcache_host')->initialValue(GWF_MEMCACHE_HOST)->required(),
			GDT_Port::make('memcache_port')->initialValue(GWF_MEMCACHE_PORT)->required(),
			GDT_Int::make('memcache_ttl')->unsigned()->initialValue(GWF_MEMCACHE_TTL)->required(),
			# Cookies
			GDT_Divider::make()->label('install_config_section_cookies'),
			GDT_String::make('sess_name')->ascii()->caseS()->initialValue(GWF_SESS_NAME)->required(),
			GDT_Hidden::make('sess_domain')->initialValue(GWF_SESS_DOMAIN),
			GDT_UInt::make('sess_time')->initialValue(GWF_SESS_TIME)->required()->min(30),
			GDT_Checkbox::make('sess_js')->initialValue(GWF_SESS_JS),
			GDT_Checkbox::make('sess_https')->initialValue(GWF_SESS_HTTPS),
			# Email
			GDT_Divider::make()->label('install_config_section_email'),
		    GDT_Checkbox::make('enable_email')->initialValue(GWF_ENABLE_EMAIL),
		    GDT_Realname::make('bot_name')->required()->initialValue(GWF_BOT_NAME)->label('bot_name'),
			GDT_Email::make('bot_email')->required()->initialValue(GWF_BOT_EMAIL)->label('bot_mail'),
			GDT_Email::make('admin_email')->required()->initialValue(GWF_ADMIN_EMAIL)->label('admin_mail'),
			GDT_Email::make('error_email')->required()->initialValue(GWF_ERROR_EMAIL)->label('error_mail'),
			GDT_Checkbox::make('debug_email')->initialValue(GWF_DEBUG_EMAIL),
		);
	}
}
