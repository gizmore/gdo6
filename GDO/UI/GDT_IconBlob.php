<?php
namespace GDO\UI;

final class GDT_IconBlob extends GDT_Message
{
	protected function __construct()
	{
		$this->binary();
		$this->max(8192);
	}
	
	public function renderCell()
	{
		return "ICON";
	}
	
	public function renderForm()
	{
		return "ICON";
	}
}
