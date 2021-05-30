<?php
namespace GDO\UI;

/**
 * Add a label to a GDT.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.2.0
 */
trait WithLabel
{
    /**
     * @param string $name
     * @return self
     * @override
     */
    public function name($name=null) { $this->name = $name; return $this->defaultLabel(); }

    
    public $label;
	public $labelArgs;
	public $labelRaw;

	public function noLabel()
	{
	    $this->label = $this->labelArgs = $this->labelRaw = null;
	    return $this;
	}
	
	public function hasLabel() { return $this->label || $this->labelRaw; }
	
	public function defaultLabel() { return $this->label($this->name); }
	
	/**
	 * @param string $key
	 * @param array $args
	 * @return static
	 */
	public function label($key, array $args=null)
	{
	    $this->labelRaw = null;
	    $this->label = $key;
	    $this->labelArgs = $args;
	    return $this;
	}
	
	public function labelRaw($label=null)
	{
	    $this->labelRaw = $label;
	    $this->label = $this->labelArgs = null;
	    return $this;
	}
	
	public function displayLabel()
	{
		if ($this->labelRaw)
		{
		    return $this->labelRaw;
		}
		if ($this->label)
		{
			return t($this->label, $this->labelArgs);
		}
		return '';
	}

}
