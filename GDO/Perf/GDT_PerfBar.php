<?php
namespace GDO\Perf;
use GDO\Core\Logger;
use GDO\DB\Database;
use GDO\Language\Trans;
use GDO\Core\GDT_Template;
use GDO\Core\ModuleLoader;
use GDO\Core\GDT_Hook;
use GDO\UI\GDT_Panel;
use GDO\Mail\Mail;
/**
 * Performance statistics panel.
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
final class GDT_PerfBar extends GDT_Panel
{
	public static function data()
	{
		global $GDT_LOADED;
		$totalTime = microtime(true) - GWF_PERF_START;
		$phpTime = $totalTime - Database::$QUERY_TIME;
		$memphp = memory_get_peak_usage(false);
		$memreal = memory_get_peak_usage(true);
		return array(
			'logWrites' => Logger::$WRITES,

			'dbReads' => Database::$READS,
			'dbWrites' => Database::$WRITES,
			'dbCommits' => Database::$COMMITS,
			'dbQueries' => Database::$QUERIES,

			'dbTime' => round(Database::$QUERY_TIME, 4),
			'phpTime' => round($phpTime, 4),
			'totalTime' => round($totalTime, 4),
			
			'memory_php' => $memphp,
			'memory_real' => $memreal,
			'memory_max' => max($memphp, $memreal), # Bug in PHP?
			
			'phpClasses' => count(get_declared_classes()),
			
			'gdoFiles' => $GDT_LOADED,
			'gdoModules' => count(ModuleLoader::instance()->getModules()),
			'gdoLangFiles' => Trans::numFiles(),
			'gdoTemplates' => GDT_Template::$CALLS,
			'gdoHooks' => GDT_Hook::$CALLS,
			'gdoIPC' => GDT_Hook::$IPC_CALLS,
			'gdoMails' => Mail::$SENT,
		);
	}

	public function render() { return GDT_Template::php('Perf', 'cell/perfbar.php', ['bar' => $this]); }
}
