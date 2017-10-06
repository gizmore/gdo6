<?php
namespace GDO\UI;
use GDO\Form\GDT_Select;
use GDO\File\FileUtil;
use GDO\Core\GDT_Template;
use GDO\Util\Strings;
/**
 * Scan the fonts dir for a select.
 * @author gizmore
 */
class GDT_Font extends GDT_Select
{
	public function defaultLabel() { return $this->label('font'); }
	
	public function renderForm()
	{
	    $this->choices = $this->fontChoices();
	    return parent::renderForm();
	}
	
	public function validate($value)
	{
        $this->choices = $this->fontChoices();
        return parent::validate($value);
	}
	
	public function fontChoices()
	{
	    static $choices;
	    if (!isset($choices))
	    {
    	    $choices = [];
    	    foreach (GDT_Template::$THEMES as $theme => $path)
    	    {
    	        $dir = $path . 'fonts';
    	        if (FileUtil::isDir($dir))
    	        {
    	            $files = FileUtil::scandir($dir);
    	            foreach ($files as $file)
    	            {
    	                $fontName = Strings::rsubstrTo($file, '.');
    	                $fontPath = Strings::rsubstrFrom($dir . '/' . $file, GWF_PATH);
    	                $choices[$fontPath] = $fontName;
    	            }
    	        }
    	    }
	    }
	    return $choices;
	}
}
