<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\DB\GDT_String;

/**
 * Arbitrary method call on a gdo for cell display.
 * 
 * @deprecated Ugly idea
 * @author gizmore
 * @since 6.10.1
 */
final class GDT_Cell extends GDT
{
	use WithLabel;
	
	public $method = null;
	public $methodArgs = null;
	public function method($method=null, $args=null)
	{
		$this->method = $method;
		$this->methodArgs = $args;
		return $this;
	}
	
	public function callMethod()
	{
		return call_user_func([$this->gdo, $this->method]);
	}
	
	public function render()
	{
		return $this->callMethod();
	}

	public function renderCell()
	{
		return $this->callMethod();
	}
	
	public function renderHeader()
	{
		return GDT_String::make()->label($this->label, $this->labelArgs)->renderHeader();
	}
	
	public function renderJSON()
	{
		return [
			$this->name => $this->callMethod(),
		];
	}

}
