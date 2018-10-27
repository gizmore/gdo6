<?php
namespace GDO\UI;
trait WithLabel
{
	public $label = null;
	public $labelArgs = null;
	public $labelRaw = null;

	public function defaultLabel() { return $this->label($this->name); }
	public function noLabel() { return $this->rawLabel(''); }
	public function label($key, array $args=null) { $this->labelRaw = null; $this->label = $key; $this->labelArgs = $args; return $this; }
	public function rawLabel($label=null) { $this->labelRaw = $label; return $this; }
	public function displayLabel() { return $this->labelRaw ? $this->labelRaw : t($this->label, $this->labelArgs); }
	public function name($name=null) { $this->name = $name ? $name : self::nextName(); return $this->defaultLabel(); }
}
