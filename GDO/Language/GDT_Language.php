<?php
namespace GDO\Language;

use GDO\DB\GDT_ObjectSelect;

final class GDT_Language extends GDT_ObjectSelect
{
	public function defaultLabel() { return $this->label('language'); }
	
	protected function __construct()
	{
	    parent::__construct();
		$this->table(GDO_Language::table());
		$this->min = $this->max = 2;
		$this->icon('language');
	}
	
	public $withName = false;
	public function withName($withName=true) { $this->withName = $withName; return $this; }

	###############
	### Choices ###
	###############
	private $all = false;
	public function all($all=true)
	{
		$this->all = $all;
		return $this;
	}
	
	public function initChoices()
	{
		return $this->choices ? $this : $this->choices($this->languageChoices());
	}
	
	private function languageChoices()
	{
		$languages = GDO_Language::table();
		return $this->all ? $languages->all() : $languages->allSupported();
	}
	
	##################
	### Completion ###
	##################
	public function withCompletion()
	{
		return $this->completionHref(href('Language', 'Completion'));
	}

}
