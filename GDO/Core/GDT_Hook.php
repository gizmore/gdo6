<?php
namespace GDO\Core;
/**
 * Hooks do not render any output.
 * Hooks add messages to the IPC queue 1, which are/can be consumed by the websocket server.
 *
 * Hooks follow this convetions.
 * 1) The hook name is camel-case, e.g: 'UserAuthenticated'.
 * 2) The method name in your object has to be hookUserAuthenticated in this example.
 * 3) The hook name should include the module name, e.g. LoginSuccess, FriendsAccepted.
 * 4) Annotate your hooks somewhere in your code with @hook LoginSuccess.
 *
 * @see Module_Websocket
 * 
 * Since v6.0, hooks are a gdo type and behave like a yielder displayhook. <?= GDT_Hook::make()->event('LeftBar')->render(); ?>
 *
 * @todo Find a way to generate hook lists for senders and receivers (dev informative). Maybe reflection for receiver and grep for sender
 *
 * @author gizmore
 * @version 6.05
 * @since 3.00
 */
final class GDT_Hook extends GDT
{
	public static $CALLS = 0;
	###########
	### API ###
	###########
	public static function renderHook($event, ...$args)
	{
		$hook = self::make()->event($event);
		if (count($args)) $hook->eventArgs(...$args);
		return $hook->render()->html;
	}

	#############
	### Event ###
	#############
	public $event;
	public function event($event=null) { $this->event = $event; return $this; }
	
	public $eventArgs;
	public function eventArgs(...$args) { $this->eventArgs = $args; return $this; }
	
	##############
	### Render ###
	##############
	public function render()
	{
		$response = GDT_Response::make('');
		$args = $this->eventArgs ? array_merge([$response], $this->eventArgs) : [$response];
		self::call($this->event, ...$args);
		return $response;
	}
	
	public function renderCell() { return $this->render()->html; }
	
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
	 * Simply try to call a function on all active modules.
	 * As on gwf5 all modules are always loaded, there is not much logic involved.
	 *
	 * @param string $event
	 * @param array $args
	 */
	private static function call($event, $ipc, array $args)
	{
		self::$CALLS++;
		$method_name = "hook$event";
		foreach (ModuleLoader::instance()->getModules() as $module)
		{
			if ($module->isEnabled())
			{
				if (method_exists($module, $method_name))
				{
					call_user_func([$module, $method_name], ...$args);
				}
			}
		}
		
		# Call IPC hooks
		if ( ($ipc) && (GWF_IPC) && 
			(!Application::instance()->isInstall()) && 
			(!Application::instance()->isCLI()) )
		{
			if ($ipc = self::ipc())
			{
				self::callIPC($ipc, $event, $args);
			}
		}
	}
	
	###########
	### IPC ###
	###########
	private static $ipc;
	public static function ipc()
	{
		if (!isset(self::$ipc))
		{
			$key = ftok(GWF_PATH.'temp/ipc.socket', 'G');
			self::$ipc = msg_get_queue($key);
		}
		return self::$ipc;
	}
	
	private static function callIPC($ipc, $event, array $args)
	{
		# Map GDO Objects to IDs.
		# The IPC Service will refetch the Objects on their end.
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
		
		Logger::logDebug("Called IPC event $event", false);
		
		# Send to IPC
		$error = 0;
		$result = msg_send($ipc, 0x612, array_merge([$event], $args), true, false, $error);
		if ( (!$result) || ($error) )
		{
			Logger::logError("IPC msg_send($event) failed with code $error");
			msg_remove_queue(self::$ipc);
			self::$ipc = null;
		}
	}
}
