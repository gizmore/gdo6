<?php
namespace GDO\File\Method;

use GDO\Core\Method;
use GDO\File\GDO_File;
use GDO\File\FileUtil;
use GDO\Net\Stream;
use GDO\Util\Common;
/**
 * Server a file from partially db(meta) and fs.
 * This method requires admin permission because it shall not be called directly.
 * You have to write a wrapper that may call this method.
 * 
 * @see ImageUser
 * 
 * @author gizmore
 * @version 6.08
 */
final class GetFile extends Method
{
	public function getPermission() { return 'admin'; }
	
	public function execute()
	{
		return $this->executeWithId(Common::getRequestString('file'));
	}
	
	public function executeWithId($id)
	{
		if (!($file = GDO_File::getById($id)))
		{
			return $this->error('err_unknown_file', null, 404);
		}
		if (!FileUtil::isFile($file->getPath()))
		{
			return $this->error('err_file_not_found', [htmlspecialchars($file->getPath())]);
		}
		Stream::serve($file);
	}
}
