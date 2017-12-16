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
    public static function iconS($icon, $style)
    {
        static $map = array(
            'account_box' => '[]',
            'add' => '✚',
            'alert' => '◴',
            'alarm_on' => '☄',
            'arrow_drop_down' => '▼',
            'arrow_drop_up' => '▲',
            'arrow_right' => '‣',
            'audio' => '♬',
        	'back' => '↶',
        	'captcha' => 'CPT',
            'check' => '✔',
            'create' => '✚',
            'credits' => '¢',
            'cut' => '✂',
            'date_range' => '◴',
            'delete' => '✖',
            'delete_sweep' => '✖',
            'edit' => '✎',
            'email' => '✉',
            'enhanced_encryption' => '⚷',
        	'error' => '⚠',
            'face' => '☺',
            'female' => '♀',
        	'group' => '😂',
        	'language' => '🌐',
            'link' => '⚓',
            'male' => '♂',
            'message' => '☶',
        	'money' => '€',
            'password' => '⚷',
            'phone' => '☎',
        	'plus_one' => '+1',
            'quote' => '↶',
            'reply' => '☞',
            'settings' => '⚙',
            'stars' => '★',
        	'users' => 'USERS',
        );
        $icon = isset($map[$icon]) ? $map[$icon] : $icon;
        return "<span class=\"gdo-icon\"$style>$icon</span>";
    }
}
