<?php
namespace GDO\UI;
/**
 * UTF8 icon provider.
 * This is the most primitive and cheap icon rendering.
 * It is included in the core, and a reference for possible icons.
 * However, the possible icons are not limited to the few used ones.
 * @author gizmore
 * @since 6.05
 * @version 6.05
 */
final class GDT_IconUTF8
{
    public static function iconS($icon)
    {
        static $map = array(
            'create' => '+',
        );
        $icon = isset($map[$icon]) ? $map[$icon] : $icon;
        return "<span gdo-icon>{$icon}</span>";
    }
}
