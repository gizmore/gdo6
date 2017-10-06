<?php
namespace GDO\Core;
/**
 * Baseclass method for a cronjob.
 * 
 * @author gizmore
 * @since 5.0
 * @version 6.05
 */
abstract class MethodCronjob extends Method
{
	public abstract function run();

	public function getPermission() { return 'cronjob'; }
	public function execute()
	{
		$this->start();
		$this->run();
		$this->end();
	}

	###########
	### Log ###
	###########
	public function start() { Logger::logCron('[START] '.get_called_class()); }
	public function end() { Logger::logCron('[DONE] '.get_called_class().PHP_EOL); }

	public function log($msg) { Logger::logCron('[+] '.$msg); }
	public function logError($msg) { Logger::logCron('[ERROR] '.$msg); return false; }
	public function logWarning($msg) { Logger::logCron('[WARNING] '.$msg); }
	public function logNotice($msg) { Logger::logCron('[NOTICE] '.$msg); }
}
