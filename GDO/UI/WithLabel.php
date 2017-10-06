<?php
namespace GDO\UI;
trait WithLabel
{
    public $label;
    public function rawLabel($label=null) { $this->label = $label; return $this; }
    public function label($key, array $args=null) { return $this->rawLabel(t($key, $args)); }
    public function noLabel() { return $this->rawLabel(); }
    public function defaultLabel() { return $this->label($this->name); }
    public function displayLabel() { return $this->label; }
    public function name($name=null) { $this->name = $name ? $name : self::nextName(); return $this->defaultLabel(); }
}
