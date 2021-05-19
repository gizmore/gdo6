<?php
namespace GDO\UI;
/**
 * Default icon provider using UTF8 icons.
 * This is the most primitive and cheap icon rendering.
 * It is included in the core, and a reference for possible icons.
 * However, the possible icons are not limited to the few used ones.
 * @author gizmore
 * @since 6.05
 * @version 6.10
 * @see https://www.utf8icons.com/
 */
final class GDT_IconUTF8
{
    public static $MAP = array(
        'account' => '⛁',
        'add' => '✚',
        'alert' => '!',
        'all' => '▤',
        'arrow_down' => '▼',
        'arrow_left' => '←',
        'arrow_right' => '‣',
        'arrow_up' => '▲',
        'audio' => '🎵',
        'back' => '↶',
        'bars' => '☰',
        'birthday' => '🎂',
        'block' => '✖',
        'book' => '📖',
        'bulb' => '💡',
        'calendar' => '📅',
        'captcha' => '♺',
        'caret' => '⌄',
        'country' => '⚑',
        'check' => '✔',
        'create' => '✚',
        'credits' => '¢',
        'cut' => '✂',
        'delete' => '✖',
        'download' => '⇩',
        'edit' => '✎',
        'email' => '✉',
        'error' => '⚠',
        'face' => '☺',
        'female' => '♀',
        'file' => '🗎',
        'flag' => '⚑',
        'folder' => '📁',
        'font' => 'ᴫ',
        'gender' => '⚥',
        'group' => '😂',
        'guitar' => '🎸',
        'help' => '💡',
        'image' => '📷',
        'language' => '⚐',
        'level' => '🏆',
        'like' => '❤',
        'link' => '🔗',
        'list' => '▤',
        'lock' => '🔒',
        'male' => '♂',
        'message' => '☰',
        'minus' => '-',
        'money' => '💰',
        'password' => '⚷',
        'pause' => '⏸',
        'phone' => '📞',
        'plus' => '+',
        'plus_one' => '+1',
        'quote' => '↶',
        'remove' => '✕',
        'reply' => '☞',
        'schedule' => '☷',
        'search' => '🔍',
        'settings' => '⚙',
        'star' => '★',
        'table' => '☷',
        'tag' => '⛓',
        'thumbs_up' => '👍',
        'thumbs_down' => '👎',
        'thumbs_none' => '👉',
        'time' => '⌚',
        'title' => 'T',
        'trophy' => '🏆',
        'upload' => '⇧',
        'url' => '🌐',
        'user' => '☺',
        'users' => '😂',
        'view' => '👁',
        'wait' => '◴',
    );
    
	public static function iconS($icon, $iconText, $style)
	{
	    $title = $iconText ? ' title="'.html($iconText).'"' : '';
		$_icon = isset(self::$MAP[$icon]) ? self::$MAP[$icon] : $icon;
		return "<span class=\"gdo-icon gdo-utf8-icon-$icon\"$style$title>$_icon</span>";
	}

}
