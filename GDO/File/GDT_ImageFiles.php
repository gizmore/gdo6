<?php
namespace GDO\File;

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

}
