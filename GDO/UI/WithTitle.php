<?php
namespace GDO\UI;
trait WithTitle
{
	public $title;
	public function title($title) { $this->title = $title; return $this; }
}
