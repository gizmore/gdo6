<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\DB\GDT_Text;
use GDO\Core\GDO;
use GDO\Util\Strings;

/**
 * A message is a GDT_Text with an editor. Classic a textarea.
 * The content is html, filtered through a whitelist with html-purifier.
 * The default editor is simply a textarea, and a gdo6-tinymce / ckeditor is available.
 * 
 * @todo: write a Markdown module. Hook into DECODE() to turn input markdown into output html.
 * 
 * @see \GDO\TinyMCE\Module_TinyMCE
 * @see \GDO\CKEditor\Module_CKEditor
 * 
 * @author gizmore
 * @version 6.11
 * @since 3.00
 */
class GDT_Message extends GDT_Text
{
    public $icon = 'message';
    
    private $input; # Raw user input
    private $output; # Decoded input to output 
    private $text; # Output with removed html for search
    
    ###########
    ### GDT ###
    ###########
    /**
     * On make, setup order and search field.
     * @param string $name
     * @return self
     */
    public static function make($name=null)
    {
        $gdt = parent::make($name);
        $gdt->orderField = $gdt->name . '_text';
        $gdt->searchField = $gdt->name . '_text';
        return $gdt;
    }
    
    ###############
    ### Decoder ###
    ###############
    public static $DECODER = [self::class, 'DECODE'];
    public static function DECODE($s)
    {
        return '<div class="gdt-message ck-content">' . self::getPurifier()->purify($s) . '</div>';
    }
    
    public static function decodeMessage($s)
    {
        if ($s === null)
        {
            return null;
        }
        return call_user_func(self::$DECODER, $s);
    }
    
    public static function plaintext($html)
    {
        if ($html === null)
        {
            return null;
        }
        $html = preg_replace("#\r?\n#", ' ', $html);
        $html = preg_replace('#<a .*href="(.*)".*>(.*)</a>#i', ' $2($1) ', $html);
        $html = preg_replace('#</p>#i', "\n", $html);
        $html = preg_replace('#<[^\\>]*>#', ' ', $html);
        $html = preg_replace('# +#', ' ', $html);
        return trim($html);
    }
    
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
            $config->set('HTML.Allowed', 'br,a[href|rel|target],p,pre[class],code[class],img[src|alt],figure[style|class],figcaption');
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
    
    /**
     * Validate via String validation twice, the input and output variants.
     * {@inheritDoc}
     * @see \GDO\DB\GDT_Text::validate()
     */
    public function validate($value)
    {
        # Check raw input for length and pattern etc.
        if (!parent::validate($value))
        {
            return false;
        }
        
        # Decode the message
        $decoded = self::decodeMessage($this->getVar());
        $text = self::plaintext($decoded);
        
        # Check decoded input for length and pattern etc.
        if (!parent::validate($decoded))
        {
            return false;
        }
        
        # Assign input variations.
        $this->input = $value;
        $this->output = $decoded;
        $this->text = $text;
        return true;
    }

//     public function getValidationValue()
//     {
//         return $this->getVarInput();
//     }
    
    ##########
    ### DB ###
    ##########
    public function gdoColumnDefine()
    {
        return
        "{$this->name}_input {$this->gdoColumnDefineB()},\n".
        "{$this->name}_output {$this->gdoColumnDefineB()},\n".
        "{$this->name}_text {$this->gdoColumnDefineB()}\n";
    }
    
    ######################
    ### 3 column hacks ###
    ######################
    
    public function initial($var=null)
    {
        $this->input = $var;
        $this->output = self::decodeMessage($var);
        $this->text = self::plaintext($this->output);
        return parent::initial($var);
    }
    
    /**
     * If we set a var, value and plaintext get's precomputed.
     * {@inheritDoc}
     * @see \GDO\Core\GDT::var()
     */
    public function var($var=null)
    {
        $this->input = $var;
        $this->output = self::decodeMessage($var);
        $this->text = self::plaintext($this->output);
        return parent::var($var);
    }
    
    public function blankData()
    {
        return [
            "{$this->name}_input" => $this->input,
            "{$this->name}_output" => $this->output,
            "{$this->name}_text" => $this->text,
        ];
    }
    
    /**
     * Set GDO Data is called when the GDO sets up the GDT.
     * We copy the 3 text columns and revert a special naming hack in module news; 'iso][en][colum_name' could be it's name.
     * {@inheritDoc}
     * @see \GDO\Core\GDT::setGDOData()
     */
    public function setGDOData(GDO $gdo)
    {
        $name = Strings::rsubstrFrom($this->name, '[', $this->name); # @XXX: ugly hack for news tabs!
        if ($gdo->hasVar("{$name}_input"))
        {
            $this->input = $gdo->getVar("{$name}_input");
            $this->output = $gdo->getVar("{$name}_output");
            $this->text = $gdo->getVar("{$name}_text");
        }
        return $this;
    }
    
    /**
     * getGDOData() is called when the gdo wants to update it's gdoVars.
     * This happens when formData() is plugged into saveVars() upon update and creation.
     * {@inheritDoc}
     * @see \GDO\Core\GDT::getGDOData()
     */
    public function getGDOData()
    {
        return [
            "{$this->name}_input" => $this->input,
            "{$this->name}_output" => $this->output,
            "{$this->name}_text" => $this->text,
        ];
    }
    
//     /**
//      * Turn an output string to an input string.
//      * {@inheritDoc}
//      * @see \GDO\Core\GDT::toVar()
//      */
//     public function toVar($value)
//     {
//         return "<pre>{$value}\n</pre>\n";
//     }
    
//     public function toValue($var)
//     {
//         return $this->output; #self::decodeMessage($var);
//     }
    
    ##############
    ### Getter ###
    ##############
    public function getVar()
    {
        $form = $this->formVariable();
        if ($form)
        {
            return $this->getRequestVar($form, $this->var, "{$this->name}");
        }
        return $this->var;
    }
    public function getVarInput() { return $this->input; }
    public function getVarOutput() { return $this->output; }
    public function getVarText() { return $this->text; }
    
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
	
}
