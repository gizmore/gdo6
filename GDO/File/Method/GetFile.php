<?php
namespace GDO\File\Method;

use GDO\Core\Method;
use GDO\File\GDO_File;
use GDO\File\FileUtil;
use GDO\Net\Stream;
use GDO\Util\Common;
use GDO\DB\GDT_Int;
use GDO\DB\GDT_String;

/**
 * Serve a file from partially db(meta) and fs.
 * This method requires admin permission because it shall not be called directly.
 * You have to write a wrapper that may call this method.
 * In your fields you can choose between 4 major GDT: GDT_File, GDT_Files, GDT_ImageFile, GDT_ImageFiles
 * The single GDT_File and GDT_ImageFile add a column to your GDO.
 * The multi GDT_Files and GDT_ImageFiles require you to implement a GDO table inheriting from GDT_FileTable.
 * 
 * @see GDO_File
 * @see GDO_FileTable
 * @see GDT_File
 * @see GDT_Files
 * @see GDT_ImageFile
 * @see GDT_ImageFiles
 * @see WithImageFile
 * 
 * @author gizmore@wechall.net
 * @version 6.08
 * @since 6.00
 */
final class GetFile extends Method
{
    public function isTrivial() { return false; } # no trivial method testing.
    
	public function getPermission() { return 'admin'; }
	
	public function gdoParameters()
	{
		return array(
			GDT_Int::make('file')->notNull(),
			GDT_String::make('variant'),
		);
	}
	
	public function execute()
	{
		return $this->executeWithId(
			Common::getRequestString('file'),
			Common::getRequestString('variant', ''));
	}
	
	public function executeWithId($id, $variant='', $nodisp=null)
	{
		if (!($file = GDO_File::getById($id)))
		{
			return $this->error('err_unknown_file', null, 404);
		}
		
		$path = $file->getVariantPath($variant);
		if (!FileUtil::isFile($path))
		{
			return $this->error('err_file_not_found', [htmlspecialchars($path)]);
		}
		
		$nodisp = $nodisp === null ? (!isset($_REQUEST['nodisposition'])) : $nodisp;
		
		Stream::serve($file, $variant, !$nodisp);
	}
	
}
