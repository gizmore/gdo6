<?php
namespace GDO\Core;

/**
 * An ajax method does not store last url.
 * Rendering is done without page template.
 * It does not lock session.
 * 
 * @author gizmore
 * @version 6.10.2
 * @since 6.2.0
 */
abstract class MethodAjax extends Method
{
	public function isAjax() { return true; }
	public function saveLastUrl() { return false; }
	public function showInSitemap() { return false; }
	public function isLockingSession() { return false; }
	
}
