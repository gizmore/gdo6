<?php
namespace GDO\File;

use GDO\Core\GDT_Template;

final class GDT_ImageFile extends GDT_File
{
	public function __construct()
	{
		$this->mime('image/gif');
		$this->mime('image/jpeg');
		$this->mime('image/png');
		$this->icon('image');
		parent::__construct();
	}
	
	public function renderForm() { return GDT_Template::php('File', 'form/imagefile.php', ['field'=>$this]); }
	
	#################
	### Flow test ###
	#################
	protected function onFlowFinishTests($key, $file)
	{
		if (false !== ($error = parent::onFlowFinishTests($key, $file)))
		{
			return $error;
		}
		if (false !== ($error = $this->onFlowTestImageDimension($key, $file)))
		{
			return $error;
		}
		return false;
	}

	private function onFlowTestImageDimension($key, $file)
	{
		return false;
	}
	
	##############
	### Bound  ###
	##############
	### XXX: Bound checking is done before a possible conversion.
	###      It could make sense to set those values to 10,10,2048,2048 or something.
	###      This could prevent DoS with giant images.
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
	### Resize ###
	##############
	public $resize;
	public $resizeWidth;
	public $resizeHeight;
	public function resize($resize=true) { $this->resize = $resize; return $this; }
	public function resizeTo($width, $height)
	{
	    $this->resizeWidth = $width;
	    $this->resizeHeight = $height;
	    return $this->resize();
	}

	###############
	### Convert ###
	###############
	public $convert;
	public function convertTo($mime) { $this->convert = $mime; return $this; }
	protected function beforeCopy(GDO_File $file)
	{
		if ($this->resize)
		{
			ImageResize::resize($file, $this->resizeWidth, $this->resizeHeight, $this->convert);
		}
	}

	##################
	### Validation ###
	##################
	protected function validateFile(GDO_File $file)
	{
	    if (parent::validateFile($file))
	    {
	        return $this->validateImageFile($file);
	    }
	}
	
	protected function validateImageFile(GDO_File $file)
	{
	    list($width, $height, $format) = getimagesize($file->getPath());
	    if ( ($this->maxWidth !== null) && ($width > $this->maxWidth) )
	    {
	        return $this->error('err_image_too_wide', [$this->maxWidth]);
	    }
	    if ( ($this->minWidth !== null) && ($width < $this->minWidth) )
	    {
	        return $this->error('err_image_not_wide_enough', [$this->minWidth]);
	    }
	    if ( ($this->maxHeight !== null) && ($height > $this->maxHeight) )
	    {
	        return $this->error('err_image_too_high', [$this->maxHeight]);
	    }
	    if ( ($this->minHeight !== null) && ($height < $this->minHeight) )
	    {
	        return $this->error('err_image_not_high_enough', [$this->minHeight]);
	    }
	    return true;
	}

}
