<?php
namespace GDO\Install\Method;

use GDO\Core\Method;
use GDO\File\FileUtil;
use GDO\File\GDO_File;

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
				FileUtil::createDir(GDO_File::filesDir()),
				FileUtil::createDir(GDO_PATH . 'temp'),
			    FileUtil::createDir(GDO_PATH . 'assets'),
				$this->testBower(),
				function_exists('mb_strlen'),
			    function_exists('mime_content_type'),
			),
			'optional' => array(
			    function_exists('curl_init'),
			    function_exists('imagecreate'),
			    class_exists('\\Memcached'),
				function_exists('openssl_cipher_iv_length'),
			),
		);
		return $this->templatePHP('page/systemtest.php', $tVars);
	}
	
	private function testPHPVersion()
	{
		$version = floatval(PHP_MAJOR_VERSION. '.' . PHP_MINOR_VERSION);
		return $version >= 7.0;
	}

	private function testBower()
	{
	}
	
}
