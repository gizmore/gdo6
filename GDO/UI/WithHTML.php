<?php
namespace GDO\UI;
/**
 * Adds plain html variable.
 * @author gizmore
 */
trait WithHTML
{
    public $html;
    public function html($html=null) { $this->html = $html; return $this; }
}
