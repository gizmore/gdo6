<?php
namespace GDO\Core;

use GDO\DB\GDT_String;

/**
 * This table holds IPC shim data.
 * The IPC shim uses a DB table to communicate with other processes.
 * data is simply stored as a json message.
 * @see GDT_Hook
 * @see GWS_Server
 * @author gizmore@wechall.net
 */
final class GDO_Hook extends GDO
{
	public function gdoCached() { return false; }
	
	public function gdoColumns()
	{
		return array(
			GDT_String::make('hook_message')->notNull()->max(768),
		);
	}
	
	public static function encodeHookMessage($event, array $args)
	{
		return json_encode(array('event'=>$event, 'args' => $args));
	}

}