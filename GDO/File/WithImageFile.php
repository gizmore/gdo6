<?php
namespace GDO\File;

use GDO\UI\WithImageSize;

/**
 * Add this trait for image related file stuff.
 * 
 * @author gizmore, kalle
 * @version 6.10
 * @since 6.07
 */
trait WithImageFile
{
    use WithImageSize;
    
	public function isImageFile() { return true; }
	
	##############
	### Scaled ###
	##############
	public $scaledVersions = [];
	public function scaledVersion($name, $width, $height)
	{
		$this->scaledVersions[$name] = [$width, $height];
		return $this;
	}
	
	###############
	### Variant ###
	###############
	public $variant;
	public function variant($variant) { $this->variant = $variant; return $this; }
	
	############
	### Size ###
	############
	public function styleSize()
	{
	    if ($this->imageWidth)
	    {
	        return sprintf('max-width: %spx; max-height: %spx;', $this->imageWidth, $this->imageHeight);
	    }
	}
	
	############
	### HREF ###
	############
	public function displayPreviewHref(GDO_File $file)
	{
	    $href = $this->previewHREF . $file->getID();
	    if ($this->variant)
	    {
	        $href .= '&variant='.$this->variant;
	    }
	    return $href;
	}
	
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
	
	###############
	### Convert ###
	###############
// 	public $convert;
// 	public function convertTo($mime) { $this->convert = $mime; return $this; }
	protected function beforeCopy(GDO_File $file)
	{
		ImageResize::derotate($file);
		
		$this->createScaledVersions($file);

// 		if ($this->resize)
// 		{
// 			$this->createFileToScale($file, 'original');
// 			ImageResize::resize($file, $this->resizeWidth, $this->resizeHeight, $this->convert);
// 		}
	}
	
	public function createScaledVersions(GDO_File $original)
	{
		foreach ($this->scaledVersions as $name => $dim)
		{
			list($w, $h) = $dim;
			$file = $this->createFileToScale($original, $name);
			ImageResize::resize($file, $w, $h);
		}
	}
	
	public function createFileToScale(GDO_File $original, $name)
	{
		$src = $original->getPath();
		$dest = $original->getDestPath() . "_$name";
		if (copy($src, $dest))
		{
			$file = GDO_File::fromForm(array(
				'name' => $original->getName(),
				'size' => $original->getSize(),
				'mime' => $original->getType(),
				'path' => $dest,
			));
			return $file;
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
		return false;
	}
	
	protected function validateImageFile(GDO_File $file)
	{
		list($width, $height) = getimagesize($file->getPath());
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
