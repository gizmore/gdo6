<?php
namespace GDO\UI;
trait WithHREF
{
    public $href;
    /**
     * @param string $href
     * @return self
     */
    public function href($href=null) { $this->href = $href; return $this; }
    public function htmlHREF() { return sprintf(" href=\"%s\"", html($href)); }
}
