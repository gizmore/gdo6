<?php
namespace GDO\UI;

/**
 * Add text variable to a GDT.
 * 
 * Evaluated lazy.
 * @author gizmore
 */
trait WithText
{
    private $textKey = null;
    private $textArgs = null;
    public function text($key, array $args=null) { $this->textKey = $key; $this->textArgs = $args; return $this; }
    
    private $textRaw = null;
    public function textRaw($text) { $this->textRaw = $text; return $this; }
    
    public function hasText()
    {
        return $this->textKey || $this->textRaw;
    }
    
    public function renderText()
    {
        return $this->textRaw ? $this->textRaw : t($this->textKey, $this->textArgs);
    }
    
}
