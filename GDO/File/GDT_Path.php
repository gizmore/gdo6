<?php
namespace GDO\File;
use GDO\DB\GDT_String;
/**
 * A path variable with existance validator.
 * 
 * @author gizmore
 * @version 6.07
 * @since 6.00
 */
final class GDT_Path extends GDT_String
{
	public $pattern = "#^[^?]+$#";

	public function defaultLabel() { return $this->label('path'); }
	public function htmlClass()
	{
		return FileUtil::isFile($this->getValue()) ? ' gdo-file-valid' : ' gdo-file-invalid';
	}

	#################
	### Existance ###
	#################
	public $existing = false;
	public function existingDir() { $this->existing = 'is_dir'; return $this->icon('folder'); }
	public function existingFile() { $this->existing = 'is_file'; return $this->icon('file'); }

	#####################
	### Var and Value ###
	#####################
// 	public function toVar($value) { return str_replace("\\", "/", $value); }
// 	public function toValue($var) { return str_replace("\\", "/", $var); }

	################
	### Validate ###
	################
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
				return $this->error('err_path_not_exists', [$filename, t($this->existing)]);
			}
		}
		return true;
	}

}
