<?php
namespace GDO\File;

use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\Date\GDT_Duration;
use GDO\DB\GDT_String;
use GDO\Core\GDOError;
use GDO\Core\GDT_Template;
use GDO\Util\Strings;
use GDO\Core\GDOException;
use GDO\DB\GDT_UInt;
use GDO\Core\Debug;
use GDO\Core\Application;

/**
 * File database storage.
 * 
 * @author gizmore
 * @version 6.10.2
 * @since 6.1.0
 *
 * @see GDT_File
 */
final class GDO_File extends GDO
{
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return [
			GDT_AutoInc::make('file_id')->label('id'),
			GDT_String::make('file_name')->notNull(),
			GDT_MimeType::make('file_type')->notNull(),
			GDT_Filesize::make('file_size')->notNull(),
			GDT_UInt::make('file_width'),
			GDT_UInt::make('file_height'),
			GDT_UInt::make('file_bitrate'),
			GDT_Duration::make('file_duration'),
		];
	}
	
	public function getName() { return $this->getVar('file_name'); }
	public function displayName() { return html($this->getName()); }
	public function getSize() { return $this->getVar('file_size'); }
	public function getType() { return $this->getVar('file_type'); }
	public function displaySize() { return FileUtil::humanFilesize($this->getSize()); }
	public function isImageType() { return Strings::startsWith($this->getType(), 'image/'); }
	public function getWidth() { return $this->getVar('file_width'); }
	public function getHeight() { return $this->getVar('file_height'); }
	
	public function renderCell() { return GDT_Template::php('File', 'cell/file.php', ['gdo'=>$this]); }
	public function renderCard() { return GDT_Template::php('File', 'card/file.php', ['gdo'=>$this]); }
	
	public $path;
	public function tempPath($path=null)
	{
		$this->path = $path;
		return $this;
	}
	
	private $href;
	public function tempHref($href=null)
	{
		$this->href = $href;
		return $this;
	}
	
	public function getHref() { return $this->href; }
	public function getPath() { return $this->path ? $this->path : $this->getDestPath(); }
	public function getDestPath() { return self::filesDir() . $this->getID(); }
	public function getVariantPath($variant='')
	{
		if ($variant)
		{
			# security
			$variant = preg_replace("/[^a-z]/", '', $variant);
			$variant = "_$variant";
		}
		return $this->getPath() . $variant;
	}
	
	public function gdoAfterDelete()
	{
		Filewalker::traverse(self::filesDir(), "/^{$this->getID()}_/", [$this, 'deleteVariant']);
		@unlink($this->getDestPath());
	}
	
	public function deleteVariant($entry, $fullpath)
	{
		@unlink($fullpath);
	}
	
	public function toJSON()
	{
		return array_merge(parent::toJSON(), [
			'id' => $this->getID(),
			'name' => $this->getName(),
			'type' => $this->getType(),
			'size' => $this->getSize(),
			'initial' => true
		]);
	}
	
	###############
	### Factory ###
	###############
	public static function filesDir()
	{
	    if (Application::instance()->isUnitTests())
	    {
	        return GDO_PATH . 'files_test/';
	    }
	    else
	    {
	        return GDO_PATH . 'files/';
	    }
	}
	
	/**
	 * @param array $values
	 * @return self
	 */
	public static function fromForm(array $values)
	{
		$file = self::blank([
			'file_name' => $values['name'],
			'file_size' => $values['size'],
			'file_type' => $values['type']
		])->tempPath($values['tmp_name']);
		
		if ($file->isImageType())
		{
			list($width, $height) = getimagesize($file->getPath());
			$file->setVars([
				'file_width' => $width,
				'file_height' => $height,
			]);
		}
		return $file;
	}
	
	/**
	 * @param string $contents
	 * @return self
	 */
	public static function fromString($name, $content)
	{
		# Create temp dir
		$tempDir = GDO_PATH . 'temp/file';
		FileUtil::createDir($tempDir);
		# Copy content to temp file
		$tempPath = $tempDir . '/' . md5(md5($name).md5($content));
		file_put_contents($tempPath, $content);
		return self::fromPath($name, $tempPath);
	}
	
	/**
	 * @param string $name
	 * @param string $path
	 * @throws GDOException
	 * @return \GDO\File\GDO_File
	 */
	public static function fromPath($name, $path)
	{
		if (!FileUtil::isFile($path))
		{
			throw new GDOException(t('err_file_not_found', [$path]));
		}
		$values = [
			'name' => $name,
			'size' => filesize($path),
			'type' => mime_content_type($path),
			'tmp_name' => $path,
		];
		return self::fromForm($values)->tempPath($path);
	}
	
	############
	### Copy ###
	############
	/**
	 * This saves the uploaded file to the files folder and inserts the db row.
	 * 
	 * @throws GDOError
	 * @return self
	 */
	public function copy()
	{
		FileUtil::createDir(self::filesDir());
		if (!@copy($this->path, $this->getDestPath()))
		{
			throw new GDOError('err_upload_move', [
			    html(Debug::shortpath($this->path)), 
			    html(Debug::shortpath($this->getDestPath()))]);
		}
		$this->path = null;
		return $this;
	}
	
}
