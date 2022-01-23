<?php
namespace GDO\Net;

use GDO\DB\GDT_String;

/**
 * IP column and rendering.
 * Current IP is assigned at the very bottom.
 * 
 * @author gizmore
 * @version 6.11.3
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
		  (str_starts_with($ip, '127')) ||
		  (str_starts_with($ip, '192.168')) ||
		  (str_starts_with($ip, '169.254')) ||
		  (str_starts_with($ip, '10.')) ||
		  ((ip2long($ip) & self::netmask(12)) === bindec('10101100000100000000000000000000'));
	}
	
	###############
	### Current ###
	###############
	public function useCurrent($useCurrent=true)
	{
		$initial = $useCurrent ? self::$CURRENT : null;
        return $this->initial($initial);
	}

	##############
	### String ###
	##############
	public $min = 3;
	public $max = 45;
	public $encoding = self::ASCII;
	public $caseSensitive = true;
	public $pattern = "/^[.:0-9A-Fa-f]{3,45}$/";
	public $writable = false;
	public $editable = false;
	public $icon = 'url';
	
	public function defaultLabel() { return $this->label('ip'); }

}

# Assign current IP.
GDT_IP::$CURRENT = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
