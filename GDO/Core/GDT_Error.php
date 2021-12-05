<?php
namespace GDO\Core;

/**
 * An error is a panel that additionally logs the given message.
 * The message is logged into messages facility, as this is a client caused error.
 * Server crashes still goto the error or critical facility.
 * 
 * @TODO: If there are multiple errors and messages render json as array. 
 * 
 * @see GDT_Success
 * 
 * @author gizmore
 * @version 6.11.1
 * @since 6.0.0
 */
class GDT_Error extends GDT_Success
{
    public static $ERROR = 0;
    
    public function isSerializable() { return true; }
    
	public function hasError() { return true; }

    public function defaultName()
    {
        $back = 'error';
        $n = ++self::$ERROR;
        $back = $n === 1 ? $back : "{$back}_$n";
        return $back;
    }
    
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
