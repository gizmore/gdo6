<?php
namespace GDO\Core;

use GDO\DB\GDT_String;

/**
 * A secret is a config string that is shown as asterisks to the user.
 * In various transport protocols (json, websocket) this field is not transmitted.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 4.0.3
 */
class GDT_Secret extends GDT_String
{
    public function isSerializable() { return false; }

    public $hidden = true;
    public $encoding = self::ASCII;
	public $caseSensitive = true;
	
	public function renderCell() { return '********'; }

}
