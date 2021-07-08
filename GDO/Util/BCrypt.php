<?php
namespace GDO\Util;

/**
 * Password hash object.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 */
final class BCrypt
{
	###############
	### Factory ###
	###############
	public static function options()
	{
	    return [
	        'cost' => env('BCRYPT_COST', 11),
	    ];
	}
	
	public static function create($plaintext)
	{
		return new self(password_hash($plaintext, PASSWORD_BCRYPT, self::options()));
	}
	
	###############
	### Members ###
	###############
	public $hash;
	
	public function __construct($hash)
	{
		$this->hash = $hash;
	}
	
	public function __toString()
	{
		return $this->hash;
	}
	
	public function validate($password)
	{
		return password_verify($password, $this->hash);
	}
	
}
