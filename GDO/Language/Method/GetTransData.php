<?php
namespace GDO\Language\Method;

use GDO\Core\MethodAjax;
use GDO\Language\Trans;
use GDO\Core\Application;

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
	    $langdata = json_encode(Trans::getCache(Trans::$ISO), JSON_PRETTY_PRINT);
	    $code = sprintf('window.GDO_TRANS = {}; window.GDO_TRANS.CACHE = %s;', $langdata);
	    if (!Application::instance()->isUnitTests())
	    {
	        echo $code;
	    }
	}
	
}
