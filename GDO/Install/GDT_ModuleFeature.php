<?php
namespace GDO\Install;
use GDO\Core\GDT;

final class GDT_ModuleFeature extends GDT
{
	/**
	 * @return \GDO\Core\GDO_Module
	 */
	public function getModule()
	{
		return $this->gdo;
	}

	public function renderCell()
	{
		$features = '';
		if ($this->getModule()->getTheme())
		{
			$features .= 'theme';
		}
		return $features;
	}

}
