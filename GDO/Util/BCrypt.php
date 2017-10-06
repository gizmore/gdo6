<?php
namespace GDO\Util;
/**
 * Password hash
 * @author gizmore
 */
final class BCrypt
{
	###############
	### Factory ###
	###############
	public static $OPTIONS = [
		'cost' => 11,
	];
	
	public static function create($plaintext)
	{
		return new self(password_hash($plaintext, PASSWORD_BCRYPT, self::$OPTIONS));
	}
	
	###############
	### Members ###
	###############
	private $hash;
	
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
