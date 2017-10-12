<?php
namespace GDO\UI;
use GDO\Core\GDT_Template;
use GDO\DB\GDT_Text;
/**
 * A message is GDT_Text with an editor.
 * Currently, no nice md editor is to be found on the webs?
 * 
 * @author gizmore
 * @since 3.0
 * @version 5.0
 */
class GDT_Message extends GDT_Text
{
	public function renderForm()
	{
		return GDT_Template::php('UI', 'form/message.php', ['field'=>$this]);
	}
	
	public function renderCell()
	{
	    return GDT_Template::php('UI', 'cell/message.php', ['field'=>$this]);
	}
	
	public function renderList()
	{
	    return '<div class="gdo-message-condense">'.$this->renderCell().'</div>';
	}
	
	private function getPurifier()
	{
	    static $purifier;
	    if (!isset($purifier))
	    {
	        require GWF_PATH . 'GDO/UI/htmlpurifier/library/HTMLPurifier.auto.php';
	        $config = \HTMLPurifier_Config::createDefault();
	        $config->set('Attr.AllowedClasses', 'b');
	        $config->set('Attr.DefaultInvalidImageAlt', t('img_not_found'));
	        $config->set('HTML.SafeObject', true);
	        $config->set('HTML.Nofollow', true);
	        $purifier = new \HTMLPurifier($config);
	    }
	    return $purifier;
	}
	
	public function validate($value)
	{
	    if (parent::validate($value))
	    {
	        $value = $this->getPurifier()->purify($value);
	        $this->changeRequestVar($value);
	        return true;
	    }
	}
	
}
