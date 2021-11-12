<?php
namespace GDO\Core;

use GDO\DB\Cache;

/**
 * Hooks do not render any output.
 * Hooks add messages to the IPC queue 1, which are/can be consumed by the websocket server.
 *
 * Hooks follow this convetions.
 * 1) The hook name is camel-case, e.g: 'UserAuthenticated'.
 * 2) The method name in your object has to be hookUserAuthenticated in this example.
 * 3) The hook name should include the module name, e.g. LoginSuccess, FriendsAccepted, CoreInitiated.
 *
 * @see Module_Websocket
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 3.0.0
 */
final class GDT_Hook extends GDT
{
	# Hook cache key
	const CACHE_KEY = 'GD2T_HO2KS_';

	# Performance counter
	public static $CALLS = 0;     # Num Hook calls.
	public static $IPC_CALLS = 0; # Num Hook calls with additional IPC overhead for CLI process sync.
	
	########################
	### Hook event names ###
	########################
	#######################################
	#######################################
// 	/**
// 	 * @see GDO_User
// 	 * @see GDO_UserActivation
// 	 * @var string
// 	 */
// 	const USER_ACTIVATED = 'UserActivated';
	
// 	/**
// 	 * A user setting has been changed.
// 	 * @see GDO_Module
// 	 * @see GDO_User
// 	 * array $changes
// 	 * @var string
// 	 */
// 	const USER_SETTING_SAVED = 'UserSettingSaved';
	
// 	/**
// 	 * Module_Docs asks which files can be ignored in the build process.
// 	 * @see GDT_Array
// 	 * @var string
// 	 */
// 	const IGNORE_DOC_FILES = 'IgnoreDocsFiles';

// 	/**
// 	 * A GDO entity has been removed from the cache.
// 	 * @see string classname
// 	 * @see string id
// 	 * @var string
// 	 */
// 	const CACHE_INVALIDATE = 'CacheInvalidate';
	
// 	/**
// 	 * Demografic data has been changed for a user.
// 	 * @see GDO_User
// 	 * @var string
// 	 */
// 	const ACCOUNT_CHANGED = 'AccountChanged';
	
// 	/**
// 	 * @see GDO_User
// 	 * @var string
// 	 */
// 	const USER_DELETED = 'UserDeleted';
	
// 	/**
// 	 * No parameters.
// 	 * @var string
// 	 */
// 	const CLEAR_CACHE = 'ClearCache';
	
// 	/**
// 	 * @see GDO_Module
// 	 * @var string
// 	 */
// 	const MODULE_VARS_CHANGED = 'ModuleVarsChanged';
	
// 	/**
// 	 * @see GDT_Bar
// 	 * @var string
// 	 */
// 	const ADMIN_BAR = 'AdminBar';
	
// 	/**
// 	 * Avatar has been changed.
// 	 * @see GDO_User
// 	 * @var string
// 	 */
// 	const AVATAR_SET = 'AvatarSet';
	
// 	const BACKUP_IMPORTED = 'BackupImported';
	
// 	/**
// 	 * @see GDO_Comment
// 	 * @var string
// 	 */
// 	const COMMENT_APPROVED = 'CommentApproved';
	
// 	/**
// 	 * @see GDO_Comment
// 	 * @var string
// 	 */
// 	const COMMENT_DELETED = 'CommentDeleted';
	
// 	/**
// 	 * @see GDO_Comment
// 	 * @var string
// 	 */
// 	const COMMENT_ADDED = 'CommentAdded';
	
// 	/**
// 	 * @see Method
// 	 * @see GDT_Response
// 	 * @var string
// 	 */
// 	const BEFORE_EXECUTE = 'BeforeExecute';
	
// 	/**
// 	 * @see Method
// 	 * @see GDT_Response
// 	 * @var string
// 	 */
// 	const AFTER_EXECUTE = 'AfterExecute';
	
// 	/**
// 	 * @see GDO_User
// 	 * @see GDO_Download
// 	 * @var string
// 	 */
// 	const DOWNLOAD_FILE = 'DownloadFile';
	
// 	/**
// 	 * @see GDO_User
// 	 * @see string FB-Nickname
// 	 * @var unknown
// 	 */
// 	const FB_USER_ACTIVATED = 'FBUserActivated';
	
// 	/**
// 	 * @see int followerUserId
// 	 * @see int followingUserId
// 	 * @var string
// 	 */
// 	const FOLLOWER_FOLLOW = 'FollowerFollow';
	
// 	/**
// 	 * @see GDO_ForumPost
// 	 * @var string
// 	 */
// 	const FORUM_POST_CREATED = 'ForumPostCreated';
	
// 	/**
// 	 * @see GDT_Card
// 	 * @see GDT_Container
// 	 * @see GDO_User
// 	 * @var string
// 	 */
// 	const DECORATE_POST_USER = 'DecoratePostUser';
	
// 	/**
// 	 * @see GDO_ForumThread
// 	 * @see GDO_ForumPost
// 	 * @var string
// 	 */
// 	const FORUM_ACTIVITY = 'ForumActivity';
	
// 	/**
// 	 * @see int acceptorUserId
// 	 * @see int friendUserId
// 	 * @var string
// 	 */
// 	const FRIENDS_ACCEPT = 'FriendsAccept';
	
// 	/**
// 	 * @see int userId
// 	 * @see int friendId
// 	 * @var string
// 	 */
// 	const FRIENDS_REMOVE = 'Friends_Remove';
	
// 	/**
// 	 * @see GDO_FriendRequest
// 	 * @var string
// 	 */
// 	const FRIENDS_REQUEST = 'FriendsRequest';
	
// 	/**
// 	 * @see GDO_User
// 	 * @see string accessToken
// 	 * @see mixed $data
// 	 * @var string
// 	 */
// 	const IG_USER_ACTIVATED = 'IGUserActivated';
// 	const IG_USER_AUTHENTICATED = 'IGUserAuthenticated';
	
// 	/**
// 	 * @see GDO_User invitor
// 	 * @see GDO_User newMember
// 	 * @var string
// 	 */
// 	const INVITE_COMPLETED = 'InviteCompleted';
	
// 	/**
// 	 * @see LUP_Room
// 	 * @var string
// 	 */
// 	const LUP_ROOM_ADDED = 'LUPRoomAdded';
// 	const LOGIN_FORM = 'LoginForm';
// 	const USER_AUTHENTICATED = 'UserAuthenticated';
// 	const BEFORE_LOGOUT = 'BeforeLogout';
// 	const USER_LOGGED_OUT = 'UserLoggedOut';
// 	const PM_SENT = 'PMSent';
// 	const CANCEL_ORDER = 'CancelOrder';
// 	const PROFILE_CARD = 'ProfileCard';
// 	const RECOVERY_FORM = 'RecoveryForm';
// 	const ALREADY_ACTIVATED = 'AlreadyActivated';
// 	const REGISTER_FORM = 'RegisterForm';
// 	const ON_REGISTER = 'OnRegister';
// 	const GUEST_FORM = 'GuestForm';
// 	const USER_PERMISSION_GRANTED = 'UserPermissionGranted';
// 	const BEFORE_REQUEST = 'BeforeRequest';
// 	const AFTER_REQUEST = 'AfterRequest';
	#######################################
	#######################################
	
	#############
	### Hooks ###
	#############
	/**
	 * Maps hook event names to array of module ids.
	 * Loaded from memcached/fs.
	 * @version 6.10.6
	 * @since 6.10.6
	 * @var string[]
	 */
	private static $HOOKS = [
// 		self::USER_ACTIVATED => [],
// 		self::USER_SETTING_SAVED => [],
// 		self::USER_ACTIVATED => [],
// 		self::USER_SETTING_SAVED => [],
// 		self::IGNORE_DOC_FILES => [],
// 		self::CACHE_INVALIDATE => [],
// 		self::ACCOUNT_CHANGED => [],
// 		self::USER_DELETED => [],
// 		self::CLEAR_CACHE => [],
// 		self::MODULE_VARS_CHANGED => [],
// 		self::ADMIN_BAR => [],
// 		self::AVATAR_SET => [],
// 		self::BACKUP_IMPORTED => [],
// 		self::COMMENT_APPROVED => [],
// 		self::COMMENT_DELETED => [],
// 		self::COMMENT_ADDED => [],
// 		self::BEFORE_EXECUTE => [],
// 		self::AFTER_EXECUTE => [],
// 		self::DOWNLOAD_FILE => [],
// 		self::FB_USER_ACTIVATED => [],
// 		self::FOLLOWER_FOLLOW => [],
// 		self::FORUM_POST_CREATED => [],
// 		self::DECORATE_POST_USER => [],
// 		self::FORUM_ACTIVITY => [],
// 		self::FRIENDS_ACCEPT => [],
// 		self::FRIENDS_REMOVE => [],
// 		self::FRIENDS_REQUEST => [],
// 		self::IG_USER_ACTIVATED => [],
// 		self::IG_USER_AUTHENTICATED => [],
// 		self::INVITE_COMPLETED => [],
// 		self::LUP_ROOM_ADDED => [],
// 		self::LOGIN_FORM => [],
// 		self::USER_AUTHENTICATED => [],
// 		self::BEFORE_LOGOUT => [],
// 		self::USER_LOGGED_OUT => [],
// 		self::PM_SENT => [],
// 		self::CANCEL_ORDER => [],
// 		self::PROFILE_CARD => [],
// 		self::RECOVERY_FORM => [],
// 		self::ALREADY_ACTIVATED => [],
// 		self::REGISTER_FORM => [],
// 		self::ON_REGISTER => [],
// 		self::GUEST_FORM => [],
// 		self::USER_PERMISSION_GRANTED => [],
// 		self::BEFORE_REQUEST => [],
// 		self::AFTER_REQUEST => [],
	];
	
	############
	### Init ###
	############
// 	private static $INITED = false;
// 	public static function &getHooksCached($event)
// 	{
// 		if (!self::$INITED)
// 		{
// 			self::init();
// 			self::$INITED = true;
// 		}
// 		return self::$HOOKS[$event];
// 	}
	
	public static function init($event)
	{
		if (!($cache = @self::$HOOKS[$event]))
		{
			$key = self::CACHE_KEY . $event;
			if ($cache = Cache::get($key))
			{
				self::$HOOKS[$event] = $cache;
			}
			elseif ($cache = Cache::fileGetSerialized($key))
			{
				Cache::set($key, $cache, GDO_MEMCACHE_TTL);
				self::$HOOKS[$event] = $cache;
			}
			elseif ($cache = self::buildHookCache($event))
			{
				Cache::set($key, $cache, GDO_MEMCACHE_TTL);
				Cache::fileSetSerialized($key, $cache);
				self::$HOOKS[$event] = $cache;
			}
		}
		return $cache;
	}


	/**
	 * Examine all modules for hook functions.
	 * Hooks have to been made known to the GDT_Hook::$CACHE
	 * @return string[]
	 */
	private static function &buildHookCache($event)
	{
		$modules = ModuleLoader::instance()->getEnabledModules();
// 		foreach (array_keys(self::$HOOKS) as $event)
// 		{
			self::$HOOKS[$event] = [];
			$method_name = "hook{$event}";
			foreach ($modules as $module)
			{
				if (method_exists($module, $method_name))
				{
					self::$HOOKS[$event][] = $module->getID();
				}
			}
// 		}
		return self::$HOOKS[$event];
	}
	
	#############
	### Event ###
	#############
	public function hook($event, ...$args)
	{
	    $this->eventArgs = $args;
	    return $this->event($event);
	}
	
	public $event;
	public function event($event=null)
	{
	    $this->event = $event;
	    return $this->name($event);
	}
	
	public $eventArgs;
	public function eventArgs(...$args)
	{
	    $this->eventArgs = $args;
	    return $this;
	}
	
	public $ipc;
	public function ipc($ipc=true)
	{
	    $this->ipc = $ipc;
	    return $this;
	}
	
	##############
	### Render ###
	##############
	public function render()
	{
		$response = GDT_Response::newWith();
		$args = $this->eventArgs ? array_merge([$response], $this->eventArgs) : [$response];
		$res2 = self::call($this->event, $this->ipc, $args);
		return $response->addField($res2);
	}
	
	public function renderCell()
	{
	    return $this->render()->renderHTML();
	}
	
	##############
	### Engine ###
	##############
	public static function callHook($event, ...$args)
	{
		return self::call($event, false, $args);
	}
	
	public static function callWithIPC($event, ...$args)
	{
		return self::call($event, true, $args);
	}
	
	###############
	### Private ###
	###############
    /**
	 * Call a hook.
	 * Only registered modules are called since 6.10.6
	 * @param string $event
	 * @param boolean $ipc
	 * @param array $args
	 */
	private static function call($event, $ipc, array $args)
	{
		$app = Application::instance();
		if ($app->isWebServer())
		{
			$response = self::callWebHooks($event, $args);
			if (GDO_IPC && $ipc)
			{
				if ($r2 = self::callIPCHooks($event, $args))
				{
					$response->addField($r2);
				}
			}
			return $response;
		}
	}
	
	/**
	 * Call hook on all signed modules.
	 * @param string $event
	 * @param array $args
	 * @return GDT_Response
	 */
	private static function callWebHooks($event, array $args)
	{
		# Count num calls up.
		self::$CALLS++;
		
		# Call hook for this HTTP/www process.
		$moduleIds = self::init($event);
		$response = GDT_Response::make();
		$method_name = "hook$event";
		$loader = ModuleLoader::instance();
// 		foreach ($moduleIds as $id)
// 		{
			foreach ($moduleIds as $id)
			{
				if ($module = $loader->getModuleByID($id))
				{
					$callable = [$module, $method_name];
					if ($result = call_user_func_array($callable, $args))
					{
						$response->addField($result);
					}
				}
			}
// 		}
		return $response;
	}
	
	private static function callIPCHooks($event, $args)
	{
		self::$IPC_CALLS++;
		
		if (GDO_IPC_DEBUG)
		{
			Logger::log('ipc', self::encodeHookMessage($event, $args));
		}
		
		if (GDO_IPC === 'db')
		{
			self::callIPCDB($event, $args);
		}
		elseif ($ipc = self::QUEUE())
		{
			self::callIPCQueue($ipc, $event, $args);
		}
	}
	
	###########
	### IPC ###
	###########
	private static $QUEUE;
	public static function QUEUE()
	{
		if (!self::$QUEUE)
		{
			$key = ftok(GDO_PATH . 'temp/ipc.socket', 'G');
			self::$QUEUE = msg_get_queue($key);
		}
		return self::$QUEUE;
	}
	
	/**
	 * Map GDO Objects to IDs.
	 * The IPC Service will refetch the Objects on their end.
	 * @param array $args
	 */
	private static function encodeIPCArgs(array $args)
	{
		if ($args)
		{
			foreach ($args as $k => $arg)
			{
				if ($arg instanceof GDO)
				{
					$args[$k] = $arg->getID();
				}
			}
		}
		return $args;
	}
	
	private static function callIPCQueue($ipc, $event, array $args)
	{
		$args = self::encodeIPCArgs($args);
		
		# Send to IPC
		$error = 0;
		$result = @msg_send($ipc, 0x612, array_merge([$event], $args), true, false, $error);
		if ( (!$result) || ($error) )
		{
			Logger::logError("IPC msg_send($event) failed with code $error");
			msg_remove_queue(self::$QUEUE);
			self::$QUEUE = null;
		}
	}

	/**
	 * Sends a message to another process via the db.
	 * @param string $event
	 * @param array $args
	 */
	private static function callIPCDB($event, array $args)
	{
		$args = self::encodeIPCArgs($args);
		GDO_Hook::blank([
			'hook_message' => self::encodeHookMessage($event, $args),
		])->insert();
	}
	
	private static function encodeHookMessage($event, array $args)
	{
		return json_encode([
			'event' => $event,
			'args' => $args,
		]);
	}
	
}

# Bootstrap on first usage.
// GDT_Hook::init();
