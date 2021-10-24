<?php
namespace GDO\File;

use GDO\Core\GDT_Template;
use GDO\Session\GDO_Session;
use GDO\Util\Arrays;
use GDO\Core\GDO;
use GDO\DB\GDT_Object;
use GDO\UI\WithHREF;
use GDO\Core\GDT_Error;
use GDO\Core\GDT_Success;
use GDO\Core\GDO_Module;
use GDO\UI\WithImageSize;

/**
 * File input and upload backend for flow.js
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 4.2.0
 */
class GDT_File extends GDT_Object
{
	use WithHREF;
	use WithImageSize;
	
	public $multiple = false;
	
	public function defaultLabel() { return $this->label('file'); }
	public function isImageFile() { return false; }
	
	protected function __construct()
	{
	    parent::__construct();
		$this->table(GDO_File::table());
		$this->icon('file');
// 		$this->defaultSize();
	}
	
	############
	### Mime ###
	############
	public $mimes = [];
	public function mime(...$mime)
	{
		$this->mimes = array_merge($this->mimes, $mime);
		return $this;
	}
	
	############
	### Size ###
	############
	public $minsize;
	public function minsize($minsize)
	{
		$this->minsize = $minsize;
		return $this;
	}
	
	public $maxsize = 1024 * 4096; # 4MB
	public function maxsize($maxsize)
	{
		$this->maxsize = $maxsize;
		return $this;
	}
	
	public function defaultSize()
	{
	    return $this->maxsize(Module_File::instance()->cfgUploadMaxSize());
	}
	
	###############
	### Preview ###
	###############
	public $preview = false;
	public function preview($preview=true)
	{
		$this->preview = $preview;
		return $this;
	}
	
	public $previewHREF;
	public function previewHREF($previewHREF=null) { $this->previewHREF = $previewHREF; return $this->preview($previewHREF!==null); }
	public function displayPreviewHref(GDO_File $file) { return $this->previewHREF . $file->getID(); }
	
	##################
	### File count ###
	##################
	public $minfiles = 0;
	public $maxfiles = 1;
	public function minfiles($minfiles)
	{
		$this->minfiles = $minfiles;
		return $minfiles  > 0 ? $this->notNull() : $this;
	}
	
	public function maxfiles($maxfiles)
	{
		$this->maxfiles = $maxfiles;
		return $this;
	}
	
	############
	### Size ###
	############
	public function styleSize()
	{
	    if ($this->imageWidth)
	    {
	        return sprintf('max-width: %.01fpx; max-height: %.01fpx;', $this->imageWidth, $this->imageHeight);
	    }
	}
	##############
	### Bound  ###
	##############
	### XXX: Bound checking is done before a possible conversion.
	###	  It could make sense to set those values to 10,10,2048,2048 or something.
	###	  This could prevent DoS with giant images.
	### @see \GDO\File\GDT_File
	##############
	public $minWidth;
	public function minWidth($minWidth) { $this->minWidth = $minWidth; return $this; }
	public $maxWidth;
	public function maxWidth($maxWidth) { $this->maxWidth = $maxWidth; return $this; }
	public $minHeight;
	public function minHeight($minHeight) { $this->minHeight = $minHeight; return $this; }
	public $maxHeight;
	public function maxHeight($maxHeight) { $this->maxHeight = $maxHeight; return $this; }
	
	##############
	### Action ###
	##############
	public $action;
	public function action($action)
	{
		$this->action = $action.'&_ajax=1&_fmt=json&flowField='.$this->name;
		return $this;
	}
	
	public function getAction()
	{
		if (!$this->action)
		{
			$this->action(@$_SERVER['REQUEST_URI']);
		}
		return $this->action;
	}
	
	public $withFileInfo = true;
	public function withFileInfo($withFileInfo=true) { $this->withFileInfo = $withFileInfo; return $this; }
	
	##############
	### Render ###
	##############
	public function renderForm()
	{
		return GDT_Template::php('File', 'form/file.php', ['field'=>$this]);
	}
	
	public function renderCell()
	{
		return GDT_Template::php('File', 'cell/file.php', ['field' => $this, 'gdo' => $this->getValue()]);
	}
	
	public function configJSON()
	{
		return [
			'mimes' => $this->mimes,
			'minsize' => $this->minsize,
			'maxsize' => $this->maxsize,
			'minfiles' => $this->minfiles,
			'maxfiles' => $this->maxfiles,
			'preview' => $this->preview,
			'previewHREF' => $this->previewHREF,
			'selectedFiles' => $this->initJSONFiles(),
		];
	}
	
	public function renderCard()
	{
	    return GDT_Template::php('File', 'card/filecard.php', ['field' => $this]);
	}
	
	public function initJSONFiles()
	{
		$json = [];
		$files = Arrays::arrayed($this->getValue());
		/** @var $file \GDO\File\GDO_File **/
		foreach ($files as $file)
		{
			$file->tempHref($this->href);
			$json[] = $file->toJSON();
		}
		return $json;
	}
	
	#############
	### Value ###
	#############
	private $files = [];
	public function toVar($value)
	{
		if ($value)
		{
			if (is_array($value))
			{
				return $value[0]->getID();
			}
			else
			{
				return $value->getID();
			}
		}
	}

	public function toValue($var)
	{
		if ($var !== null)
		{
			return GDO_File::getById($var);
		}
	}
	
	public function getVar()
	{
		return $this->toVar($this->getValue());
	}
	
	/**
	 * Get all initial files for this file gdt.
	 * @return \GDO\File\GDO_File[]
	 */	
	public function getInitialFiles()
	{
		return Arrays::arrayed($this->getInitialFile());
	}
	
	public function getInitialFile()
	{
	    $var = $this->getRequestVar($this->formVariable(), $this->var);
		if ($var !== null)
		{
			return GDO_File::getById($var);
		}
	}
	
	public function setGDOData(GDO $gdo=null)
	{
		$this->var = $gdo->getVar($this->name);
		return $this;
	}
	
	public function getGDOData()
	{
		if ($file = $this->getValue())
		{
			return [$this->name => $file->getID()];
		}
		return [$this->name => null];
	}
	
	/**
	 * @return GDO_File
	 */
	public function getValidationValue()
	{
		$new = $this->getFiles($this->name);
		if (count($new))
		{
			return $new;
		}
		else
		{
			$old = $this->getInitialFiles();
			return $old;
		}
	}
	
	/**
	 * @return GDO_File
	 */
	public function getValue()
	{
		$files = array_merge($this->getInitialFiles(), Arrays::arrayed($this->files));
		return array_pop($files);
	}
	
	##############
	### Delete ###
	##############
	public $noDelete = false;
	public function noDelete($noDelete=true)
	{
	    $this->noDelete = $noDelete;
	    return $this;
	}
	
	public function notNull($notNull=true)
	{
	    $this->noDelete = $notNull;
	    return parent::notNull($notNull);
	}
	
	public function onDeleteFiles(array $ids)
	{
		$id = array_shift($ids); # only first id
		
		if ( ($this->gdo) && ($this->gdo->isPersisted()) ) # GDO possibly has a file
		{
			if ($this->gdo instanceof GDO_Module)
			{
				if ($id == $this->gdo->getConfigVar($this->name))
				{
					$this->gdo->removeConfigVar($this->name);
				}
			}
			
			if ($id == $this->gdo->getVar($this->name)) # It is the requested file to delete.
			{
				$this->gdo->saveVar($this->name, null); # Unrelate
				$this->initial(null);
			}
		}

		if ($file = GDO_File::getById($id)) # Delete file physically
		{
			$file->delete();
		}
	}
	
	################
	### Validate ###
	################
	public function validate($value)
	{
        $valid = true;
	    try
	    {
	        /** @var $files \GDO\File\GDO_File[] **/
	        $files = Arrays::arrayed($value);
	        $this->files = [];
	        
	        if ( ($this->notNull) && (empty($files)) )
	        {
	            $valid = $this->error('err_upload_min_files', [1]);
	        }
	        elseif (count($files) < $this->minfiles)
	        {
	            $valid = $this->error('err_upload_min_files', [max(1, $this->minfiles)]);
	        }
	        elseif (count($files) > $this->maxfiles)
	        {
	            $valid = $this->error('err_upload_max_files', [$this->maxfiles]);
	        }
	        else
	        {
	            foreach ($files as $file)
	            {
	                if (!($file->getSize()))
	                {
	                    $valid = $this->error('err_file_not_ok', [$file->display('file_name')]);
	                }
	                elseif (!$this->validateFile($file))
	                {
	                    $valid = false;
	                }
	                else
	                {
	                    if (!$file->isPersisted())
	                    {
	                        $file->insert();
	                        $this->beforeCopy($file);
	                        $file->copy();
	                        if ($this->gdo)
	                        {
	                            if (!$this->gdo->isTable())
	                            {
	                                $this->gdo->setVar($this->name, $file->getID());
	                            }
	                        }
	                        $this->var($file->getID());
	                        $this->files[] = $file;
	                    }
	                }
	            }
	        }
	        return $valid;
	    }
	    catch (\Throwable $ex)
	    {
	        $valid = false;
	    }
	    finally
	    {
	        $this->cleanup();
	    }
	}
	
	protected function validateFile(GDO_File $file)
	{
		if ( ($this->minsize !== null) && ($file->getSize() < $this->minsize) )
		{
			return $this->error('err_file_too_small', [FileUtil::humanFilesize($this->minsize)]);
		}
		if ( ($this->maxsize !== null) && ($file->getSize() > $this->maxsize) )
		{
			return $this->error('err_file_too_large', [FileUtil::humanFilesize($this->maxsize)]);
		}
		return true;
	}
	
	protected function beforeCopy(GDO_File $file)
	{
	}
	
	###################
	### Flow upload ###
	###################
	private function getTempDir($key='')
	{
		return GDO_PATH.'temp/flow/'.GDO_Session::instance()->getID().'/'.$key;
	}
	
	private function getChunkDir($key)
	{
		$chunkFilename = str_replace('/', '', $_REQUEST['flowFilename']);
		return $this->getTempDir($key).'/'.$chunkFilename;
	}
	
	private function denyFlowFile($key, $file, $reason)
	{
	    $this->cleanup();
	    $dir = $this->getChunkDir($key);
	    @mkdir($dir, GDO_CHMOD, true);
		return @file_put_contents($dir.'/denied', $reason);
	}
	
	private function deniedFlowFile($key, $file)
	{
		$file = $this->getChunkDir($key).'/denied';
		return FileUtil::isFile($file) ? file_get_contents($file) : false;
	}
	
	private function getFile($key)
	{
		if ($files = $this->getFiles($key))
		{
			return array_shift($files);
		}
	}
	
	protected function getFiles($key)
	{
		$files = array();
		$path = $this->getTempDir($key);
		if ($dir = @dir($path))
		{
			while ($entry = $dir->read())
			{
				if (($entry !== '.') && ($entry !== '..'))
				{
					if ($file = $this->getFileFromDir($path.'/'.$entry))
					{
						$files[] = $file;
					}
				}
			}
		}
		if (isset($_FILES[$key]))
		{
			if ($_FILES[$key]['name'])
			{
				$files[] = GDO_File::fromForm(array(
					'name' => $_FILES[$key]['name'],
					'type' => $_FILES[$key]['type'],
					'size' => $_FILES[$key]['size'],
					'dir' => dirname($_FILES[$key]['tmp_name']),
					'tmp_name' => $_FILES[$key]['tmp_name'],
					'error' => $_FILES[$key]['error'],
				));
			}
		}
		return $files;
	}
	
	/**
	 * @param string $dir
	 * @return GDO_File
	 */
	private function getFileFromDir($dir)
	{
		if (FileUtil::isFile($dir.'/0'))
		{
		    return GDO_File::fromForm([
				'name' => @file_get_contents($dir.'/name'),
				'type' => @file_get_contents($dir.'/mime'),
				'size' => filesize($dir.'/0'),
				'dir' => $dir,
				'tmp_name' => $dir.'/0',
		    ]);
		}
	}
	
	public function onValidated()
	{
		$this->cleanup();
	}
	
	public function cleanup()
	{
		$this->files = null;
		FileUtil::removeDir($this->getTempDir($this->name));
	}
	
	############
	### Flow ###
	############
	public function flowUpload()
	{
		return $this->onFlowUploadFile($this->name, $_FILES[$this->name]);
	}
	
	private function onFlowError($error, ...$args)
	{
	    $this->cleanup();
		return GDT_Error::responseWith($error, $args, 413);
	}
	
	private function onFlowUploadFile($key, $file)
	{
		$chunkDir = $this->getChunkDir($key);
		
		if (!FileUtil::createDir($chunkDir))
		{
			return $this->onFlowError('err_create_dir', $chunkDir);
		}
		
		if (false !== ($error = $this->deniedFlowFile($key, $file)))
		{
			return $this->onFlowError("err_upload_denied", $error);
		}
		
		if (!$this->onFlowCheckSizeBeforeCopy($key, $file))
		{
			return $this->onFlowError("err_file_too_large", $this->maxsize);
		}
		
		if (!$this->onFlowCopyChunk($key, $file))
		{
			return $this->onFlowError("err_copy_chunk_failed");
		}
		
		if ($_REQUEST['flowChunkNumber'] === $_REQUEST['flowTotalChunks'])
		{
			if ($error = $this->onFlowFinishFile($key, $file))
			{
				return $this->onFlowError("err_upload_failed", $error);
			}
		}
		return GDT_Success::responseWith('msg_uploaded');
	}
	
	private function onFlowCopyChunk($key, $file)
	{
		$chunkDir = $this->getChunkDir($key);
		$chunkNumber = (int) $_REQUEST['flowChunkNumber'];
		$chunkFile = $chunkDir . '/' . $chunkNumber;
		return @copy($file['tmp_name'], $chunkFile);
	}
	
	private function onFlowCheckSizeBeforeCopy($key, $file)
	{
		$chunkDir = $this->getChunkDir($key);
		$already = FileUtil::dirsize($chunkDir);
		$additive = filesize($file['tmp_name']);
		
		$substract = @filesize($chunkDir.'/0');
		$substract += @filesize($chunkDir.'/temp');
		$substract += @filesize($chunkDir.'/name');
		$substract += @filesize($chunkDir.'/mime');
		$substract += @filesize($chunkDir.'/denied');
		
		$sumSize = $already + $additive - $substract;

		if ($this->maxsize && ($sumSize > $this->maxsize))
		{
			$this->denyFlowFile($key, $file, t('err_filesize_exceeded', [FileUtil::humanFilesize($this->maxsize)]));
			return false;
		}

		return true;
	}
	
	private function onFlowFinishFile($key, $file)
	{
		$chunkDir = $this->getChunkDir($key);
		 
		# Clean old 0 file
		$finalFile = $chunkDir.'/0';
		@unlink($finalFile);
		
		# Merge chunks to single temp file
		$finalFile = $chunkDir.'/temp';
		Filewalker::traverse($chunkDir, null, array($this, 'onMergeFile'), false, true, array($finalFile));
		
		# Write user chosen name to a file for later
		$nameFile = $chunkDir.'/name';
		@file_put_contents($nameFile, $file['name']);
		
		# Write mime type for later use
		$mimeFile = $chunkDir.'/mime';
		@file_put_contents($mimeFile, mime_content_type($chunkDir.'/temp'));
		
		# Run finishing tests to deny.
		if (false !== ($error = $this->onFlowFinishTests($key, $file)))
		{
			$this->denyFlowFile($key, $file, $error);
			return $error;
		}
		
		# Move single temp to chunk 0
		if (!@rename($finalFile, $chunkDir.'/0'))
		{
			return "Cannot move temp file.";
		}
		
		return false; # no error
	}
	
	public function onMergeFile($entry, $fullpath, $args)
	{
		list($finalFile) = $args;
		@file_put_contents($finalFile, file_get_contents($fullpath), FILE_APPEND);
	}
	
	protected function onFlowFinishTests($key, $file)
	{
		if (false !== ($error = $this->onFlowTestChecksum($key, $file)))
		{
			return $error;
		}
		if (false !== ($error = $this->onFlowTestMime($key, $file)))
		{
			return $error;
		}
		return false;
	}
	
	private function onFlowTestChecksum($key, $file)
	{
		return false;
	}
	
	private function onFlowTestMime($key, $file)
	{
		if (!($mime = @file_get_contents($this->getChunkDir($key).'/mime')))
		{
			return t('err_no_mime_file', [$this->displayLabel(), $key]);
		}
		if ((!in_array($mime, $this->mimes, true)) && (count($this->mimes)>0))
		{
			return t('err_mimetype', [$this->displayLabel(), $mime]);
		}
		return false;
	}
	
}
