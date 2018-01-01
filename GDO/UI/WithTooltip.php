<?php
namespace GDO\UI;

trait WithTooltip
{
	use WithIcon;
	
    public function tooltip($tooltipText=null)
    {
    	if (!$this->icon)
    	{
    		$this->icon('help');
    	}
    	return $this->iconText($tooltipText);
    }

    public static function with($tooltipText)
    {
    	return self::make()->tooltip($tooltipText)->render();
    }
}
