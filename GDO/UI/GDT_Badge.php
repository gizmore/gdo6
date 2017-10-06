<?php
namespace GDO\UI;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
/**
 * A label with a counter.
 * @author gizmore
 * @since 6.00
 * @version 7.00
 */
class GDT_Badge extends GDT
{
    use WithLabel;
    
    public $badge;
    public function badge($badge) { $this->badge = $badge; return $this; }
    
    public function renderCell() { return GDT_Template::php('UI', 'cell/badge.php', ['field' => $this]); }
}
