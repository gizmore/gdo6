<?php
namespace GDO\Language\Method;

use GDO\Core\MethodAjax;
use GDO\Language\Trans;
use GDO\Core\Application;

/**
 * Get all translation data for the current language.
 * Javascript applications use this if enabled.
 * 
 * @author gizmore
 * @version 6.11.3
 * @since 6.2.0
 */
final class GetTransData extends MethodAjax
{
	public function execute()
	{
	    $langdata = json_encode(Trans::getCache(Trans::$ISO), JSON_PRETTY_PRINT);
	    $code = sprintf('window.GDO_TRANS = {}; window.GDO_TRANS.CACHE = %s;', $langdata);
	    if (!Application::instance()->isUnitTests())
	    {
	        hdr('Content-Type: application/javascript');
	        die($code);
	    }
	}
	
}
