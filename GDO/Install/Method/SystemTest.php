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
				FileUtil::createDir(GWF_PATH . 'protected'),
				FileUtil::createDir(GWF_PATH . 'files'),
				FileUtil::createDir(GWF_PATH . 'temp'),
				$this->testBower(),
				function_exists('mb_strlen'),
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
		$version = floatval(PHP_MAJOR_VERSION. '.' . PHP_MINOR_VERSION);
		return $version >= 5.6;
	}

	private function testBower()
	{
		return null;
	}
	
}
