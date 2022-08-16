<?php
namespace GDO\Util;

/**
 * @TODO: Move to module Crypto: Helper for AES-256 encryption.
 * @author gizmore
 * @version 6.11.0
 * @since 3.0.1
 */
final class AES
{
	const IV = 'MyHomeIsMyCastleIamhungrywhereisi'; # <-- 32 chars
	const CIPHER = 'aes-256-cbc';

	/**
	 * Encrypt with AES256 using the default IV.
	 * @param string $data
	 * @param string $key
	 */
	public static function encrypt($data, $key)
	{
		return self::encrypt4($data, $key, self::IV);
	}

	/**
	 * Encrypt with AES256. Use sha256($iv) as IV. It is recommended to call this with a funny IV over the above.
	 * @param string $data
	 * @param string $key
	 * @param string $iv
	 * @return string data
	 */
	public static function encrypt4($data, $key, $iv)
	{
	    return openssl_encrypt($data, self::CIPHER, $key, 0, $iv);
	}

	/**
	 * Encrypt with AES256. Use sha256($password) as key. Use a random IV and prepend to the output.
	 * This is probably the function you are looking for.
	 * @param string $data
	 * @param string $password
	 * @return string data
	 */
	public static function encryptIV($data, $password)
	{
	    $iv_size = openssl_cipher_iv_length(self::CIPHER);
	    $iv = openssl_random_pseudo_bytes($iv_size);
		$key = hash('SHA256', $password, true);
        $encrypted = base64_encode($iv).openssl_encrypt($data, self::CIPHER, $key, 0, $iv);
	    return $encrypted.hash_hmac("sha256",$encrypted,$key);
	}
	
	/**
	 * Decrypt data encrypted with with the encryptIV function above.
	 * @param string $data
	 * @param string $password
	 * @return string plaintext
	 */
	public static function decryptIV($data, $password)
	{
	    $iv_size = openssl_cipher_iv_length(self::CIPHER);
	    $iv64 = ((4 * floor($iv_size / 3)) + 3) & ~3;
        $hmac = substr($data,-64);
        $data = substr($data,0,-64);
        $key = hash('SHA256', $password, true);
        if($hmac !== hash_hmac("sha256",$data, $key)) //only decrypt if cookie has not been tampered
        { 
            return false;
        }
	    $iv = substr($data, 0, $iv64);
	    $iv = base64_decode($iv);
	    $data = substr($data, $iv64);
	    return openssl_decrypt($data, self::CIPHER, $key, 0, $iv);
	}

	/**
	 * Decrypt with AES256 using the default IV.
	 * @param string $data
	 * @param string $key
	 */
	public static function decrypt($data, $key)
	{
		return self::decrypt4($data, $key, self::IV);
	}

	/**
	 * Decrypt with AES256. Use sha256($iv) as IV.
	 * @param string $data
	 * @param string $key
	 * @param string $iv
	 * @return string data
	 */
	public static function decrypt4($data, $key, $iv)
	{
	    return openssl_decrypt($data, self::CIPHER, $key, 0, $iv);
	}

}

