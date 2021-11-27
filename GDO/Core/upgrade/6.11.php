<?php
namespace GDO\Core\upgrade;

/**
 * This script changes Datetimes to Timestamps for GDT_CreatedAt, GDT_EditedAt, GDT_DeletedAt
 */

use GDO\DB\Database;
use GDO\Core\GDT;
use GDO\Core\ModuleLoader;
use GDO\Core\GDO;
use GDO\DB\GDT_CreatedAt;
use GDO\DB\GDT_EditedAt;
use GDO\DB\GDT_DeletedAt;
use GDO\Core\GDO_Module;
use GDO\User\GDO_User;

# Change datetime to timestamp
$modules = ModuleLoader::instance()->getEnabledModules();
foreach ($modules as $module)
{
	if ($classes = $module->getClasses())
	{
		foreach ($classes as $class)
		{
			/**
			 * @var GDO $gdo
			 */
			$gdo = $class::table();
			$columns = $gdo->gdoColumnsCache();
			foreach ($columns as $gdt)
			{
				if ( ($gdt instanceof GDT_CreatedAt) ||
				     ($gdt instanceof GDT_EditedAt) ||
				     ($gdt instanceof GDT_DeletedAt)
				)
				{
					changeColumn($module, $gdo, $gdt);
				}
			}
		}
	}
}

# Change user timezone to database reference.
# All configs cleared.
$users = GDO_User::table()->select()->exec();
while ($user = $users->fetchObject())
{
	$user->saveVar('user_timezone', "1", false);
}


function changeColumn(GDO_Module $module, GDO $table, GDT $gdt)
{
	try
	{
		$db = Database::instance();
		$db->disableForeignKeyCheck();
		
		$tablename = $table->gdoTableName();
		$temptable = "TEMP_{$tablename}";
		$query = "CREATE TABLE $temptable LIKE $tablename";
		$db->queryWrite($query);
		$query = "INSERT INTO $temptable SELECT * FROM $tablename";
		$db->queryWrite($query);
		$query = "ALTER TABLE $tablename MODIFY COLUMN {$gdt->gdoColumnDefine()}";
		$db->queryWrite($query);
		$ids = $table->gdoPrimaryKeyColumnNames();
		$ids = array_map(function($s) { return "a.{$s}=b.{$s}"; }, $ids);
		$ids = implode(' AND ', $ids);
		$query = "UPDATE $tablename a, $temptable b SET a.{$gdt->name} = b.{$gdt->name} WHERE {$ids}";
		$db->queryWrite($query);
		$query = "DROP TABLE {$temptable}";
		$db->queryWrite($query);
	}
	finally
	{
		$db->enableForeignKeyCheck();
	}
}
