<?php
namespace GDO\Core;

/**
 * An ajax method does not store last url.
 * Rendering is done without page template.
 * @author gizmore
 * @version 6.10
 * @since 6.02
 */
abstract class MethodAjax extends Method
{
	public function isAjax() { return true; }
	public function saveLastUrl() { return false; }
	
}
