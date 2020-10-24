<?php
namespace GDO\Core;

/**
 * This table holds IPC shim data.
 * The IPC shim uses a DB table to communicate with other processes.
 * data is simply stored as a json message.
 * @see GDT_Hook
 * @see GWS_Server
 * @author gizmore@wechall.net
 * @version 6.10
 * @since 6.05
 */
final class GDO_Hook extends GDO
{
	public function gdoEngine() { return GDO::MYISAM; }
	
	public function gdoCached() { return false; }
	
	public function gdoColumns()
	{
		return array(
			GDT_JSON::make('hook_message')->notNull()->max(1024),
		);
	}
	
	public static function encodeHookMessage($event, array $args)
	{
		return json_encode(['event'=>$event, 'args' => $args]);
	}

}
