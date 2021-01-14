<?php
namespace GDO\Install\Method;

use GDO\Core\Method;
use GDO\File\FileUtil;

/**
 * Do some tests and output in page.
 * @author gizmore
 */
final class SystemTest extends Method
{
	public function execute()
	{
		$tVars = array(
			'tests' => array(
				$this->testPHPVersion(),
				FileUtil::createDir(GDO_PATH . 'protected'),
				FileUtil::createDir(GDO_PATH . 'files'),
				FileUtil::createDir(GDO_PATH . 'temp'),
				FileUtil::createDir(GDO_PATH . 'assets'),
				$this->testBower(),
				function_exists('mb_strlen'),
				ini_get('date.timezone'),
			),
			'optional' => array(
				function_exists('imagecreate'),
				class_exists('\\Memcached'),
			),
		);
		return $this->templatePHP('page/systemtest.php', $tVars);
	}
	
	private function testPHPVersion()
	{
		return version_compare(PHP_VERSION, '5.6.0') >= 0;
	}
	
	private function testBower()
	{
		return null;
	}
	
}
