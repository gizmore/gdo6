<?php
namespace GDO\Core\Method;

use GDO\Core\GDT_Array;
use GDO\Core\Module_Core;
use GDO\Core\MethodAjax;
use GDO\Session\GDO_Session;

/**
 * Get current user json via ajax.
 * 
 * @author gizmore
 * @version 6.11.3
 * @since 6.4.0
 */
final class GetCookie extends MethodAjax
{
	public function execute()
	{
		$json = Module_Core::instance()->gdoUserJSON();
		
		$cookie = GDO_Session::$INSTANCE ? GDO_Session::$INSTANCE->cookieContent() : '';
		$data = [
			'user' => $json,
			'cookie' => $cookie,
			'attempt' => (int) @$_REQUEST['attempt'],
		];
		return GDT_Array::makeWith($data);
	}
	
}
