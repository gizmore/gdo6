<?php
namespace GDO\Date;

use GDO\Language\Trans;
use DateTime;
use GDO\Core\Application;
use GDO\Core\GDOError;

/**
 * Time helper class.
 * Using mysql dates with milliseconds.
 * 
 * For GDT_Timestamp, the value is the microtime(true) timestamp.
 * For GDT_Date and GDT_DateTime, the value is a \DateTime object.
 * The $var is always a mysql date string in UTC.
 * 
 * There are 3 time datatypes the class operates on.
 *  - $time(stamp): A float, microtime(true)
 *  - $date: A string, date($format_via_trans)
 *  - $datetime: A PHP @\DateTime object.
 *
 * @author gizmore
 * @version 6.11.2
 * @since 1.0.0
 * 
 * @see GDT_Date
 * @see GDT_DateTime
 * @see GDT_Duration
 */
final class Time
{
    # durations in seconds
    const ONE_MILLISECOND = 0.001;
	const ONE_SECOND = 1;
	const ONE_MINUTE = 60;
	const ONE_HOUR = 3600;
	const ONE_DAY = 86400;
	const ONE_WEEK = 604800;
	const ONE_MONTH = 2592000;
	const ONE_YEAR = 31536000;
	
	# known display formats from lang file
	const FMT_MINUTE = 'minute';
	const FMT_SHORT = 'short';
	const FMT_LONG = 'long';
	const FMT_DAY = 'day';
	const FMT_MS = 'ms';
	
	################
	### Timezone ###
	################
	/**
	 * UTC DB ID
	 * @see GDO_Timezone
	 * @var string
	 */
	const UTC = '1';
	public static $UTC;
	public static $TIMEZONE = self::UTC; # default timezone
	public static $TIMEZONE_OBJECTS = [];
	public static function getTimezoneObject($timezone=null)
	{
	    $timezone = $timezone ? $timezone : self::$TIMEZONE;
	    if (!isset(self::$TIMEZONE_OBJECTS[$timezone]))
	    {
	    	$timezone = GDO_Timezone::findById($timezone);
	        $tz = new \DateTimeZone($timezone->getName());
	        self::$TIMEZONE_OBJECTS[$timezone->getID()] = $tz;
	        return $tz;
	    }
	    return self::$TIMEZONE_OBJECTS[$timezone];
	}
	
	public static function setTimezone($timezone)
	{
	    self::$TIMEZONE = $timezone;
	    Module_Date::instance()->timezone = $timezone;
	}
	
	###############
	### Convert ###
	###############
	
	/**
	 * Get a mysql date from a timestamp, like YYYY-mm-dd HH:ii:ss.vvv.
	 * @example $date = Time::getDate();
	 * @return string
	 */
	public static function getDate($time=0, $format='Y-m-d H:i:s.v')
	{
	    if ($dt = self::getDateTime($time))
	    {
	        $date = $dt->format($format);
	        return $date;
	    }
	}
	
	public static function getDateDay($time=0)
	{
	    return self::getDate($time, 'Y-m-d');
	}
	
	public static function getDateSec($time=0)
	{
	    return self::getDate($time, 'Y-m-d H:i:s');
	}
	
	/**
	 * Get a datetime object from a timestamp.
	 * @param number $time
	 * @return \DateTime
	 */
	public static function getDateTime($time=0)
	{
	    $time = $time <= 0 ? Application::$MICROTIME : (float)$time;
	    return DateTime::createFromFormat('U.u', sprintf('%.03f', $time), self::$UTC);
	}
	
	public static function getDateWithoutTime($time=null)
	{
		return substr(self::getDate($time), 0, 10);
	}
	
	/**
	 * Get the timestamp for a database date (UTC).
	 * @param string $date
	 * @return float microtime (ms)
	 */
	public static function getTimestamp($date=null)
	{
	    $ts = $date ? self::parseDate($date, self::UTC, 'db') : Application::$MICROTIME;
	    return $ts;
	}
	
	/**
	 * Convert DateTime input from a user.
	 * This is usually in the users language format and timezone
	 * @param string $date
	 * @param string $timezone
	 * @param string $format
	 * @return int Timestamp
	 */
	public static function parseDate($date, $timezone=null, $format='parse')
	{
	    $timestamp = self::parseDateIso(Trans::$ISO, $date, $timezone, $format);
	    return $timestamp;
	}
	
	public static function parseDateDB($date)
	{
		return self::parseDate($date, self::UTC, 'db');
	}
	
	/**
	 * Convert a user date input to a timestamp.
	 * @TODO parseDateIso is broken a bit, because strlen($date) might differ across languages.
	 * 
	 * @param string $iso
	 * @param string $date
	 * @param string $timezone
	 * @param string $format
	 * @return int Timestamp
	 */
	public static function parseDateIso($iso, $date, $timezone=null, $format='parse')
	{
	    if ($d = self::parseDateTimeISO($iso, $date, $timezone, $format))
	    {
            $timestamp = $d->format('U.v');
	        return (float)$timestamp;
	    }
	}
	
	public static function parseDateTime($date, $timezone=null, $format='parse')
	{
	    return self::parseDateTimeIso(Trans::$ISO, $date, $timezone, $format);
	}
	
	public static function parseDateTimeDB($date)
	{
	    return self::parseDateTimeIso('en', $date, self::UTC, 'db');
	}
	
	/**
	 * Parse a string into a datetime.
	 * @param string $iso
	 * @param string $date
	 * @param int $timezone
	 * @param string $format
	 * @throws GDOError
	 * @return \DateTime
	 */
	public static function parseDateTimeISO($iso, $date, $timezone=null, $format='parse')
	{
	    # Adjust
	    if (!$date)
	    {
	        return null;
	    }
	    
	    $date = preg_replace('/[ap]m/iD', '', $date);
// 	    $date = preg_replace('/ {2,}/D', ' ', $date);
	    $date = trim($date, "\r\n\t ");
	    
	    $len = strlen($date);
	    if ($len === 10)
	    {
	        $date .= ' 00:00:00.000';
	    }
	    elseif ($len === 16)
	    {
	        $date .= ':00.000';
	    }
	    elseif ($len === 19)
	    {
	        $date .= '.000';
	    }
	    
	    # Parse
	    if ($format === 'db')
	    {
	        $format = 'Y-m-d H:i:s.u';
	    }
	    else
	    {
	        $format = tiso($iso, 'df_' . $format);
	    }
	    $timezone = $timezone ? $timezone : self::$TIMEZONE;
	    $timezone = self::getTimezoneObject($timezone);
	    if (!($d = DateTime::createFromFormat($format, $date, $timezone)))
	    {
	        throw new GDOError('err_invalid_date', [html($date), $format]);
	    }
	    return $d;
	}
	
	###############
	### Display ###
	###############
	/**
	 * Display a timestamp.
	 * @param $timestamp
	 * @param $langid
	 * @param $default_return
	 * @return string
	 */
	public static function displayTimestamp($timestamp, $format='short', $default_return='---', $timezone=null)
	{
	    return self::displayTimestampISO(Trans::$ISO, $timestamp, $format, $default_return, $timezone);
	}
	
	public static function displayTimestampISO($iso, $timestamp, $format='short', $default_return='---', $timezone=null)
	{
	    if ($timestamp <= 0)
	    {
	        return $default_return;
	    }
	    $dt = DateTime::createFromFormat('U.u', sprintf('%.06f', $timestamp), self::$UTC);
	    return self::displayDateTimeISO($iso, $dt, $format, $default_return, $timezone);
	}
	
	/**
	 * Display a datetime string.
	 * @param string $date
	 * @param string $format
	 * @param string $default_return
	 * @param int $timezone
	 * @return string
	 */
	public static function displayDate($date=null, $format='short', $default_return='---', $timezone=null)
	{
	    return self::displayDateISO(Trans::$ISO, $date, $format, $default_return, $timezone);
	}
	
	/**
	 * Display a datestring.
	 * 
	 * @param string $iso
	 * @param string $date a date from the database in utc
	 * @param string $format display format
	 * @param string $default_return default return for null
	 * @param string $timezone
	 * @return string
	 */
	public static function displayDateISO($iso, $date=null, $format='short', $default_return='---', $timezone=null)
	{
	    if ($date === null)
	    {
	        return $default_return;
	    }
	    if (!($d = self::parseDateTimeDB($date)))
	    {
	        return $default_return; 
	    }
	    return self::displayDateTimeISO($iso, $d, $format, $default_return, $timezone);
	}
	
	/**
	 * 
	 * @param DateTime $datetime
	 * @param string $format
	 * @param string $default_return
	 * @param int $timezone
	 * @return string
	 */
	public static function displayDateTime(DateTime $datetime=null, $format='short', $default_return='---', $timezone=null)
	{
		return self::displayDateTimeISO(Trans::$ISO, $datetime, $format, $default_return, $timezone);
	}
	
	/**
	 * Actual display of a \DateTime.
	 * 
	 * @param string $iso
	 * @param DateTime $datetime
	 * @param string $format
	 * @return string
	 */
	public static function displayDateTimeISO($iso, DateTime $datetime=null, $format='short', $default_return='---', $timezone=null)
	{
		if (!$datetime)
		{
			return $default_return;
		}
	    $timezone = $timezone ? $timezone : self::$TIMEZONE;
        $datetime->setTimezone(self::getTimezoneObject($timezone));
	    $format = tiso($iso, "df_$format");
	    return $datetime->format($format);
	}
	
	###########
	### Age ###
	###########
	public static function getDiff($date)
	{
		$a = new DateTime($date);
		$b = new DateTime(self::getDate(Application::$MICROTIME));
		return abs($b->getTimestamp() - $a->getTimestamp());
	}
	
	/**
	 * Get the age of a date.
	 * @param string $date
	 * @return int
	 */
	public static function getAgo($date)
	{
	    return $date ?
	       Application::$MICROTIME - self::getTimestamp($date) : 
	       null;
	}
	
	/**
	 * Get the age in years of a date.
	 * @param string $date
	 * @return number
	 */
	public static function getAge($date)
	{
	    $seconds = self::getAgo($date);
	    return $seconds / self::ONE_YEAR;
	}
	
	public static function getAgeTS($duration)
	{
	    return $duration / self::ONE_YEAR;
	}
	
	public static function displayAge($date)
	{
		return self::displayAgeTS(self::getTimestamp($date));
	}
	
	public static function displayAgeTS($timestamp)
	{
	    $timestamp = Application::$TIME - (int)$timestamp;
	    return self::humanDuration($timestamp);
	}
	
	public static function displayAgeISO($date, $iso)
	{
		return self::displayAgeTSISO(self::getTimestamp($date), $iso);
	}
	
	public static function displayAgeTSISO($timestamp, $iso)
	{
	    return self::humanDurationISO($iso, Application::$TIME - $timestamp);
	}
	
	#################
	### From Week ###
	#################
	public static function weekTimestamp($year, $week)
	{
	    $week_start = new DateTime(null, Time::$UTC);
	    $week_start->setISODate(intval($year, 10), intval($week, 10));
	    $week_start = $week_start->format('U');
	    return $week_start;
	}
	
	################
	### Duration ###
	################
	/**
	 * Return a human readable duration.
	 * Example: 666 returns 11 minutes 6 seconds.
	 * @param $duration int in seconds.
	 * @param $nUnits int how many units to display max.
	 * @return string
	 */
	public static function humanDuration($duration, $nUnits=2)
	{
		return self::humanDurationISO(Trans::$ISO, $duration, $nUnits);
	}
	
	public static function humanDurationEN($duration, $nUnits=2)
	{
	    return self::humanDurationISO('en', $duration, $nUnits);
	}
	
	public static function humanDurationISO($iso, $duration, $nUnits=2)
	{
		static $cache = array();
		if (!isset($cache[$iso]))
		{
			$cache[$iso] = array(
				tiso($iso, 'tu_s') => 60,
				tiso($iso, 'tu_m') => 60,
				tiso($iso, 'tu_h') => 24,
				tiso($iso, 'tu_d') => 365,
				tiso($iso, 'tu_y') => 1000000,
			);
		}
		return self::humanDurationRaw($duration, $nUnits, $cache[$iso]);
	}
	
	public static function humanDurationRaw($duration, $nUnits, array $units)
	{
		$duration = (int)$duration;
		$calced = array();
		foreach ($units as $text => $mod)
		{
			if (0 < ($remainder = $duration % $mod))
			{
				$calced[] = $remainder.$text;
			}
			$duration = intval($duration / $mod);
			if ($duration === 0)
			{
				break;
			}
		}
		
		if (count($calced) === 0)
		{
			return '0'.key($units);
		}
		
		$calced = array_reverse($calced, true);
		$i = 0;
		foreach (array_keys($calced) as $key)
		{
			$i++;
			if ($i > $nUnits)
			{
				unset($calced[$key]);
			}
		}
		return implode(' ', $calced);
	}
	
	public static function isValidDuration($string, $min, $max)
	{
		$duration = self::humanToSeconds($string);
		return $duration >= $min && $duration <= $max;
	}
	
	########################
	### Human to seconds ###
	########################
	/**
	 * Convert a human duration to seconds.
	 * Input may be like 3d5h8m 7s.
	 * There is no months, only minutes and weeks etc.
	 * Also possible is 1 month 3 days or 1year2sec.
	 * No unit means default unit, which is seconds.
	 * Supported units are:
	 * ms, millis, millisecond,
	 * s, sec, second, seconds,
	 * m, min, minute, minutes,
	 * h, hour, hours,
	 * d, day, days,
	 * w, week, weeks,
	 * mo, month, months,
	 * y, year, years.
	 * 
	 * @param $duration string is the duration in human format.
	 * @return float duration in seconds
	 * */
	public static function humanToSeconds($duration)
	{
		if (is_int($duration)) { return $duration; }
		if (!is_string($duration)) { return 0.0; }
		if (is_numeric($duration)) { return floatval($duration); }
		$matches = null;
		$duration = strtolower($duration);
		if (!preg_match('/^\\s*(([0-9]+)\\s*([smhdwoy]{0,2}))+\\s*$/iD', $duration, $matches))
		{
			return 0.0;
		}
		$multis = [
			'ms' => 0.001,
			's' => 1,
			'm' => 60,
			'h' => 3600,
			'd' => 86400,
			'w' => 604800,
			'mo' => 2592000,
			'y' => 31536000,
		];
		$back = 0.0;
		$len = (count($matches) - 1) / 3;
		$j = 1;
		for ($i = 0; $i < $len; $i++, $j+=3)
		{
			$d = floatval($matches[$j+1]);
			if ($d)
			{
				if ($unit = @$multis[$matches[$j+2]])
				{
					$back += $d * $unit;
				}
				else
				{
				    $back += $d;
				}
			}
		}
		return $back;
	}
	
	#############
	### Parts ###
	#############
	public static function getYear($date) { return substr($date, 0 , 4); }
	public static function getMonth($date) { return substr($date, 5 , 2); }
	public static function getDay($date) { return substr($date, 8 , 2); }
	
}
	
date_default_timezone_set('UTC');
Time::$UTC = new \DateTimeZone('UTC');
Time::$TIMEZONE_OBJECTS[Time::UTC] = Time::$UTC;
