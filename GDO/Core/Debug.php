<?php
namespace GDO\Core;

use GDO\UI\GDT_Page;
use GDO\Util\Common;
use GDO\Mail\Mail;
use GDO\User\GDO_User;

/**
 * Debug backtrace and error handler.
 * 
 * Can send email on PHP errors.
 * 
 * Also has a method to get debug timings.
 * 
 * @example Debug::enableErrorHandler(); fatal_ooops();
 * 
 * @TODO: It displays and sends two errors for each error... why?!
 * 
 * @author gizmore
 * @version 6.10.2
 * @since 3.0.1
 */
final class Debug
{
	private static $die = false; # @TODO uppercase static members
	private static $enabled = false;
	private static $exception = false;
	private static $MAIL_ON_ERROR = false;
	public static $MAX_ARG_LEN = 28;
	
	// Call this to auto inc.
	public static function init()
	{
	}
	
	###############
	## Settings ###
	###############
	public static function setDieOnError($bool = true)
	{
		self::$die = $bool;
	}
	public static function setMailOnError($bool = true)
	{
		self::$MAIL_ON_ERROR = $bool;
	}
	public static function disableErrorHandler()
	{
		if (self::$enabled)
		{
			restore_error_handler();
			self::$enabled = false;
		}
	}
	public static function enableErrorHandler()
	{
		if (!self::$enabled)
		{
			set_error_handler(array(
				'GDO\\Core\\Debug',
				'error_handler'));
			register_shutdown_function(array(
				'GDO\\Core\\Debug',
				'shutdown_function'));
			self::$enabled = true;
		}
	}
	public static function enableStubErrorHandler()
	{
		self::disableErrorHandler();
		set_error_handler(array(
			'GDO\\Core\\Debug',
			'error_handler_stub'));
		self::$enabled = true;
	}
	
	#####################
	## Error Handlers ###
	#####################
	public static function error_handler_stub($errno, $errstr, $errfile, $errline, $errcontext)
	{
		return false;
	}
	
	/**
	 * This one get's called on a fatal.
	 * No stacktrace available and some vars are messed up.
	 */
	public static function shutdown_function()
	{
		if ($error = error_get_last())
		{
			if ($error && ($error['type'] === 1))
			{
				self::error_handler(1, $error['message'], self::shortpath($error['file']), $error['line'], NULL);
			}
		}
	}
	
	public static function error(\Error $e)
	{
	    self::error_handler($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
	}
	
	/**
	 * Error handler creates some html backtrace and can die on _every_ warning etc.
	 * 
	 * @param int $errno			
	 * @param string $errstr			
	 * @param string $errfile			
	 * @param int $errline			
	 * @param mixed $errcontext
	 * @return false
	 */
	public static function error_handler($errno, $errstr, $errfile, $errline, $errcontext=null)
	{
	    if (!(error_reporting() & $errno))
		{
			return;
		}
		
		// Log as critical!
		if (class_exists('GDO\Core\Logger', false))
		{
			Logger::logCritical(sprintf('%s in %s line %s', $errstr, $errfile, $errline));
			Logger::flush();
		}
		
		switch ($errno)
		{
			case - 1:
				$errnostr = 'GWF Error';
				break;
			
			case E_ERROR:
			case E_CORE_ERROR:
				$errnostr = 'PHP Fatal Error';
				break;
			case E_WARNING:
			case E_USER_WARNING:
			case E_CORE_WARNING:
				$errnostr = 'PHP Warning';
				break;
			case E_USER_NOTICE:
			case E_NOTICE:
				$errnostr = 'PHP Notice';
				break;
			case E_USER_ERROR:
				$errnostr = 'PHP Error';
				break;
			case E_STRICT:
				$errnostr = 'PHP Strict Error';
				break;
			// if(PHP5.3) case E_DEPRECATED: case E_USER_DEPRECATED: $errnostr = 'PHP Deprecated'; break;
			// if(PHP5.2) case E_RECOVERABLE_ERROR: $errnostr = 'PHP Recoverable Error'; break;
			case E_COMPILE_WARNING:
			case E_COMPILE_ERROR:
				$errnostr = 'PHP Compiling Error';
				break;
			case E_PARSE:
				$errnostr = 'PHP Parsing Error';
				break;
			
			default:
				$errnostr = 'PHP Unknown Error';
				break;
		}
		
		$app = Application::instance();
		$is_html = (!$app->isCLI()) && (!$app->isUnitTests()) && ($app->isHTML());
		
		if ($is_html)
		{
			$message = sprintf('<p>%s(EH %s):&nbsp;%s&nbsp;in&nbsp;<b style=\"font-size:16px;\">%s</b>&nbsp;line&nbsp;<b style=\"font-size:16px;\">%s</b></p>', $errnostr, $errno, $errstr, $errfile, $errline);
		}
		else
		{
			$message = sprintf('%s(EH %s) %s in %s line %s.', $errnostr, $errno, $errstr, $errfile, $errline);
		}
		
		// Send error to admin
		if (self::$MAIL_ON_ERROR)
		{
			self::sendDebugMail(self::backtrace($message, false));
		}
		
		// Output error
		if ($app->isCLI())
		{
			file_put_contents('php://stderr', self::backtrace($message, false) . PHP_EOL);
		}
		else
		{
			$message = GDO_ERROR_STACKTRACE ? self::backtrace($message, $is_html) : $message;
			echo self::renderError($message);
		}
		
		if (self::$die)
		{
			die(500);
		}
		
		return true;
	}
	public static function exception_handler($e)
	{
	    return self::debugException($e);
	}
	
	public static function debugException(\Throwable $e, $render=true)
	{
	    $app = Application::instance();
	    $is_html = $app ? (!$app->isUnitTests()) && $app->isHTML() : true;
	    $firstLine = sprintf("%s in %s Line %s", $e->getMessage(), $e->getFile(), $e->getLine());
	    
	    $mail = self::$MAIL_ON_ERROR;
	    $log = true;
	    
	    // Send error to admin?
	    if ($mail)
	    {
	        self::sendDebugMail($firstLine . "\n" . $e->getTraceAsString());
	    }
	    
	    // Log it?
	    if ($log)
	    {
	        Logger::logCritical($firstLine);
	        Logger::flush();
	    }
	    
	    // $content = ''; while (ob_get_level() > 0) { $content .= ob_get_contents(); ob_end_clean(); }
	    $message = self::backtraceException($e, $is_html, ' (XH)');
	    
	    if ($render)
	    {
	        echo self::renderError($message);
	    }
	    
	    return true;
	}
	
	private static function renderError($message)
	{
		http_response_code(405);
		$app = Application::instance();
		if (!$app)
		{
		    return html($message);
		}
		if ($app->isJSON())
		{
			return json_encode(array('error' => $message));
		}
		if ($app->isCLI() || $app->isUnitTests())
		{
		    return "$message\n";
		}
		else
		{
// 		    $message = html($message);
		}
		if ($app->isAjax() || (!defined('GDO_CORE_STABLE')))
		{
		    return $message;
		}
		else
		{
		    return GDT_Page::$INSTANCE->html($message)->render();
		}
	}
	public static function disableExceptionHandler()
	{
		if (self::$exception === true)
		{
			restore_exception_handler();
			self::$exception = false;
		}
	}
	public static function enableExceptionHandler()
	{
		if (!self::$exception)
		{
			self::$exception = true;
			set_exception_handler(array('GDO\\Core\\Debug', 'exception_handler'));
		}
	}
	
	/**
	 * Send error report mail.
	 * 
	 * @param string $message			
	 */
	public static function sendDebugMail($message)
	{
		return Mail::sendDebugMail(': PHP Error', $message);
	}
	
	/**
	 * Get some additional information
	 * 
	 * @todo move?
	 */
	public static function getDebugText($message)
	{
		$user = "~~GHOST~~";
		if (class_exists('GDO_User', false))
		{
    		try { $user = GDO_User::current()->displayNameLabel(); } catch (\Throwable $e) { }
		}
		
		if ($url = @$_SERVER['REQUEST_URI'])
		{
		    $url = GDO_PROTOCOL . '://' . GDO_DOMAIN . GDO_WEB_ROOT . $url;
		}
		
		$args = [
			isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'NULL',
			isset($_SERVER['REQUEST_URI']) ? $url : self::getMoMe(),
			isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'NULL',
			isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'NULL',
			isset($_SERVER['USER_AGENT']) ? $_SERVER['USER_AGENT'] : 'NULL',
			$user,
			$message,
			print_r($_GET, true),
		    print_r($_POST, true),
		    print_r($_REQUEST, true),
		    print_r($_COOKIE, true),
		];
		$args = array_map('html', $args);
		$pattern = "RequestMethod: %s\nRequestURI: %s\nReferer: %s\nIP: %s\nUserAgent: %s\nGDO_User: %s\n\nMessage: %s\n\n_GET: %s\n\n_POST: %s\n\nREQUEST: %s\n\n_COOKIE: %s\n\n";
		return vsprintf($pattern, $args);
	}
	
	private static function getMoMe()
	{
		return
		  Common::getGetString('mo', 'NoModule') .
		  '/' .
		  Common::getGetString('me', 'NoMethod');
	}
	
	/**
	 * Return a backtrace in either HTML or plaintext.
	 * You should use monospace font for html.
	 * HTML means (x)html(5) and <pre> style.
	 * Plaintext means nice for logfiles.
	 * 
	 * @param string $message			
	 * @param boolean $html			
	 * @return string
	 */
	public static function backtrace($message = '', $html = true)
	{
		return self::backtraceMessage($message, $html, debug_backtrace());
	}
	
	public static function backtraceException(\Throwable $e, $html = true, $message = '')
	{
		$message = sprintf("PHP Exception: %s in %s line %s", $e->getMessage(), self::shortpath($e->getFile()), $e->getLine());
		return self::backtraceMessage($message, $html, $e->getTrace(), $e->getLine(), $e->getFile());
	}
	
	private static function backtraceArgs(array $args = null)
	{
		$out = [];
		if ($args)
		{
			foreach ($args as $arg)
			{
				$out[] = self::backtraceArg($arg);
			}
		}
		return implode(", ", $out);
	}
	
	private static function backtraceArg($arg)
	{
		if ($arg === null)
		{
			return 'NULL';
		}
		elseif ($arg === true)
		{
			return 'true';
		}
		elseif ($arg === false)
		{
			return 'false';
		}
		elseif (is_string($arg) || is_array($arg))
		{
			$arg = json_encode($arg);
		}
		elseif (is_object($arg))
		{
			return get_class($arg);
		}
		else
		{
			$arg = json_encode($arg);
		}
		
		$app = Application::instance();
		$is_html = $app ? $app->isHTML() && (!$app->isUnitTests()) : true;
		
		$arg = $is_html ? html($arg) : $arg;
		$arg = str_replace('&quot;', '"', $arg); # It is safe to inject quotes. Turn back to get length calc right.
		$arg = str_replace('\\\\', '\\', $arg); # Double backslash was escaped always via json encode?
		
		if (mb_strlen($arg) > self::$MAX_ARG_LEN)
		{
		    return mb_substr($arg, 0, self::$MAX_ARG_LEN) . '…' . mb_substr($arg, -14);
		}
		
		return $arg;
	}
	private static function backtraceMessage($message, $html, array $stack, $lastLine='?', $lastFile='[unknown file]')
	{
		// Fix full path disclosure
		$message = self::shortpath($message);
		
		if (!GDO_ERROR_STACKTRACE)
		{
			return $html ? sprintf('<pre class="gdo-exception">%s</pre>', $message) . PHP_EOL : $message;
		}
		
		// Append PRE header.
		$back = $html ? "<pre class=\"gdo-exception\">\n" : '';
		
		// Append general title message.
		if ($message !== '')
		{
			$back .= $html ? '<em>' . $message . '</em>' : $message;
		}
		
		$implode = [];
		$preline = $lastLine;
		$prefile = $lastFile;
		$longest = 0;
		
		foreach ($stack as $row)
		{
		    # Skip debugger trace
		    if (@$row['class'] !== 'GDO\\Core\\Debug')
		    {
		        # Build current call
				$function = sprintf('%s%s(%s)',
				    isset($row['class']) ? $row['class'] . $row['type'] : '',
				    $row['function'],
				    self::backtraceArgs(isset($row['args']) ? $row['args'] : null));
				
				# Collect relevant stack frame
				$implode[] = array(
					$function,
					$prefile,
					$preline);
				
				# Calculations for align
				$len = mb_strlen($function);
				$longest = max([$len, $longest]);
			}

			# Use line in next frame.
			$preline = isset($row['line']) ? $row['line'] : '?';
			$prefile = isset($row['file']) ? $row['file'] : '[unknown file]';
		}
		
		$copy = [];
		foreach ($implode as $imp)
		{
			list ($func, $file, $line) = $imp;
			$len = mb_strlen($func);
			$func .= ' ' . str_repeat('.', $longest - $len);
			$copy[] = sprintf(' - %s %s line %s.', $func, self::shortpath($file), $line);
		}
		
		$back .= $html ? '<div class="gdt-hr"></div>' : "\n";
		$back .= sprintf('Backtrace starts in %s line %s.', self::shortpath($prefile), $preline) . "\n";
		$back .= implode("\n", array_reverse($copy));
		$back .= $html ? "</pre>\n" : '';
		return $back;
	}
	
	/**
	 * Strip full pathes so we don't have a full path disclosure.
	 * 
	 * @param string $path			
	 * @return string
	 */
	public static function shortpath($path)
	{
		$path = str_replace('\\', '/', $path);
		$path = str_replace(GDO_PATH, '', $path);
		return trim($path, ' /');
	}
	
}
