<?php
namespace GDO\Language\Method;
use GDO\Core\MethodAjax;
use GDO\Language\Trans;
use GDO\Core\Website;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_JSON;
use GDO\Core\Method;

final class GetTranslationData extends MethodAjax
{
	public function execute()
	{
		return GDT_Response::makeWith(GDT_JSON::make()->value(Trans::getCache(Trans::$ISO)));
	}
	
}
