<?php
namespace GDO\File;
use GDO\DB\GDT_String;
/**
 * A path variable with existance validator.
 * @author gizmore
 * @version 7.00
 * @since 6.00
 */
final class GDT_Path extends GDT_String
{
    public $pattern = "#^[^?:]+$#";
    
    public function defaultLabel() { return $this->label('path'); }
    public function htmlClass()
    {
        return FileUtil::isFile($this->getValue()) ? ' class="gdo-file-valid"' : ' class="gdo-file-invalid"';
    }
    
    public $existing = false;
    public function existingDir() { $this->existing = 'is_dir'; return $this->icon('folder'); }
    public function existingFile() { $this->existing = 'is_file'; return $this->icon('insert_drive_file'); }
    
    public function validate($value)
    {
        return parent::validate($value) && $this->validatePath($value);
    }
    
    public function validatePath($filename)
    {
        if ($this->existing)
        {
            if ( (!is_readable($filename)) || (!call_user_func($this->existing, $filename)) )
            {
                return $this->error('err_path_not_exists', [$filename, $this->existing]);
            }
        }
        return true;
    }
    
}
