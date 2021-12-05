<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\GDO;
use GDO\Util\Strings;
use GDO\User\GDO_User;
use GDO\Profile\GDT_ProfileLink;
use GDO\DB\GDT_Text;

/**
 * A message is a GDT_Text with an editor. Classic uses a textarea.
 * The content is html, filtered through a whitelist with html-purifier.
 * A gdo6-tinymce / ckeditor is available. Planned is markdown and bbcode.
 * 
 * @TODO: write a Markdown module. Hook into DECODE() to turn input markdown into output html.
 * 
 * @see \GDO\TinyMCE\Module_TinyMCE
 * @see \GDO\CKEditor\Module_CKEditor
 * @see \GDO\Markdown\Module_Markdown
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 4.0.0
 */
class GDT_Message extends GDT_Text
{
    public $icon = 'message';
    
    private $input; # Raw user input
    private $output; # Decoded input to output 
    private $text; # Output with removed html for search
    private $editor; # Message Codec Provider used for this message.
    
    ###########
    ### GDT ###
    ###########
    public static $NUM = 1;
    public $num = 0;
    /**
     * On make, setup order and search field.
     * @param string $name
     * @return self
     */
    public static function make($name=null)
    {
        $gdt = parent::make($name);
        $gdt->num = self::$NUM++;
        $gdt->orderField = $gdt->name . '_text';
        $gdt->searchField = $gdt->name . '_text';
        return $gdt;
    }
    
    ##############
    ### Quoter ###
    ##############
    public static $QUOTER = [self::class, 'QUOTE'];
    public static function QUOTE(GDO_User $user, $date, $text)
    {
        $link = GDT_ProfileLink::make()->withNickname()->forUser($user);
        return sprintf("<div><blockquote>\n<span class=\"quote-by\">%s</span>\n<span class=\"quote-from\">%s</span>\n%s</blockquote>&nbsp;</div>\n",
            t('quote_by', [$link->render()]), t('quote_at', [tt($date)]), $text);
    }
    
    public static function quoteMessage(GDO_User $user, $date, $text)
    {
        return call_user_func(self::$QUOTER, $user, $date, $text);
    }
    
    ###############
    ### Decoder ###
    ###############
    public static $EDITOR_NAME = 'GDT';
    public static $DECODER = [self::class, 'DECODE'];
    public static $DECODERS = [
    	'GDT' => [self::class, 'DECODE'],
    	'NONE' => [self::class, 'NONDECODE'],
    ];
    
    public static function setDecoder($decoder)
    {
    	self::$EDITOR_NAME = $decoder;
    	self::$DECODER = self::$DECODERS[$decoder];
    }

    public static function DECODE($s)
    {
    	return self::getPurifier()->purify($s);
    }
    
    public static function NONDECODE($s)
    {
    	return $s;
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
        $html = html_entity_decode($html, ENT_HTML5);
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
            $config->set('URI.Host', GDO_DOMAIN);
            $config->set('HTML.Nofollow', true);
            $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
            $config->set('URI.DisableExternalResources', false);
            $config->set('URI.DisableResources', false);
            $config->set('HTML.TargetBlank', true);
            $config->set('HTML.Allowed', 'br,a[href|rel|target],p,pre[class],code[class],img[src|alt],figure[style|class],figcaption,center,b,i,div[class],h1,h2,h3,h4,h5,h6');
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
        $decoded = self::decodeMessage($this->toVar($value));
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
        $this->editor = self::$EDITOR_NAME;
        return true;
    }
    
    ##########
    ### DB ###
    ##########
    public function gdoColumnNames()
    {
    	return [
    		"{$this->name}_input",
    		"{$this->name}_output",
    		"{$this->name}_text",
    		"{$this->name}_editor",
    	];
    }
    
    public function gdoColumnDefine()
    {
        return
        "{$this->name}_input {$this->gdoColumnDefineB()},\n".
        "{$this->name}_output {$this->gdoColumnDefineB()},\n".
        "{$this->name}_text {$this->gdoColumnDefineB()},\n".
        "{$this->name}_editor VARCHAR(16) CHARSET ascii COLLATE ascii_bin\n";
    }
    
    ######################
    ### 3 column hacks ###
    ######################
    public function initial($var=null)
    {
        $this->input = $var;
        $this->output = self::decodeMessage($var);
        $this->text = self::plaintext($this->output);
        $this->editor = $this->nowysiwyg ? 'GDT' : self::$EDITOR_NAME;
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
        $this->editor = $this->nowysiwyg ? 'GDT' : self::$EDITOR_NAME;
        return parent::var($var);
    }
    
    public function blankData()
    {
        return [
            "{$this->name}_input" => $this->input,
            "{$this->name}_output" => $this->output,
            "{$this->name}_text" => $this->text,
            "{$this->name}_editor" => self::$EDITOR_NAME,
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
        $this->input = $gdo->getVar("{$name}_input");
        $this->output = $gdo->getVar("{$name}_output");
        $this->text = $gdo->getVar("{$name}_text");
        $this->editor = $gdo->getVar("{$name}_editor");
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
            "{$this->name}_editor" => $this->editor,
        ];
    }
    
    ##############
    ### Getter ###
    ##############
    public function getVar()
    {
        $form = $this->formVariable();
        if ($form)
        {
            return $this->getRequestVar($form, $this->input);
        }
        return $this->var;
    }
    public function getVarInput() { return $this->input; }
    public function getVarOutput() { return $this->output; }
    public function getVarText() { return $this->text; }
    
    ##############
	### Render ###
	##############
    public function renderCLI() { return $this->getVarText(); }
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
