<?php
namespace GDO\Core;

use GDO\Util\Common;
use GDO\Mail\Mail;
use GDO\User\GDO_User;
use GDO\Util\Strings;

/**
 * Debug backtrace and error handler.
 * Can send email on PHP fatal errors.
 * Has a method to get debug timings.
 * 
 * @example Debug::enableErrorHandler(); fatal_ooops();
 * 
 * @TODO: check, on an out of memory fatal error, if a shutdown function would draw a stack trace.
 * 
 * @author gizmore
 * @version 6.11.3
 * @since 3.0.1
 */
final class Debug
{
	private static $DIE = false;
	private static $ENABLED = false;
	private static $EXCEPTION = false;
	private static $MAIL_ON_ERROR = false;
	public static $MAX_ARG_LEN = 23;
	
	/**
	 * Call this to auto include.
	 */
	public static function init() {}
	
	###############
	## Settings ###
	###############
	public static function setDieOnError($bool = true)
	{
		self::$DIE = $bool;
	}
	
	public static function setMailOnError($bool = true)
	{
		self::$MAIL_ON_ERROR = $bool;
	}
	
	public static function disableErrorHandler()
	{
		if (self::$ENABLED)
		{
			restore_error_handler();
			self::$ENABLED = false;
		}
	}
	
	public static function enableErrorHandler()
	{
		if (!self::$ENABLED)
		{
			set_error_handler([self::class, 'error_handler']);
			self::$ENABLED = true;
		}
	}
	
	#####################
	## Error Handlers ###
	#####################
// 	/**
// 	 * @TODO: shutdown function shall show debug stacktrace on fatal error. If an error was already shown, print nothing.
// 	 * No stacktrace available and some vars are messed up.
// 	 */
// 	public static function shutdown_function()
// 	{
// 		if ($error = error_get_last())
// 		{
// 			if ($error && ($error['type'] === 1))
// 			{
// 				self::error_handler(1, $error['message'], self::shortpath($error['file']), $error['line'], NULL);
// 			}
// 		}
// 	}
	
	public static function error(\Error $ex)
	{
	    self::error_handler($ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
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
			case -1:
				$errnostr = 'GDO Error';
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
		
		hdrc('HTTP/1.1 500 Server Error');

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
		
		return self::$DIE ? die(1) : true;
	}
	
	public static function exception_handler($ex)
	{
	    echo self::debugException($ex);
	}
	
	public static function debugException(\Throwable $ex, $render=true)
	{
	    $app = Application::instance();
	    $is_html = $app ? (!$app->isUnitTests()) && $app->isHTML() : true;
	    $firstLine = sprintf("%s in %s Line %s",
	    	$ex->getMessage(), $ex->getFile(), $ex->getLine());
	    
	    $log = true;
	    $mail = self::$MAIL_ON_ERROR;
	    $message = self::backtraceException($ex, $is_html, ' (XH)');
	    
	    // Send error to admin?
	    if ($mail)
	    {
	        self::sendDebugMail($firstLine . "\n\n" . $message);
	    }
	    
	    // Log it?
	    if ($log)
	    {
	        Logger::logCritical($firstLine);
	        Logger::flush();
	    }
	    
	    hdrc('HTTP/1.1 500 Server Error');
	    
	    if ($render)
	    {
	        return self::renderError($message);
	    }
	}
	
	private static function renderError($message)
	{
		$app = Application::instance();
		if (!$app)
		{
		    return html($message);
		}
		if ($app->isJSON())
		{
			return json_encode(['error' => $message]);
		}
		if ($app->isCLI() || $app->isUnitTests())
		{
		    return "$message\n";
		}
	    return $message;
	}
	
	public static function disableExceptionHandler()
	{
		if (self::$EXCEPTION === true)
		{
			restore_exception_handler();
			self::$EXCEPTION = false;
		}
	}
	
	public static function enableExceptionHandler()
	{
		if (!self::$EXCEPTION)
		{
			self::$EXCEPTION = true;
			$handler = ['GDO\\Core\\Debug', 'exception_handler'];
			set_exception_handler($handler);
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
	 * @TODO move?
	 */
	public static function getDebugText($message)
	{
		$user = "~~GHOST~~";
		if (class_exists('GDO\\User\\GDO_User', false))
		{
    		try { $user = GDO_User::current()->displayNameLabel(); } catch (\Throwable $ex) { }
		}
		
		if ($url = trim(@$_SERVER['REQUEST_URI'], '/'))
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
// 		    print_r($_ENV, true), # security vuln here?
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
		  Common::getRequestString('mo', '-none-') .
		  '/' .
		  Common::getRequestString('me', '-none-');
	}
	
	/**
	 * Return a backtrace in either HTML or plaintext.
	 * You should use monospace font for html style rendering / pre tags.
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
	
	public static function backtraceException(\Throwable $ex, $html = true, $message = '')
	{
		$message = sprintf("PHP %s: '%s' in %s line %s",
			get_class($ex), $ex->getMessage(),
			self::shortpath($ex->getFile()), $ex->getLine());
		return self::backtraceMessage($message, $html, $ex->getTrace(), $ex->getLine(), $ex->getFile());
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
		elseif (is_object($arg))
		{
			$class = get_class($arg);
			$back = Strings::rsubstrFrom($class, '\\', $class);
			if ($arg instanceof GDO)
			{
				$back .= '#' . $arg->getID();
			}
			return $back;
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
		$arg = str_replace('\\/', '/', $arg); # Double backslash was escaped always via json encode?
		if (mb_strlen($arg) > self::$MAX_ARG_LEN)
		{
			if ($app->isCLI())
			{
				self::$MAX_ARG_LEN = 40;
			}
			# @TODO: Debug parameter value output shows buggy parameter value for strings that are close to the limit. like {"foo":"bar", "bar":"fo..., "bar:foo"}. Only some basic math is needed.
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
		    if (@$row['class'] !== self::class)
		    {
		        # Build current call
				$function = sprintf('%s%s(%s)',
				    isset($row['class']) ? $row['class'] . $row['type'] : '',
				    $row['function'],
				    self::backtraceArgs(isset($row['args']) ? $row['args'] : null));
				
				# Collect relevant stack frame
				$implode[] = [
					$function,
					$prefile,
					$preline,
				];
				
				# Calculations for align
				$len = mb_strlen($function);
				$longest = max([$len, $longest]);
			}

			# Use line in next frame.
			$preline = isset($row['line']) ? $row['line'] : '?';
			$prefile = isset($row['file']) ? $row['file'] : '[unknown file]';
		}
		
		$copy = [];
		$cli = Application::instance()->isCLI();
		$ajax = Application::instance()->isAjax();
		foreach ($implode as $imp)
		{
			list ($func, $file, $line) = $imp;
			if ( (!$cli) && (!$ajax) )
			{
				$len = mb_strlen($func);
				$func .= ' ' . str_repeat('.', $longest - $len);
			}
			$copy[] = sprintf(' - %s %s line %s.', $func, self::shortpath($file, "\n"), $line);
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
	public static function shortpath($path, $newline="")
	{
		$path = str_replace('\\', '/', $path);
		$path = str_replace(GDO_PATH, '', $path);
		$path = trim($path, ' /');
		return $path;
	}
	
}
