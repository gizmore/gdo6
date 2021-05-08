<?php
namespace GDO\Core;

use GDO\Util\Common;

/**
 * The GDO Logger.
 * 
 * @author gizmore
 * @author spaceone
 * @version 6.10.3
 * @since 1.0.0
 */
final class Logger
{
	const GDO_WARNING = 0x01;
	const GDO_MESSAGE = 0x02;
	const GDO_ERROR = 0x04;
	const GDO_CRITICAL = 0x08;
	const PHP_ERROR = 0x10;
	const DB_ERROR = 0x20;
	const HTTP_ERROR = 0x80;
	const HTTP_GET = 0x100;
	const HTTP_POST = 0x200;
	const IP = 0x400;
	const BUFFERED = 0x1000;
	const DEBUG = 0x2000;
	
	const _NONE = 0x00;
	const _ALL = 0x37ff;
	const _DEFAULT = self::_ALL;

	public static $POST_DELIMITER = '.::.';

	private static $username = false;
	private static $basedir = GDO_PATH . 'protected/logs';
	private static $logbits = self::_DEFAULT;
	private static $logformat = "%s [%s%s] - %s\n";
	private static $cache = 0;
	private static $logs = [];
	public static $WRITES = 0;

	/**
	 * Init the logger. If a username is given, the logger will log into a logs/username dir.
	 * @param string $username The username for memberlogs
	 * @param int $logbits bitmask for logging-modes
	 * @param string $basedir The path to the logfiles. Should be relative.
	 */
	public static function init($username=null, $logbits=self::_DEFAULT, $basedir='protected/logs')
	{
		self::$username = $username;
		self::$logbits = $logbits;
		self::$basedir = GDO_PATH . $basedir;
	}

	public static function isEnabled($bits) { return ($bits === (self::$logbits & $bits)); }
	public static function isDisabled($bits) { return ($bits !== (self::$logbits & $bits)); }

	public static function cache($new) { self::$cache = self::$logbits; self::$logbits = $new; }
	public static function restore() { self::$logbits = self::$cache; }

	public static function enable($bits) { self::$logbits |= $bits; }
	public static function disable($bits) { self::$logbits &= (~$bits); }

	public static function setLogFormat($format) { self::$logformat = $format; }
	
	public static function enableBuffer() { self::enable(self::BUFFERED); }
	public static function disableBuffer() { self::flush();	self::disable(self::BUFFERED); }
	public static function isBuffered() { return self::isEnabled(self::BUFFERED); }

	/**
	 * Get the whole request to log it. Censor passwords.
	 * @return string
	 */
	private static function getRequest()
	{
		$post = self::isDisabled(self::HTTP_POST);
		if (true === self::isDisabled(self::HTTP_GET) && true === $post)
		{
			return '';
		}

		$back = Common::getServer('REQUEST_METHOD', '').' ';
		$back .= Common::getServer('REQUEST_URI', '');

		if (false === $post && count($_REQUEST) > 0)
		{
			$back .= self::$POST_DELIMITER .'POSTDATA'.self::stripPassword($_REQUEST);
		}
		return $back;
	}

	/**
	 * shorten a string and remove dangerous pattern
	 *
	 */
	public static function &shortString(&$str, $length=256)
	{
		$str = substr($str, 0, $length);
		while (false !== strpos($str, '<?'))
		{
			$str = str_replace('<?', '##', $str);
		}
		return $str;
	}

	/**
	 * strip values from arraykeys which begin with 'pass'
	 * @todo faster way without foreach...
	 * print_r and preg_match ?
	 * array_map stripos('pass') return '';
	 */
	private static function stripPassword(array $a)
	{
		$back = '';
		foreach ($a as $k => $v)
		{
		    if (stripos($k, 'pass') !== false)
			{
				$v = 'xxxxx';
			}
			elseif (is_array($v) === true)
			{
				$v = 'Array(' . count($v) . ')';
// 				$v = Arrays::implode(',', $v); # can fail horribly here
			}
			$back .= self::$POST_DELIMITER.$k.'=>'.$v;
		}
		return self::shortString($back);
	}

	/**
	 * Log the request.
	 */
	public static function logRequest() { self::log('request', self::getRequest()); }

	########################
	### Default logfiles ###
	########################
	public static function logCron($message) { self::rawLog('cron', $message, 0); echo $message.PHP_EOL; }
	public static function logWebsocket($message) { self::rawLog('websocket', $message, 0); echo $message.PHP_EOL; }
	public static function logDebug($message) { self::rawLog('debug', $message, self::DEBUG); }
	public static function logError($message) { self::log('error', $message, self::GDO_ERROR); }
	public static function logMessage($message) { self::log('message', $message, self::GDO_MESSAGE); }
	public static function logWarning($message) { self::log('warning', $message, self::GDO_WARNING); }
	public static function logCritical($message)
	{
		self::log('critical', $message, self::GDO_CRITICAL);
// 		self::log('critical_details', Debug::backtrace(print_r($_GET, true).PHP_EOL.self::stripPassword($_REQUEST).PHP_EOL.$message, false), self::GDO_CRITICAL); // TODO: formating
	}
	public static function logException(\Throwable $e)
	{
		$message = sprintf("%s in %s Line %s\n", $e->getMessage(), Debug::shortpath($e->getFile()), $e->getLine());
		self::log('critical', $message, self::GDO_CRITICAL);
		$log = Debug::backtraceException($e, true).PHP_EOL.self::stripPassword($_REQUEST).PHP_EOL.$message;
		self::log('critical_details', $log, self::GDO_CRITICAL);
	}
	public static function logInstall($message) { self::log('install', $message, self::_NONE); }
	public static function logHTTP($message) { self::rawLog('http', $message, self::HTTP_ERROR); }

	/**
	 * Get the full log path, either for username log or site log.
	 * @param string $filename
	 * @param string|false $username
	 */
	private static function getFullPath($filename, $username=false)
	{
	    $dt = new \DateTime('now', self::tz());
	    $date = $dt->format('Ymd');
		return is_string($username)
			? sprintf('%s/memberlog/%s/%s_%s.txt', self::$basedir, $username, $date, $filename)
			: sprintf('%s/%s_%s.txt', self::$basedir, $date, $filename);
	}

	/**
	 * Recursively create logdir with GDO_CHMOD permissions.
	 * @param string $filename
	 * @return boolean
	 */
	private static function createLogDir($filename)
	{
		$dir = dirname($filename);
		return is_dir($dir) ? true : @mkdir($dir, GDO_CHMOD, true);
	}

	/**
	 * Flush all logfiles
	 * throws an GDOError within logfile content when fails
	 */
	public static function flush()
	{
		foreach (self::$logs as $file => $msg)
		{
			if ($e = self::writeLog($file, $msg))
			{
				unset(self::$logs[$file]);
			}
			else
			{
				throw $e;
			}
		}
	}

	/**
	 * Log a message.
	 * The core logging function.
	 * Raw mode will not write any datestamps or IP/username.
	 * @param string $filename short logname
	 * @param string $message the message
	 * format: $time, $ip, $username, $message
	 */
	public static function log($filename, $message, $logmode=0)
	{
		# log it?
		if (self::isEnabled($logmode))
		{
		    $dt = new \DateTime("now", self::tz());
		    $time = $dt->format('H:i');
			$ip = self::isDisabled(self::IP) ? '' : @$_SERVER['REMOTE_ADDR'];
			$username = self::$username === false ? ':~guest~' : ':'.self::$username;

			self::logB($filename, sprintf(self::$logformat, $time, $ip, $username, $message));
		}
	}
	
	private static function tz()
	{
	    static $tz;
	    if ($tz === null)
	    {
	        $tz = new \DateTimeZone(def('GDO_ERROR_TIMEZONE', 'UTC'));
	    }
	    return $tz;
	}

	public static function rawLog($filename, $message, $logmode=0)
	{
		# log it?
		if (self::isEnabled($logmode))
		{
			self::logB($filename, $message.PHP_EOL);
		}
	}

	private static function logB($filename, $message)
	{
		self::$WRITES++;
		if (!self::isBuffered())
		{
			self::writeLog($filename, $message);
		}
		elseif (true === isset(self::$logs[$filename]))
		{
			self::$logs[$filename] .= $message;
		}
		else
		{
			self::$logs[$filename] = $message;
		}
	}

	private static function writeLog($filename, $message)
	{
		# Create logdir if not exists
		$filename = self::getFullPath($filename, self::$username);
		if (!self::createLogDir($filename))
		{
			return new GDOException(sprintf('Cannot create logdir "%s" in %s line %s.', dirname($filename), __METHOD__, __LINE__));
		}

		# Default kill banner.
		if (!is_file($filename))
		{
			$bool = true;
			$bool = $bool && (false !== file_put_contents($filename, '<?php die(2); ?>'.PHP_EOL));
			$bool = $bool && @chmod($filename, GDO_CHMOD&0666);
			if (false === $bool)
			{
				return new GDOException(sprintf('Cannot create logfile "%s" in %s line %s.', $filename, __METHOD__, __LINE__));
			}
		}

		# Write to file
		if (!file_put_contents($filename, $message, FILE_APPEND))
		{
			return new GDOException(sprintf('Cannot write logs: logfile "%s" in %s line %s.', $filename, __METHOD__, __LINE__));
		}

		return true;
	}
	
	public static function debug(...$objects)
	{
		foreach ($objects as $object)
		{
			$message = $object;
			if ( (is_array($object)) || (is_object($object)) )
			{
				$message = print_r($object, true);
			}
			self::logDebug($message);
		}
	}
	
}
