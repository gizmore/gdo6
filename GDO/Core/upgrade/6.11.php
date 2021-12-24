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
use GDO\Core\Logger;
use GDO\Core\Application;

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
					if ($gdt->name !== 'sess_created')
					{
						changeColumn($module, $gdo, $gdt);
					}
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
		$temptable = "zzz_temp_{$tablename}";

		$query = "SHOW CREATE TABLE $tablename";
		$result = Database::instance()->queryRead($query);
		$query = mysqli_fetch_row($result)[1];
		$query = str_replace($tablename, $temptable, $query);
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
	catch (\Throwable $ex)
	{
		Logger::logException($ex);
		if (Application::instance()->isCLI())
		{
			echo $ex->getMessage();
			echo PHP_EOL;
			echo $ex->getTraceAsString();
			echo PHP_EOL;
		}
	}
	finally
	{
		$db->enableForeignKeyCheck();
	}
}
