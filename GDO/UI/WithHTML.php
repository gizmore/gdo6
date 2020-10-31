<?php
namespace GDO\UI;
/**
 * Adds plain html variable.
 * @author gizmore
 */
trait WithHTML
{
	/**
	 * @param $html
	 * @return self
	 */
	public static function withHTML($html) { return self::make()->html($html); }

	############
	### HTML ###
	############
	public $html = '';
	public function html($html=null) { $this->html = $html; return $this; }
	
}
