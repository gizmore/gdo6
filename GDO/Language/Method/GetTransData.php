<?php
namespace GDO\Language\Method;
use GDO\Core\MethodAjax;
use GDO\Language\Trans;

final class GetTransData extends MethodAjax
{
	public function execute()
	{
		header("ContentType: text/javascript");
		$json = json_encode(Trans::getCache(Trans::$ISO));
		echo "window.GDO_TRANS = { CACHE: {$json} };\n";
		die();
	}
	
}
