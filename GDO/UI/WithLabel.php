<?php
namespace GDO\UI;

/**
 * Add a label to a GDT.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 6.2.0
 */
trait WithLabel
{
    /**
     * @param string $name
     * @return static
     */
    public function name($name=null) { $this->name = $name; return $this->defaultLabel(); }
    
    public $label;
	public $labelArgs;
	public $labelRaw;

	public function noLabel() { return $this->labelRaw(''); }
	public function hasLabel() { return $this->label || $this->labelRaw; }
	public function defaultLabel() { return $this->label($this->name); }
	
	/**
	 * @param string $key
	 * @param array $args
	 * @return static
	 */
	public function label($key, array $args=null) { $this->labelRaw = null; $this->label = $key; $this->labelArgs = $args; return $this; }
	public function labelRaw($label=null) { $this->labelRaw = $label; return $this; }
	
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
	}

}
