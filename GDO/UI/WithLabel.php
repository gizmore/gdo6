<?php
namespace GDO\UI;

trait WithLabel
{
	public $label;
	public $labelArgs;
	public $labelRaw;

	public function name($name=null) { $this->name = $name ? $name : self::nextName(); return $this->defaultLabel(); }

	public function hasLabel() { return $this->label || $this->labelRaw; }
	public function defaultLabel() { return $this->label($this->name); }
	public function noLabel() { return $this->rawLabel(''); }
	
	/**
	 * @param string $key
	 * @param array $args
	 * @return self
	 */
	public function label($key, array $args=null) { $this->labelRaw = null; $this->label = $key; $this->labelArgs = $args; return $this; }
	public function rawLabel($label=null) { $this->labelRaw = $label; return $this; }
	
	public function displayLabel()
	{
		if (isset($this->labelRaw))
		{
		    return $this->labelRaw;
		}
		if (isset($this->label))
		{
			return t($this->label, $this->labelArgs);
		}
		return '';
	}

}
