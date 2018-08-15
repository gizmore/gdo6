<?php
namespace GDO\Core;
use GDO\UI\GDT_Panel;
/**
 * An error is a panel that additionally logs the given message.
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
class GDT_Error extends GDT_Panel
{
	public function isError() { return true; }
	
	public function hasError() { return true; }
	
	public static function responseException(\Exception $e)
	{
		Logger::logException($e);
		$html = Debug::backtraceException($e, Application::instance()->isHTML(), $e->getMessage());
		return self::responseWith('err_exception', [$html], 500, false);
	}
	
	public static function responseWith($key, array $args=null, $code=405, $log=true)
	{
		return GDT_Response::makeWith(self::with($key, $args, $code, $log))->code($code);
	}
	
	public static function with($key, array $args=null, $code=405, $log=true)
	{
		http_response_code($code);
		if ($log)
		{
			Logger::logError(tiso('en', $key, $args));
		}
		return self::withHTML(t($key, $args));
	}
	
	public static function withHTML($html)
	{
		return self::make()->html($html)->icon('report_problem');
	}
	
	##############
	### Render ###
	##############
	public function renderCell() { return GDT_Template::php('Core', 'cell/error.php', ['field' => $this]); }
	
	public function renderJSON()
	{
		return array(
			'error' => $this->html,
		);
	}
}
