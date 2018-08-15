<?php
namespace GDO\UI;
/**
 * Add HTML href capabilities.
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
trait WithHREF
{
	public $href;
	/**
	 * @param string $href
	 * @return self
	 */
	public function href($href=null) { $this->href = $href; return $this; }
	public function htmlHREF() { return sprintf(" href=\"%s\"", html($this->href)); }
}
