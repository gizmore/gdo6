<?php
namespace GDO\Language\Method;

use GDO\Core\MethodAjax;
use GDO\Language\Trans;
use GDO\Core\Website;

/**
 * Get all translation data for the current language.
 * @author gizmore
 * @version 6.10
 * @since 6.02
 */
final class GetTransData extends MethodAjax
{
	public function execute()
	{
	    Website::renderJSON(Trans::getCache(Trans::$ISO));
	}
	
}
