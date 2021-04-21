<?php
namespace GDO\Core;

use GDO\DB\GDT_AutoInc;

/**
 * This table holds IPC shim data.
 * The IPC shim uses a DB table to communicate with other processes.
 * data is simply stored as a json message.
 * @see GDT_Hook
 * @see GWS_Server
 * @author gizmore@wechall.net
 * @version 6.10.1
 * @since 6.5.0
 */
final class GDO_Hook extends GDO
{
	public function gdoEngine() { return GDO::MYISAM; }
	
	public function gdoCached() { return false; }
	
	public function gdoColumns()
	{
		return [
		    GDT_AutoInc::make('hook_id'),
			GDT_JSON::make('hook_message')->notNull()->max(2048),
		];
	}
	
	public static function encodeHookMessage($event, array $args)
	{
		return json_encode(['event'=>$event, 'args' => $args]);
	}

}
