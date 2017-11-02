<?php
namespace GDO\UI;

trait WithHTMLClass
{
	public $htmlKlass;
	public function klass($klass) { $this->htmlKlass = $klass; return $this; }
	public function htmlKlass() { return $this->htmlKlass ? "class=\"{$this->htmlKlass}\"" : ''; }
	
	public $htmlStyle;
	public function style($style) { $this->htmlStyle = $style; return $this; }
	public function htmlStyle() { return $this->htmlStyle ? "style=\"{$this->htmlStyle}\"" : ''; } 
}
