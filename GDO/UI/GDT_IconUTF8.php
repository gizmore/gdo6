<?php
namespace GDO\UI;
/**
 * Default icon provider using UTF8 icons.
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
			'arrow_down' => '▼',
			'arrow_left' => '←',
			'arrow_right' => '‣',
			'arrow_up' => '▲',
			'audio' => '♬',
			'back' => '↶',
			'block' => '✖',
			'book' => '📖',
			'bulb' => '💡',
			'calendar' => '📅',
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
			'file' => '🗎',
			'flag' => '⚑',
			'folder' => '📁',
			'group' => '😂',
			'gender' => '⚥',
			'help' => '☛',
			'image' => '📷',
			'level' => 'LVL',
			'like' => '❤',
			'link' => '⚓',
			'list' => '▤',
			'lock' => '🔒',
			'male' => '♂',
			'message' => '☶',
			'money' => '€',
			'password' => '⚷',
			'pause' => '⏸',
			'phone' => '☎',
			'plus_one' => '+1',
			'quote' => '↶',
			'reply' => '☞',
			'search' => '.o',
			'settings' => '⚙',
			'star' => '★',
			'time' => '⌛',
			'title' => 'T',
			'url' => '🌐',
			'users' => '😂',
			'view' => '👁',
			'wait' => '◴',
		);
		$icon = isset($map[$icon]) ? $map[$icon] : $icon;
		return "<span class=\"gdo-icon\"$style title=\"$iconText\">$icon</span>";
	}
}
