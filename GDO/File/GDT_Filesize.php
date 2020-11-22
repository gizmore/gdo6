<?php
namespace GDO\File;

use GDO\DB\GDT_UInt;

/**
 * Display int as human readable filesize.
 * @author gizmore
 * @version 6.05
 * @since 6.01
 */
final class GDT_Filesize extends GDT_UInt
{
	public function defaultLabel() { return $this->label('filesize'); }
	
	public function renderCell()
	{
		return FileUtil::humanFilesize($this->getValue());
	}

	
	public function toValue($var)
	{
	    return (int) FileUtil::humanToBytes($var);
	}
	
}
