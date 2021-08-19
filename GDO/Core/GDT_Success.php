<?php
namespace GDO\Core;

use GDO\UI\WithPHPJQuery;
use GDO\UI\WithText;
use GDO\UI\WithTitle;

/**
 * A success message, the pedant to GDT_Error.
 * Logs a message if desired.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.0.0
 * @see GDT_Error
 */
class GDT_Success extends GDT
{
    use WithTitle;
    use WithText;
    use WithPHPJQuery;

    public static $MESSAGE = 0;

    public function isSerializable() { return true; }

    public function defaultName()
    {
        $back = 'message';
        $n = ++self::$MESSAGE;
        $back = $n === 1 ? $back : "{$back}_$n";
        return $back;
    }

    public function code($code)
	{
	    if ($code > 200)
	    {
	        GDT_Response::$CODE = $code;
	        http_response_code($code);
	    }
	    return $this;
	}

	public static function with($key, array $args=null, $code=200, $log=true)
	{
	    if ($log)
		{
			Logger::logMessage(tiso('en', $key, $args));
		}
		return self::make()->text($key, $args)->code($code);
	}

	public static function withText($text, $code=200, $log=true)
	{
	    if ($log)
	    {
	        Logger::logMessage($text);
	    }
	    return self::make()->textRaw($text)->code($code);
	}

	public static function responseWith($key, array $args=null, $code=200, $log=true)
	{
	    return GDT_Response::makeWith(self::with($key, $args, $code, $log))->code($code);
	}

	##############
	### Render ###
	##############
	public function renderCell() { return GDT_Template::php('Core', 'cell/success.php', ['field' => $this]); }
	public function renderJSON()
	{
	    if ($this->hasTitle())
	    {
	        return sprintf('%s - %s', $this->renderTitle(), $this->renderText());
	    }
	    return $this->renderText();
	}

	public function renderCLI()
	{
	    return $this->renderJSON();
	}

}
