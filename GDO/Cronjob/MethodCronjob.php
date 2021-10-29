<?php
namespace GDO\Cronjob;

use GDO\Core\Logger;
use GDO\Core\Method;

/**
 * Baseclass method for a cronjob.
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.1.0
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
	
	public function runEvery()
	{
	    return 1;
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
