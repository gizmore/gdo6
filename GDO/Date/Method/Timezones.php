<?php
namespace GDO\Date\Method;

use GDO\Core\MethodAjax;
use Nette\Utils\DateTime;
use GDO\Core\GDT_Array;
use GDO\Date\GDO_Timezone;

/**
 * Get all timezones and offsets via ajax.
 * 
 * @author gizmore
 * @version 6.10.7
 * @since 6.10.6
 */
final class Timezones extends MethodAjax
{
	public function execute()
	{
		$data = GDO_Timezone::table()->select()->exec()->fetchAllArray2dObject();
		return GDT_Array::makeWith($data);
	}

}
