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
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.0.0
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
		    'CopyHTAccess',
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
		if (!defined('GDO_SITENAME')) define('GDO_SITENAME', 'GDO6');
		if (!defined('GDO_SITECREATED')) define('GDO_SITECREATED', Time::getDate(microtime(true)));
		if (!defined('GDO_LANGUAGE')) define('GDO_LANGUAGE', 'en');
		if (!defined('GDO_TIMEZONE')) define('GDO_TIMEZONE', ini_get('date.timezone'));
		if (!defined('GDO_THEMES')) define('GDO_THEMES', '[default]');
		if (!defined('GDO_MODULE')) define('GDO_MODULE', 'Core');
		if (!defined('GDO_METHOD')) define('GDO_METHOD', 'Welcome');
		if (!defined('GDO_SEO_URLS')) define('GDO_SEO_URLS', false);
		if (!defined('GDO_IPC')) define('GDO_IPC', 'none');
		if (!defined('GDO_IPC_DEBUG')) define('GDO_IPC_DEBUG', false);
		if (!defined('GDO_GDT_DEBUG')) define('GDO_GDT_DEBUG', false);
		# HTTP
		if (!defined('GDO_DOMAIN')) define('GDO_DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
		if (!defined('GDO_SERVER')) define('GDO_SERVER', self::detectServerSoftware());
		if (!defined('GDO_PROTOCOL')) define('GDO_PROTOCOL', @$_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
		if (!defined('GDO_WEB_ROOT')) define('GDO_WEB_ROOT', Strings::substrTo($_SERVER['SCRIPT_NAME'], 'install/wizard.php'));
		# Files
		if (!defined('GDO_CHMOD')) define('GDO_CHMOD', 0770);
		# Logging
		if (!defined('GDO_LOG_REQUEST')) define('GDO_LOG_REQUEST', false);
		if (!defined('GDO_CONSOLE_VERBOSE')) define('GDO_CONSOLE_VERBOSE', false);
		if (!defined('GDO_ERROR_LEVEL')) define('GDO_ERROR_LEVEL', Logger::_DEFAULT);
		if (!defined('GDO_ERROR_STACKTRACE')) define('GDO_ERROR_STACKTRACE', true);
		if (!defined('GDO_ERROR_DIE')) define('GDO_ERROR_DIE', true);
		if (!defined('GDO_ERROR_MAIL')) define('GDO_ERROR_MAIL', false);
		if (!defined('GDO_ERROR_TIMEZONE')) define('GDO_ERROR_TIMEZONE', 'UTC');

		# Database
		if (!defined('GDO_SALT')) define('GDO_SALT', Random::randomKey(16));
		if (!defined('GDO_DB_ENABLED')) define('GDO_DB_ENABLED', true);
		if (!defined('GDO_DB_HOST')) define('GDO_DB_HOST', 'localhost');
		if (!defined('GDO_DB_USER')) define('GDO_DB_USER', '');
		if (!defined('GDO_DB_PASS')) define('GDO_DB_PASS', '');
		if (!defined('GDO_DB_NAME')) define('GDO_DB_NAME', '');
		if (!defined('GDO_DB_DEBUG')) define('GDO_DB_DEBUG', false);
		# Cache
		if (!defined('GDO_FILECACHE')) define('GDO_FILECACHE', false);
		if (!defined('GDO_MEMCACHE')) define('GDO_MEMCACHE', false);
		if (!defined('GDO_MEMCACHE_HOST')) define('GDO_MEMCACHE_HOST', '127.0.0.1');
		if (!defined('GDO_MEMCACHE_PORT')) define('GDO_MEMCACHE_PORT', 61221);
		if (!defined('GDO_MEMCACHE_TTL')) define('GDO_MEMCACHE_TTL', 1800);
		# Cookies
		if (!defined('GDO_SESS_NAME')) define('GDO_SESS_NAME', 'GDO6');
		if (!defined('GDO_SESS_DOMAIN')) define('GDO_SESS_DOMAIN', GDO_DOMAIN);
		if (!defined('GDO_SESS_TIME')) define('GDO_SESS_TIME', Time::ONE_DAY*2);
		if (!defined('GDO_SESS_JS')) define('GDO_SESS_JS', true);
		if (!defined('GDO_SESS_HTTPS')) define('GDO_SESS_HTTPS', false);
		if (!defined('GDO_SESS_LOCK')) define('GDO_SESS_LOCK', GDO_DB_ENABLED);
		
		# Email
		if (!defined('GDO_ENABLE_EMAIL')) define('GDO_ENABLE_EMAIL', false);
		if (!defined('GDO_BOT_NAME')) define('GDO_BOT_NAME', GDO_SITENAME . ' support');
		if (!defined('GDO_BOT_EMAIL')) define('GDO_BOT_EMAIL', 'support@'.GDO_DOMAIN);
		if (!defined('GDO_ADMIN_EMAIL')) define('GDO_ADMIN_EMAIL', 'administrator@'.GDO_DOMAIN);
		if (!defined('GDO_ERROR_EMAIL')) define('GDO_ERROR_EMAIL', 'administrator@'.GDO_DOMAIN);
		if (!defined('GDO_DEBUG_EMAIL')) define('GDO_DEBUG_EMAIL', true);
	}
	
	public static function fields()
	{
		$themes = array_diff(GDT_Template::themeNames(), ['install']);
		return array(
		    GDT_Hidden::make('configured')->var('1'),
// 		    GDT_Hidden::make('secured')->var('1'),
		    
			# Site
			GDT_Divider::make()->label('install_config_section_site'),
			GDT_String::make('sitename')->initialValue(GDO_SITENAME)->max(16)->label('cfg_sitename'),
		    GDT_Checkbox::make('seo_urls')->initialValue(GDO_SEO_URLS),
			GDT_Hidden::make('sitecreated')->var(GDO_SITECREATED),
		    GDT_Enum::make('language')->enumValues('en', 'de')->initialValue(GDO_LANGUAGE)->required(),
		    GDT_String::make('timezone')->initialValue(GDO_TIMEZONE)->required(),
		    GDT_Select::make('themes')->multiple()->choices(array_combine($themes, $themes))->required()->initialValue(array('default')),
			GDT_String::make('module')->required()->initialValue(GDO_MODULE),
			GDT_String::make('method')->required()->initialValue(GDO_METHOD),
			GDT_Select::make('ipc')->choices(['db' => 'Database', '1' => 'IPC', '0' => 'none'])->initialValue(GDO_IPC),
		    GDT_Checkbox::make('ipc_debug')->initialValue(GDO_IPC_DEBUG),
		    GDT_Checkbox::make('gdt_debug')->initialValue(GDO_GDT_DEBUG),
		    # HTTP
			GDT_Divider::make()->label('install_config_section_http'),
			GDT_String::make('domain')->required()->initialValue(GDO_DOMAIN),
			GDT_Enum::make('server')->required()->enumValues('none', 'apache2.2', 'apache2.4', 'nginx', 'other')->initialValue(GDO_SERVER),
			GDT_Enum::make('protocol')->required()->enumValues('http', 'https')->initialValue(GDO_PROTOCOL),
			GDT_String::make('web_root')->required()->initialValue(GDO_WEB_ROOT),
			# Files
			GDT_Divider::make()->label('install_config_section_files'),
			GDT_Enum::make('chmod')->enumValues((string)0700, (string)0770, (string)0777)->initialValue(GDO_CHMOD),
			# Logging
			GDT_Divider::make()->label('install_config_section_logging'),
		    GDT_Checkbox::make('log_request')->initialValue(GDO_LOG_REQUEST),
		    GDT_Checkbox::make('console_verbose')->initialValue(GDO_CONSOLE_VERBOSE),
		    GDT_Hidden::make('error_level')->initialValue(GDO_ERROR_LEVEL),
			GDT_Checkbox::make('error_stacktrace')->initialValue(GDO_ERROR_STACKTRACE),
			GDT_Checkbox::make('error_die')->initialValue(GDO_ERROR_DIE),
		    GDT_Checkbox::make('error_mail')->initialValue(GDO_ERROR_MAIL),
		    GDT_Checkbox::make('error_mail')->initialValue(GDO_ERROR_MAIL),
		    # Database
			GDT_Divider::make()->label('install_config_section_database'),
			GDT_Hidden::make('salt')->initialValue(GDO_SALT),
		    GDT_Checkbox::make('db_enabled')->initialValue(GDO_DB_ENABLED),
		    GDT_String::make('db_host')->initialValue(GDO_DB_HOST),
			GDT_String::make('db_user')->initialValue(GDO_DB_USER),
			GDT_String::make('db_pass')->initialValue(GDO_DB_PASS),
			GDT_String::make('db_name')->initialValue(GDO_DB_NAME),
//			 Text::make('db_prefix')->initialValue(GDO_DB_PREFIX)->required(),
			GDT_Checkbox::make('db_debug')->initialValue(GDO_DB_DEBUG),
			# Cache
			GDT_Divider::make()->label('install_config_section_cache'),
		    GDT_Checkbox::make('filecache')->initialValue(GDO_FILECACHE),
		    GDT_Checkbox::make('memcache')->initialValue(GDO_MEMCACHE),
		    GDT_String::make('memcache_host')->initialValue(GDO_MEMCACHE_HOST)->required(),
			GDT_Port::make('memcache_port')->initialValue(GDO_MEMCACHE_PORT)->required(),
			GDT_Int::make('memcache_ttl')->unsigned()->initialValue(GDO_MEMCACHE_TTL)->required(),
			# Cookies
			GDT_Divider::make()->label('install_config_section_cookies'),
			GDT_String::make('sess_name')->ascii()->caseS()->initialValue(GDO_SESS_NAME)->required(),
			GDT_Hidden::make('sess_domain')->initialValue(GDO_SESS_DOMAIN),
			GDT_UInt::make('sess_time')->initialValue(GDO_SESS_TIME)->required()->min(30),
			GDT_Checkbox::make('sess_js')->initialValue(GDO_SESS_JS),
		    GDT_Checkbox::make('sess_https')->initialValue(GDO_SESS_HTTPS),
		    GDT_Checkbox::make('sess_lock')->initialValue(GDO_SESS_LOCK),
		    # Email
			GDT_Divider::make()->label('install_config_section_email'),
		    GDT_Checkbox::make('enable_email')->initialValue(GDO_ENABLE_EMAIL),
		    GDT_Realname::make('bot_name')->required()->initialValue(GDO_BOT_NAME)->label('bot_name'),
			GDT_Email::make('bot_email')->required()->initialValue(GDO_BOT_EMAIL)->label('bot_mail'),
			GDT_Email::make('admin_email')->required()->initialValue(GDO_ADMIN_EMAIL)->label('admin_mail'),
			GDT_Email::make('error_email')->required()->initialValue(GDO_ERROR_EMAIL)->label('error_mail'),
			GDT_Checkbox::make('debug_email')->initialValue(GDO_DEBUG_EMAIL),
		);
	}
	
}
