<?php
namespace GDO\Core;

/**
 * Raw data retrieval methods.
 * 
 * - Does not store last url.
 * - Rendering is done without page template.
 * - Does not lock session by default.
 * - Does not show up in sitemap.
 * - Is not indexed by robots.
 * - Does not work in CLI.
 * 
 * @author gizmore
 * @version 6.11.1
 * @since 6.2.0
 */
abstract class MethodAjax extends Method
{
    public function isCLI() { return false; }
    public function isAjax() { return true; }
	public function saveLastUrl() { return false; }
	public function isSEOIndexed() { return false; }
	public function showInSitemap() { return false; }
	public function isLockingSession() { return false; }
	
}
