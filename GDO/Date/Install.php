<?php
namespace GDO\Date;

use GDO\Date\Method\RefreshOffsets;

final class Install
{
	public static function install(Module_Date $module)
	{
		$list = timezone_identifiers_list();
		GDO_Timezone::blank([
			'tz_id' => '1',
			'tz_name' => 'UTC',
			'tz_offset' => '0',
		])->insert();
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
