<?php
namespace GDO\File;

/**
 * @author gizmore
 */
final class GDT_ImageFiles extends GDT_Files
{
	use WithImageFile;
	
	public function defaultLabel() { return $this->label('images'); }
	
	public function __construct()
	{
		parent::__construct();
		$this->mime('image/gif');
		$this->mime('image/jpeg');
		$this->mime('image/png');
		$this->icon('image');
	}

	public function displayPreviewHref(GDO_File $file)
	{
	    $href = $this->previewHREF . $file->getID();
	    if ($this->variant)
	    {
	        $href .= '&variant='.$this->variant;
	    }
	    return $href;
	}
}
