<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;

/**
 * A panel with a title that collapses onclick.
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class GDT_Accordeon extends GDT_Panel
{
    public function renderCell()
    {
        return GDT_Template::php('UI', 'cell/accordeon.php', ['field' => $this]);
    }

    public function renderCard()
    {
        return GDT_Template::php('UI', 'card/accordeon.php', ['field' => $this]);
    }
    
}
