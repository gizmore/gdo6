<?php
namespace GDO\File;

use GDO\DB\GDT_String;

/**
 * Mime Filetype widget.
 * Lots todo. But one can already use it.
 * 
 * @author gizmore
 * @version 6.11.2
 * @since 6.10
 */
final class GDT_MimeType extends GDT_String
{
	public $max = 96;
	public $caseSensitive = true;
	public $encoding = self::ASCII;
	
	public function defaultLabel() { return $this->label('file_type'); }

}
