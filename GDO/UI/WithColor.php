<?php
namespace GDO\UI;

/**
 * 
 * @author gizmore
 * @since 6.10.4
 */
trait WithColor
{
    public $colorFG = null;
    public $colorBG = null;
    
    public function color($colorFG, $colorBG)
    {
        return $this->colorFG($colorFG)->colorBG($colorBG);
    }
    
    public function colorFG($colorFG)
    {
        $this->colorFG = $colorFG;
        return $this;
    }
    
    public function colorBG($colorBG)
    {
        $this->colorBG = $colorBG;
        return $this;
    }
    
}
