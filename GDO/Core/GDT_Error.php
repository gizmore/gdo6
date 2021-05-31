<?php
namespace GDO\Core;

/**
 * An error is a panel that additionally logs the given message.
 * The message is logged into messages facility, as this is a client caused error.
 * Server crashes still goto the error or critical facility.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.0.0
 */
class GDT_Error extends GDT_Success
{
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
	
	##############
	### Render ###
	##############
	public function renderCell()
	{
	    return GDT_Template::php('Core', 'cell/error.php', ['field' => $this]);
	}

}
