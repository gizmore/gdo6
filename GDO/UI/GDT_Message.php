<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\DB\GDT_Text;

/**
 * A message is a GDT_Text with an editor.
 * The content is html, filtered through a whitelist with html-purifier.
 * The default editor is simply a textarea, and a gdo6-tinymce is available.
 * 
 * @see \GDO\TinyMCE\Module_TinyMCE
 * @author gizmore
 * @since 3.0
 * @version 6.09
 */
class GDT_Message extends GDT_Text
{
	public function __construct()
	{
		$this->icon('message');
	}
	
	##############
	### Render ###
	##############
	public function renderCell() { return GDT_Template::php('UI', 'cell/message.php', ['field'=>$this]); }
	public function renderForm() { return GDT_Template::php('UI', 'form/message.php', ['field'=>$this]); }
	public function renderList() { return '<div class="gdo-message-condense">'.$this->renderCell().'</div>'; }
	
	##############
	### Editor ###
	##############
	public $nowysiwyg = false;
	public function nowysiwyg($nowysiwyg=true) { $this->nowysiwyg = $nowysiwyg; return $this; }
	public function classEditor() { return $this->nowysiwyg ? 'as-is' : 'wysiwyg'; }
	
	################
	### Validate ###
	################
	public function validate($value)
	{
		if (parent::validate($value))
		{
			return true;
		}
	}
	
	private function getPurifier()
	{
		static $purifier;
		if (!isset($purifier))
		{
			require GDO_PATH . 'GDO/UI/htmlpurifier/library/HTMLPurifier.auto.php';
			$config = \HTMLPurifier_Config::createDefault();
			
			$config->set('HTML.Allowed', 'div,blockquote,span');
			$config->set('Attr.AllowedClasses', 'quote-from,quote-by');
			$config->set('Attr.DefaultInvalidImageAlt', t('img_not_found'));
			$config->set('HTML.SafeObject', true);
			$config->set('HTML.Nofollow', true);
			$purifier = new \HTMLPurifier($config);
		}
		return $purifier;
	}
	
}
