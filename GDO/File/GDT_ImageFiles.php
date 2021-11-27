<?php
namespace GDO\File;

/**
 * An image file array with N:M table.
 * 
 * @version 6.11.0
 * @see GDT_Files
 * @author gizmore
 */
final class GDT_ImageFiles extends GDT_Files
{
	use WithImageFile;
	
	public function defaultLabel() { return $this->label('images'); }
	
	protected function __construct()
	{
		parent::__construct();
		$this->mime('image/gif');
		$this->mime('image/jpeg');
		$this->mime('image/png');
		$this->icon('image');
	}

	public function displayPreviewHref(GDO_File $file)
	{
		$href = parent::displayPreviewHref($file);
	    if ($this->variant)
	    {
	        $href .= '&variant='.$this->variant;
	    }
	    return $href;
	}

}
