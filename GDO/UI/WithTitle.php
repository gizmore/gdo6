<?php
namespace GDO\UI;

/**
 * Adds a title to a GDT.
 * This title is not rendered with a H tag.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.0.1
 * @see GDT_Headline
 */
trait WithTitle
{
	public $titleKey;
	public $titleArgs;
	public function title($key, array $args=null)
	{
	    $this->titleKey = $key;
	    $this->titleArgs = $args;
	    return $this;
	}
	
	public $titleRaw;
	public function titleRaw($title)
	{
	    $this->titleRaw = $title;
	    $this->titleKey = null;
	    $this->titleArgs = null;
	    return $this;
	}

	public $titleEscaped = true;
	public function titleEscaped($escaped)
	{
	    $this->titleEscaped = $escaped;
	    return $this;
	}
	
	##############
	### Render ###
	##############
	public function hasTitle() { return $this->titleKey || $this->titleRaw; }
	public function renderTitle() { return $this->hasTitle() ? ($this->titleRaw ? $this->titleRaw : t($this->titleKey, $this->titleArgs)) : ''; }
	public function noTitle() { return $this->titleRaw(''); }

}
