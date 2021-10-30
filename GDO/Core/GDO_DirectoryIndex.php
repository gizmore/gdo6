<?php
namespace GDO\Core;

use GDO\DB\GDT_String;
use GDO\UI\GDT_Icon;
use GDO\Net\GDT_Url;
use GDO\File\GDT_Filesize;

/**
 * A file table for directory index.
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.10.5
 */
final class GDO_DirectoryIndex extends GDO
{
    public function gdoColumns()
    {
        return [
            GDT_Icon::make('file_icon'),
            GDT_Url::make('file_name'),
            GDT_String::make('file_type'),
            GDT_Filesize::make('file_size'),
        ];
    }
    
}
