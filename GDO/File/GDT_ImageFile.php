<?php
namespace GDO\File;

use GDO\Core\GDT_Template;

final class GDT_ImageFile extends GDT_File
{
	public function __construct()
	{
		$this->mime('image/bmp');
		$this->mime('image/gif');
		$this->mime('image/jpeg');
		$this->mime('image/png');
		parent::__construct();
	}
	
	public function renderForm()
	{
		return GDT_Template::php('File', 'form/imagefile.php', ['field'=>$this]);
	}
	
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
	### Resize ###
	##############
	public $minWidth;
	public function minWidth($minWidth) { $this->minWidth = $minWidth; return $this; }
	public $maxWidth;
	public function maxWidth($maxWidth) { $this->maxWidth = $maxWidth; return $this; }
	public $minHeight;
	public function minHeight($minHeight) { $this->minHeight = $minHeight; return $this; }
	public $maxHeight;
	public function maxHeight($maxHeight) { $this->maxHeight = $maxHeight; return $this; }
	public $resize;
	public function resize($resize=true) { $this->resize = $resize; return $this; }
	public function resizeTo($width, $height)
	{
	    $this->minWidth = $this->maxWidth = $width;
	    $this->minHeight = $this->maxHeight = $height;
	    return $this->resize();
	}

	public $convert;
	public function convertTo($mime) { $this->convert = $mime; return $this; }
	protected function beforeCopy(GDO_File $file)
	{
		if ( ($this->maxWidth !== null) || ($this->convert !== null) )
		{
			ImageResize::resize($file, $this->maxWidth, $this->maxHeight, $this->convert);
		}
	}
	
}
