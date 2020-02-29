<?php
namespace GDO\UI;

trait WithTooltip
{
	public $tooltipText = null;
	public $tooltipTextArgs = null;
	
	public function tooltip($tooltipText, array $tooltipTextArgs=null)
	{
		$this->tooltipText = $tooltipText;
		$this->tooltipTextArgs = $tooltipTextArgs;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function htmlTooltip()
	{
		if ($this->tooltipText)
		{
			return GDT_Icon::iconS($this->icon, t($this->tooltipText, $this->tooltipTextArgs));
		}
	}
	
}
