<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\Util\Strings;

/**
 * Hooks can add messages to the IPC queue, which are/can be consumed by the websocket server.
 *
 * Hooks follow these convetions.
 * 
 * 1) The hook name is camel-case, e.g: 'UserAuthenticated'.
 * 2) The method name in your object has to be hookUserAuthenticated in this example.
 * 3) The hook name should include the module name, e.g. LoginSuccess, FriendsAccepted, CoreInitiated.
 *
 * @TODO: write an event system for games.
 *
 * @see Module_Websocket
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 3.0.0
 */
final class GDT_Hook extends GDT
{
	# Hook cache key
	const CACHE_KEY = 'HOOKS_';
	
	/**
	 * @var string[string[]]
	 */
	private static $CACHE = null;
	
	# Performance counter
	public static $CALLS = 0;     # Num Hook calls.
	public static $IPC_CALLS = 0; # Num Hook calls with additional IPC overhead for CLI process sync.
	
	###########
	### GDT ###
	###########
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
	
	public $ipc = false;
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
		self::init();
		
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

		# Add to global response
		$response = GDT_Response::make();
		
		# Call hooks for this HTTP/www process.
		if ($moduleNames = self::getHookModuleNames($event))
		{
			$method_name = "hook$event";
			$loader = ModuleLoader::instance();
			foreach ($moduleNames as $moduleName)
			{
				if ($module = $loader->getModule($moduleName))
				{
					if ($module->isEnabled())
					{
						$callable = [$module, $method_name];
						if ($result = call_user_func_array($callable, $args))
						{
							$response->addField($result);
						}
					}
				}
			}
		}
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
		foreach ($args as $k => $arg)
		{
			if ($arg instanceof GDO)
			{
				$args[$k] = $arg->getID();
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
	
	############
	### Init ###
	############
	/**
	 * Initialize the hooks from filesystem cache.
	 */
	public static function init()
	{
		if (self::$CACHE === null)
		{
			if ($hooks = Cache::fileGetSerialized(self::CACHE_KEY))
			{
				self::$CACHE = $hooks;
			}
			else
			{
				self::$CACHE = self::buildHookCache();
				Cache::fileSetSerialized(self::CACHE_KEY, self::$CACHE);
			}
		}
	}
	
	private static function getHookModuleNames($event)
	{
		if (isset(self::$CACHE[$event]))
		{
			return self::$CACHE[$event];
		}
	}
	
	/**
	 * Loop through all enabled modules and their methods.
	 * A methodname starting with hook adds to the hook table.
	 * 
	 * @return array<string, string[]>
	 */
	private static function buildHookCache()
	{
		$cache = [];
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			$classname = $module->gdoRealClassName();
			$methods = get_class_methods($classname);
			foreach ($methods as $methodName)
			{
				if (Strings::startsWith($methodName, 'hook'))
				{
					$event = substr($methodName, 4);
					if (!isset($cache[$event]))
					{
						$cache[$event] = [$module->getName()];
					}
					else
					{
						$cache[$event][] = $module->getName();
					}
				}
			}
		}
		return $cache;
	}

}
