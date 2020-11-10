<?php
namespace GDO\UI;

trait WithImageSize
{
    public $imageWidth = 32;
    public $imageHeight = 32;
    
    public function imageSize($w, $h=null)
    {
        $this->imageWidth = $w;
        $this->imageHeight = $h ? $h : $w;
        return $this;
    }
    
}
