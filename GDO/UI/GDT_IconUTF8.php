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
    public static function iconS($icon, $iconText, $style)
    {
        static $map = array(
            'account_box' => '⛁',
            'add' => '✚',
            'alert' => '!',
        	'all' => '▤',
            'alarm_on' => '☄',
            'arrow_drop_down' => '▼',
            'arrow_drop_up' => '▲',
            'arrow_right' => '‣',
            'audio' => '♬',
        	'back' => '↶',
        	'block' => '✖',
        	'captcha' => '♺',
        	'country' => '⚑',
            'check' => '✔',
            'create' => '✚',
            'credits' => '¢',
            'cut' => '✂',
            'date_range' => '◴',
            'delete' => '✖',
            'delete_sweep' => '✖',
        	'download' => '⇩',
            'edit' => '✎',
            'email' => '✉',
            'enhanced_encryption' => '⚷',
        	'error' => '⚠',
            'face' => '☺',
            'female' => '♀',
        	'group' => '😂',
        	'gender' => '⚥',
        	'help' => '☛',
        	'image' => '📷',
        	'language' => '🌐',
        	'level' => 'LVL',
        	'like' => '❤',
        	'link' => '⚓',
        	'list' => '▤',
        	'male' => '♂',
            'message' => '☶',
        	'money' => '€',
            'password' => '⚷',
            'phone' => '☎',
        	'plus_one' => '+1',
            'quote' => '↶',
            'reply' => '☞',
        	'search' => '.o',
            'settings' => '⚙',
            'stars' => '★',
        	'title' => 'T',
        	'users' => 'USERS',
        	'wait' => '◴',
        );
        $icon = isset($map[$icon]) ? $map[$icon] : $icon;
        return "<span class=\"gdo-icon\"$style title=\"$iconText\">$icon</span>";
    }
}
