<?php
namespace GDO\Net;

use GDO\DB\GDT_String;

/**
 * IP column and rendering.
 * Current IP is assigned at the very bottom.
 * @author gizmore
 * @version 6.10.4
 * @since 6.0.0
 */
final class GDT_IP extends GDT_String
{
    public $searchable = false;

	###############
	### IP Util ###
	###############
	public static $CURRENT = null; # for connections like websocket too!
	public static function current() { return self::$CURRENT; }

	/**
	 * Get the IP netmask for a number of bits.
	 * @example netmask(8) => 11111111 00000000 00000000 00000000 =>
	 * @param int $bits
	 * @return int
	 */
	public static function netmask($bits)
	{
	    return bindec(str_repeat('1', $bits) . str_repeat('0', 32 - $bits));
	}

	public static function isLocal($ip=null)
	{
		$ip = $ip ? $ip : self::$CURRENT;
		return
		  ($ip === '::1') ||
		  (substr($ip, 0, 4) === '127.') ||
		  (substr($ip, 0, 8) === '192.168.') ||
		  (substr($ip, 0, 8) === '169.254.') ||
		  (substr($ip, 0, 3) === '10.') ||
		  ((ip2long($ip) & self::netmask(12)) === bindec('10101100000100000000000000000000'));
	}

	public function useCurrent($useCurrent=true)
	{
	    if (!$useCurrent)
	    {
	        return $this->initial(null);
	    }
	    else
	    {
    	    return $this->initial(self::current());
	    }
	}

	##############
	### String ###
	##############
	public $min = 3;
	public $max = 45;
	public $encoding = self::ASCII;
	public $caseSensitive = true;
	public $pattern = "/^[.:0-9A-F]{3,45}$/";
	public $writable = false;
	public $editable = false;
	public $icon = 'url';

	public function defaultLabel() { return $this->label('ip'); }

	/**
	 * Uppercase IPv6 to speed up DB lookups.
	 * {@inheritDoc}
	 * @see \GDO\DB\GDT_String::getVar()
	 */
	public function getVar()
	{
		return strtoupper(parent::getVar());
	}
}

# Assign current IP.
GDT_IP::$CURRENT = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
