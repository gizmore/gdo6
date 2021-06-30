<?php
namespace GDO\File;

use GDO\DB\GDT_UInt;

/**
 * Display int as human readable filesize.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.1.0
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
