<?php
namespace GDO\Core;
use GDO\Form\GDT_Form;
/**
 * Hooks do not render any output.
 * Hooks add messages to the IPC queue 1, which are/can be consumed by the websocket server.
 *
 * Hooks follow this convetions.
 * 1) The hook name is camel-case, e.g: 'UserAuthenticated'.
 * 2) The hook name shall include the module name, e.g. LoginSuccess
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
    /**
     * Simply try to call a function on all active modules.
     * As on gwf5 all modules are always loaded, there is not much logic involved.
     *
     * @param string $event
     * @param array $args
     */
    public static function call($event, ...$args)
    {
        self::$CALLS++;
        $method_name = "hook$event";
        foreach (ModuleLoader::instance()->getModules() as $module)
        {
            if (method_exists($module, $method_name))
            {
                call_user_func([$module, $method_name], ...$args);
            }
        }
        
        # Call IPC hooks
        if ( (GWF_IPC) && (!Application::instance()->isInstall()) )
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
            self::$ipc = msg_get_queue(1);
        }
        return self::$ipc;
    }
    
    private static function callIPC($ipc, $event, array $args=null)
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
                elseif ($arg instanceof GDT_Form)
                {
                    return; # SKIP GDT_Form hooks, as they enrich forms only,
                    # which is currently not required on websocket IPC channels.
                }
            }
        }
        
        # Send to IPC
        msg_send($ipc, 1, [$event, $args]);
    }
}
