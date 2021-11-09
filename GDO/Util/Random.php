<?php
namespace GDO\Util;

/**
 * Random utility functions.
 * 
 * @TODO: Write a fantasy name generator that works with syllabels.
 * 
 * @author gizmore
 * @author noother
 * @author dloser
 * 
 * @version 6.10.4
 * @since 3.0.5
 */
final class Random
{
	const TOKEN_LEN = 16;
	const RAND_MAX = 4294967295;

	const NUMERIC = '0123456789';
	const ALPHAUP = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const ALPHALOW = 'abcdefghijklmnopqrstuvwxyz';
	const HEXLOWER = 'abcdef0123456789';
	const HEXUPPER = 'ABCDEF0123456789';
	const ALPHANUMLOW = 'abcdefghijklmnopqrstuvwxyz0123456789';
	const ALPHANUMUPLOW = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	const ALPHANUMUPLOWSPECIAL = '!"\'_.,%&/()=<>;:#+-*~@abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	/**
	 * Get a single random item from an array.
	 * This is not cryptographically safe.
	 * @param array $array
	 * @return mixed
	 */
	public static function arrayItem(array $array)
	{
		return $array[array_rand($array, 1)];
	}

	/**
	 * Generate a randomkey from a charset. A bit slow but should be random.
	 * 
	 * @param $len int
	 * @param $alpha string or true
	 * @return string
	 */
	public static function randomKey($len=self::TOKEN_LEN, $alpha=self::ALPHANUMUPLOW)
	{
		$alphalen = strlen($alpha) - 1;
		$key = '';
		for($i = 0; $i < $len; $i++)
		{
			$key .= $alpha[self::rand(0, $alphalen)];
		}
		return $key;
	}


	/**
	 * Secure and evenly distributed random generator.
	 * 
	 * @author noother
	 * @author gizmore
	 * @param int $min
	 * @param int $max
	 * @return int
	 */
	public static function rand($min=0, $max=self::RAND_MAX)
	{
		# Generate random numbers
		static $BUFFER;
		if (empty($BUFFER) || (strlen($BUFFER) < 4))
		{
			$BUFFER = openssl_random_pseudo_bytes(1024);
		}

		# Take 4 bytes and unpack to a signed int
		$n = unpack('L', substr($BUFFER, 0, 4));
		# thx to dloser we convert to unsigned on 32 bit arch
		$n = PHP_INT_SIZE === 4 ? $n[1] + 2147483648 : $n[1];

		# Eat from random buffer
		$BUFFER = substr($BUFFER, 4);

		# Evenly distributed
		return (int) ( $min + ($max-$min) * ($n/(self::RAND_MAX+1.0)) );
	}

	################
	### Insecure ### but faster
	################
	public static function srand($seed)
	{
	    srand();
	}

	/**
	 * Get an insecure random number.
	 * @param int $min
	 * @param int $max
	 * @return int
	 */
	public static function mrand($min=null, $max=null)
	{
	    return rand($min, $max);
	}

	/**
	 * Get an insecure random key.
	 * @param int $len
	 * @param string $alpha
	 * @return string
	 */
	public static function mrandomKey($len=self::TOKEN_LEN, $alpha=self::ALPHANUMUPLOW)
	{
	    $alphalen = strlen($alpha) - 1;
	    $key = '';
	    for($i = 0; $i < $len; $i++)
	    {
	        $key .= $alpha[self::mrand(0, $alphalen)];
	    }
	    return $key;
	}

}
