<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\DB\GDT_Text;
use GDO\Core\GDO;

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
    public static function make($name=null)
    {
        $gdt = parent::make($name);
        $gdt->orderField = $gdt->name . '_text';
        $gdt->searchField = $gdt->name . '_text';
        return $gdt;
    }
    
    public static $DECODER = [self::class, 'DECODE'];
    public static function DECODE($s)
    {
        return '<div class="gdt-message ck-content">' . self::getPurifier()->purify($s) . '</div>';
    }
    
    public static function plaintext($html)
    {
        if ($html === null)
        {
            return null;
        }
        $html = preg_replace("#\r?\n#", ' ', $html);
        $html = preg_replace('#<a .*href="(.*)".*>(.*)</a>#', ' $2($1) ', $html);
        $html = preg_replace('#</p>#i', "\n", $html);
        $html = preg_replace('#<[^\\>]*>#', ' ', $html);
        $html = preg_replace('# +#', ' ', $html);
        return $html;
    }
    
    public $icon = 'message';
    
    private $input;
    private $output;
    private $text;
    
    public function gdoColumnDefine()
    {
        return
        "{$this->name}_input {$this->gdoColumnDefineB()},\n".
        "{$this->name}_output {$this->gdoColumnDefineB()},\n".
        "{$this->name}_text {$this->gdoColumnDefineB()}\n";
    }
    
    public static function decodeMessage($s)
    {
        if ($s === null)
        {
            return null;
        }
        return call_user_func(self::$DECODER, $s);
    }
    
    public function blankData()
    {
        $decoded = self::decodeMessage($this->initial);
        $text = self::plaintext($decoded);
        return [
            "{$this->name}_input" => $this->initial,
            "{$this->name}_output" => $decoded,
            "{$this->name}_text" => $text,
        ];
    }
    
    public function setGDOData(GDO $gdo)
    {
        $this->input = $gdo->getVar("{$this->name}_input");
        $this->output = $gdo->getVar("{$this->name}_output");
        $this->text = $gdo->getVar("{$this->name}_text");
        return $this;
    }
    
    public function getGDOData()
    {
        $decoded = self::decodeMessage($this->getVar());
        $text = self::plaintext($decoded);
        return [
            "{$this->name}_input" => $this->input,
            "{$this->name}_output" => $decoded,
            "{$this->name}_text" => $text,
        ];
    }
    
    public function var($var=null)
    {
        $this->input = $var;
        $this->output = self::decodeMessage($var);
        $this->text = self::plaintext($this->output);
        return parent::var($var);
    }
    
    public function initial($var=null)
    {
        $this->input = $var;
        $this->output = self::decodeMessage($var);
        $this->text = self::plaintext($this->output);
        return parent::initial($var);
    }
    
    public function toValue($var)
    {
        return self::decodeMessage($var);
    }
    
    public function getVar() { $form = $this->formVariable(); return $form ? $this->getRequestVar($form, $this->input, "{$this->name}") : $this->input; }
    public function getVarInput() { $form = $this->formVariable(); return $form ? $this->getRequestVar($form, $this->input, "{$this->name}") : $this->input; }
    public function getVarOutput() { $form = $this->formVariable(); return $form ? $this->getRequestVar($form, $this->output, "{$this->name}_output") : $this->output; }
    
    ##############
	### Render ###
	##############
    public function renderCell() { return $this->getVarOutput(); }
    public function renderCard() { return '<div class="gdt-message-card">'.$this->getVarOutput().'</div>'; }
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
	public static function getPurifier()
	{
		static $purifier;
		if (!isset($purifier))
		{
			require GDO_PATH . 'GDO/UI/htmlpurifier/library/HTMLPurifier.auto.php';
			$config = \HTMLPurifier_Config::createDefault();
			$config->set('URI.Host', GWF_DOMAIN);
			$config->set('HTML.Nofollow', true);
			$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
			$config->set('URI.DisableExternalResources', false);
			$config->set('URI.DisableResources', false);
			$config->set('HTML.TargetBlank', true);
			$config->set('HTML.Allowed', 'a[href|rel|target],p,pre[class],code[class],img[src|alt],figure[style|class],figcaption');
			$config->set('Attr.DefaultInvalidImageAlt', t('img_not_found'));
			$config->set('HTML.SafeObject', true);
			$config->set('Attr.AllowedRel', array('nofollow'));
			$config->set('HTML.DefinitionID', 'gdo6-message');
			$config->set('HTML.DefinitionRev', 1);
			if ($def = $config->maybeGetRawHTMLDefinition())
			{
			    $def->addElement('figcaption', 'Block', 'Flow', 'Common');
			    $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
			}
			$purifier = new \HTMLPurifier($config);
		}
		return $purifier;
	}
	
}
