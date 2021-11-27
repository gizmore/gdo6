<?php
namespace GDO\Date;

use GDO\Date\Method\RefreshOffsets;

/**
 * Install timezone table.
 * @author gizmore
 */
final class Install
{
	public static function install(Module_Date $module)
	{
		$list = timezone_identifiers_list();
		array_unshift($list, 'UTC');
		foreach ($list as $tz)
		{
			if (!(GDO_Timezone::getBy('tz_name', $tz)))
			{
				GDO_Timezone::blank([
					'tz_name' => $tz,
				])->insert(false);
			}
		}
		$refresh = RefreshOffsets::make();
		$refresh->run();
	}
	
}
