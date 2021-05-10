<?php
namespace GDO\Core;

/**
 * Hooks do not render any output.
 * Hooks add messages to the IPC queue 1, which are/can be consumed by the websocket server.
 *
 * Hooks follow this convetions.
 * 1) The hook name is camel-case, e.g: 'UserAuthenticated'.
 * 2) The method name in your object has to be hookUserAuthenticated in this example.
 * 3) The hook name should include the module name, e.g. LoginSuccess, FriendsAccepted, CoreInitiated.
 * 4) Annotate your hooks somewhere in your code with @hook LoginSuccess.
 *
 * @see Module_Websocket
 * 
 * Since v6.0, hooks are a gdo type and behave like a yielder displayhook. <?= GDT_Hook::make()->event('LeftBar')->render(); ?>
 * The yielder was a bad ide and got removed in 6.10. hooks for navs and sections need to be called early.
 *
 * @author gizmore
 * @version 6.10.3
 * @since 3.0.0
 */
final class GDT_Hook extends GDT
{
	public static $CALLS = 0; # Num Hook calls.
	public static $IPC_CALLS = 0; # Num calls with additional IPC for websocket server.
// 	public static $CALL_NAMES = []; # called event names to hint in debugging/optimization.
	
	############
	### Init ###
	############
	/**
	 * @var [GDO_Module[]]
	 */
// 	private static $HOOKS = [];
	public static function initModule(GDO_Module $module)
	{
// 	    foreach (get_class_methods($module) as $m)
// 	    {
// 	        if ( ($m[0] == 'h') && ($m[1] == 'o') && ($m[2]=='o') && ($m[3] === 'k') )
// 	        {
// 	            self::initHook($m, $module);
// 	        }
// 	    }
	}
	
// 	private static function initHook($hookName, GDO_Module $module)
// 	{
// 	    if (!isset(self::$HOOKS[$hookName]))
// 	    {
// 	        self::$HOOKS[$hookName] = [];
// 	    }
// 	    self::$HOOKS[$hookName][] = $module;
// 	}
	
	###########
	### API ###
	###########
// 	public static function renderHook($event, ...$args)
// 	{
// 		$hook = self::make()->event($event);
// 		if (count($args)) $hook->eventArgs(...$args);
// 		return $hook->render()->html;
// 	}

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
		$args = $this->eventArgs ? array_merge([$response], $this->eventArgs) : [$response];
		$response = GDT_Response::newWith();
		self::call($this->event, $this->ipc, $args);
		return $response;
	}
	
	public function renderCell()
	{
	    return $this->render()->renderHTML();
	}
	
	##############
	### Engine ###
	##############
	public static function callWithIPC($event, ...$args)
	{
		return self::call($event, true, $args);
	}
	
	public static function callHook($event, ...$args)
	{
		return self::call($event, false, $args);
	}
	
    /**
	 * Call a hook.
	 * Only registered modules are called since 6.11.
	 * @param string $event
	 * @param boolean $ipc
	 * @param array $args
	 */
	private static function call($event, $ipc, array $args)
	{
	    # Perf
// 	    self::$CALL_NAMES[] = $event . ($ipc ? '+IPC' : '');
		self::$CALLS++;
		
		$method_name = "hook$event";
		
		/**
		 * @var $hooks GDO_Module[]
		 */
// 		if ($hooks = @self::$HOOKS[$method_name])
// 		{
//     		foreach ($hooks as $module)
//     		{
//     			call_user_func([$module, $method_name], ...$args);
//     		}
// 		}
// 		else
// 		{
// 		    self::$HOOKS[$method_name] = [];
		    foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		    {
		        if (method_exists($module, $method_name))
		        {
// 		            self::$HOOKS[$method_name][] = $module;
		            call_user_func([$module, $method_name], ...$args);
		        }
		    }
// 		}
        if (GDO_IPC_DEBUG)
        {
            Logger::log('hook', GDO_Hook::encodeHookMessage($event, $args));
        }

		# Call IPC hooks
		if ( ($ipc) && (GDO_IPC) && 
			(!Application::instance()->isInstall()) && 
			(!Application::instance()->isCLI()) )
		{
			self::$IPC_CALLS++;
			
			if (GDO_IPC_DEBUG)
			{
				Logger::log('ipc', GDO_Hook::encodeHookMessage($event, $args));
			}
			
			if (GDO_IPC === 'db')
			{
				self::callIPCDB($event, $args);
			}
			elseif ($ipc = self::QUEUE())
			{
				self::callIPC($ipc, $event, $args);
			}
		}
	}
	
	###########
	### IPC ###
	###########
	private static $QUEUE = null;
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
	
	private static function callIPC($ipc, $event, array $args)
	{
// 		Logger::logDebug("Called IPC event $event");
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
// 		Logger::logDebug("Called IPC DB event $event");
		$args = self::encodeIPCArgs($args);
		GDO_Hook::blank(array(
			'hook_message' => GDO_Hook::encodeHookMessage($event, $args),
		))->insert();
	}
	
}
