<?php
namespace GDO\Language\Method;

use GDO\Core\MethodAjax;
use GDO\Language\Trans;
use GDO\Core\Application;
use GDO\Core\GDT_Array;

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
		# Get data
		$trans = Trans::getCache(Trans::$ISO);
		
		# 
		if (Application::instance()->isJSON())
		{
			return GDT_Array::makeWith($trans);
		}
		
	    $langdata = json_encode($trans, JSON_PRETTY_PRINT);
	    $code = sprintf('window.GDO_TRANS = {}; window.GDO_TRANS.CACHE = %s;', $langdata);
	    if (!Application::instance()->isUnitTests())
	    {
	        hdr('Content-Type: text/javascript');
	        die($code);
	    }
	}
	
}
