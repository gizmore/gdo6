<?php
namespace GDO\File;

use GDO\Core\GDT_Template;

final class GDT_ImageFile extends GDT_File
{
	public function __construct()
	{
		$this->mime('image/jpeg');
		$this->mime('image/gif');
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
	
}
