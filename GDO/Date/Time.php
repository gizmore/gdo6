<?php
namespace GDO\Date;

use GDO\Language\Trans;
use DateTime;
use GDO\Core\Application;
use GDO\Core\GDOError;

/**
 * Time helper class.
 * Using mysql date with milliseconds.
 * For GDT, the value is the timestamp and the var is the date in mysql format.
 *
 * @author gizmore
 * @version 6.10.4
 * @since 1.0.0
 * 
 * @see GDT_Date
 * @see GDT_DateTime
 * @see GDT_Duration
 */
final class Time
{
    # durations in seconds
	const ONE_SECOND = 1;
	const ONE_MINUTE = 60;
	const ONE_HOUR = 3600;
	const ONE_DAY = 86400;
	const ONE_WEEK = 604800;
	const ONE_MONTH = 2592000;
	const ONE_YEAR = 31536000;
	
	# known display formats
	const FMT_MINUTE = 'minute';
	const FMT_SHORT = 'short';
	const FMT_LONG = 'long';
	const FMT_DAY = 'day';
	const FMT_MS = 'ms';
	
	################
	### Timezone ###
	################
	public static $UTC;
	public static $TIMEZONE = 'UTC'; # default timezone
	private static $TIMEZONE_OBJECTS = [];
	public static function getTimezoneObject($timezone=null)
	{
	    $timezone = $timezone ? $timezone : self::$TIMEZONE;
	    if (!isset(self::$TIMEZONE_OBJECTS[$timezone]))
	    {
	        $tz = new \DateTimeZone($timezone);
	        self::$TIMEZONE_OBJECTS[$timezone] = $tz;
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
// 	const PARSE_DAY = 'Y-m-d';
// 	const PARSE_SEC = 'Y-m-d H:i:s';
// 	const PARSE_MIL = 'Y-m-d H:i:s.v';
	
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
	 * @param int $time
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
	    $ts = $date ? self::parseDate($date, 'UTC', 'db') : Application::$MICROTIME;
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
	
	public static function parseDateDB($date, $timezone=null, $format='parse')
	{
	    return self::parseDate($date, 'UTC', 'db');
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
	    # Adjust
	    $len = strlen($date);
	    if ($len === 10)
	    {
	        $date .= ' 00:00:00.000';
	    }
	    elseif ($len === 16)
	    {
	        $date .= ':00.000';
	    }
	    elseif (strlen($date) === 19)
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
	    $timestamp = $d->format('U.v');
	    return (float)$timestamp;
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
	    $dt = DateTime::createFromFormat('U.v', sprintf('%.03f', $timestamp), self::$UTC);
	    return self::displayDateTimeISO($iso, $dt, $format, $default_return, $timezone);
	}
	
	public static function displayDate($date=null, $format='short', $default_return='---', $timezone=null)
	{
	    return self::displayDateISO(Trans::$ISO, $date, $format, $default_return, $timezone);
	}
	
	/**
	 * Display a database date.
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
	    $timestamp = self::getTimestamp($date);
	    return self::displayTimestampISO($iso, $timestamp, $format, $default_return, $timezone);
	}
	
	/**
	 * Actual display of a \DateTime.
	 * 
	 * @param string $iso
	 * @param DateTime $datetime
	 * @param string $format
	 * @return string
	 */
	private static function displayDateTimeISO($iso, DateTime $datetime, $format='short', $default_return='---', $timezone=null)
	{
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
		return Application::$MICROTIME - self::getTimestamp($date);
	}
	
	public static function displayAge($date)
	{
		return self::displayAgeTS(self::getTimestamp($date));
	}
	
	public static function displayAgeTS($timestamp)
	{
	    return self::humanDuration(Application::$TIME - $timestamp);
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
	    $week_start = new DateTime();
	    $week_start->setISODate(intval($year, 10), intval($week, 10));
	    $week_start = $week_start->format('U');
	    $week_start -= ($week_start%Time::ONE_WEEK);
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
	 * Input may be like 3d5h8i 7s.
	 * Also possible is 1 month 3 days or 1year2sec.
	 * No unit means default unit, which is seconds.
	 * Supported units are:
	 * s, sec, second, seconds,
	 * m, min, minute, minutes,
	 * h, hour, hours,
	 * d, day, days,
	 * w, week, weeks,
	 * y, year, years.
	 * @param $duration string is the duration in human format.
	 * @return int duration as seconds
	 * */
	public static function humanToSeconds($duration)
	{
		if (is_int($duration)) { return $duration; }
		if (!is_string($duration)) { return 0; }
		if (is_numeric($duration)) { return (int)$duration; }
		$duration = trim(mb_strtolower($duration));
		if (!preg_match('/^(?:(?:[0-9 ]+[sihdwmy]*)+)$/', $duration)) { return 0; }
		
		$multis = array('s' => 1, 'm' => 60, 'h' => 3600, 'd' => 86400, 'w' => 86400*7, 'y' => 31536000);
		$replace = array(
			'seconds' => 's', 'second' => 's', 'sec' => 's',
			'minutes' => 'm', 'minute' => 'm', 'min' => 'm',
		    'hours' => 'h', 'hour' => 'h',
		    'days' => 'd', 'day' => 'd',
		    'weeks' => 'w', 'week' => 'w',
			'years' => 'y', 'year' => 'y',
		);
		
		$negative = 1;
		$duration = mb_strtolower(trim($duration));
		if ($duration[0] == '-')
		{
			$negative = -1;
		}
		$duration = trim($duration, '-');
		$duration = str_replace(array_keys($replace), array_values($replace), $duration);
		// 		$duration = preg_replace('/[^sihdwmy0-9]/', '', $duration);
		$duration = preg_replace('/([sihdwmy])/', '$1 ', $duration);
		$duration = explode(' ', trim($duration));
		$back = 0;
		foreach ($duration as $d)
		{
			if ($d = trim($d))
			{
				$unit = mb_substr($d, -1);
				if (is_numeric($unit))
				{
				    $back += $unit;
				    continue;
				}
				else
				{
				    $d = mb_substr($d, 0, -1);
				}
				$d = floatval($d);
				$back += $multis[$unit] * $d;
			}
		}
		return $negative * $back;
	}
	
	#############
	### Parts ###
	#############
	public static function getYear($date) { return substr($date, 0 , 4); }
	public static function getMonth($date) { return substr($date, 5 , 2); }
	public static function getDay($date) { return substr($date, 8 , 2); }
	
	########################
	### Calendar utility ###
	########################
	/**
	 * Get timestamp of start of this week. (Monday)
	 * @return int unix timestamp.
	 * */
	public static function getTimeWeekStart()
	{
	    return strtotime('previous monday', Application::$TIME + self::ONE_DAY);
	}
	
	// 	/**
	// 	 * Get Long Weekday Names (translated), starting from monday. returns array('monday', 'tuesday', ...);
	// 	 * @return array
	// 	 */
	// 	public static function getWeekdaysFromMo()
	// 	{
	// 		return array(t('D1'),t('D2'),t('D3'),t('D4'),t('D5'),t('D6'),t('D0'));
	// 	}
		
	// 	/**
	// 	 * Compute the week of the day for a given GDO_Date.
	// 	 * 0=Sunday.
	// 	 * @param $gdo_date string min length 8
	// 	 * @return int 0-6
	// 	 */
	// 	public static function computeWeekDay($date)
	// 	{
	// 		$century = array('12' => 6, '13' => 4, '14' => 2, '15'=> 0, # <-- not sure if these are correct :(
	// 		'16'=>6, '17'=>4, '18'=>2, '19'=>0, '20'=>6, '21'=>4, '22'=>2, '23'=>0); # <-- these are taken from wikipedia
	// 		static $months = array(array(0,3,3,6,1,4,6,2,5,0,3,5), array(6,2,3,6,1,4,6,2,5,0,3,5));
	// 		$step1 = $century[substr($date, 0, 2)];
	// 		$y = intval(substr($date, 2, 2), 10); // step2
	// 		$m = intval(substr($date, 5, 2), 10);
	// 		$d = intval(substr($date, 8, 2), 10);
	// 		$leap = ($y % 4) === 0 ? 1 : 0;
	// 		$step3 = intval($y / 4);
	// 		$step4 = $months[$leap][$m-1];
	// 		$sum = $step1 + $y + $step3 + $step4 + $d;
	// 		return $sum % 7;
	// 	}
	
	// 	public static function getNumDaysForMonth($month, $year)
	// 	{
	// 		$leap = (($year % 4) === 0) || (($year % 100) === 0);
	// 		switch ($month)
	// 		{
	// 			case 1: case 3: case 5: case 7: case 8: case 10: case 12: return 31;
	// 			case 4: case 6: case 9: case 11: return 30;
	// 			case 2: return $leap ? 29 : 28;
	// 			default: return false;
	// 		}
	// 	}
}
	
date_default_timezone_set('UTC');
Time::$UTC = Time::getTimezoneObject('UTC');
