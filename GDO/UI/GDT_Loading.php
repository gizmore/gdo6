<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

final class GDT_Loading extends GDT
{
    public $name = 'loading';
    
    public function renderCell() { return GDT_Template::php('UI', 'cell/loading.php', ['field' => $this]); }

}
