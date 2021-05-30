<?php
namespace GDO\Core;

use GDO\UI\WithPHPJQuery;
use GDO\UI\WithText;
use GDO\UI\WithTitle;

/**
 * An error is a panel that additionally logs the given message.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.0.0
 */
class GDT_Error extends GDT
{
    use WithTitle;
    use WithText;
    use WithPHPJQuery;
    
    public static $ERROR = 1;
    
    public function isSerializable() { return true; }
    
    public function defaultName() { return self::$ERROR === 1 ? 'error' : 'error_' . (++self::$ERROR); }
    
	public function hasError() { return true; }

	public static function responseException(\Throwable $e)
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
	    if ($code > 200)
	    {
	        GDT_Response::$CODE = $code;
	        http_response_code($code);
	    }
		
		if ($log)
		{
			Logger::logError(tiso('en', $key, $args));
		}
		
		return self::make()->text($key, $args);
	}
	
	##############
	### Render ###
	##############
	public function renderCell()
	{
	    return GDT_Template::php('Core', 'cell/error.php', ['field' => $this]);
	}
	
	public function renderJSON()
	{
	    return $this->renderText();
	}
	
	public function renderCLI()
	{
	    return $this->renderText();
	}

}
