<?php
if ( !function_exists('getallheaders'))
{
	/**
	 * Get all HTTP header key/values as an associative array for the current request.
	 *
	 * @return string[string] The HTTP header key/value pairs.
	 */
	function getallheaders()
	{
		$headers = array();

		$copy_server = array(
			'CONTENT_TYPE' => 'Content-Type',
			'CONTENT_LENGTH' => 'Content-Length',
			'CONTENT_MD5' => 'Content-Md5',
		);

		foreach ($_SERVER as $key => $value)
		{
			if (substr($key, 0, 5) === 'HTTP_')
			{
				$key = substr($key, 5);
				if ( !isset($copy_server[$key]) || !isset($_SERVER[$key]))
				{
					$key = str_replace(' ', '-',
					ucwords(strtolower(str_replace('_', ' ', $key))));
					$headers[$key] = $value;
				}
			}
			elseif (isset($copy_server[$key]))
			{
				$headers[$copy_server[$key]] = $value;
			}
		}

		if ( !isset($headers['Authorization']))
		{
			if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
			{
				$headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
			}
			elseif (isset($_SERVER['PHP_AUTH_USER']))
			{
				$basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
				$headers['Authorization'] = 'Basic ' .
				base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
			}
			elseif (isset($_SERVER['PHP_AUTH_DIGEST']))
			{
				$headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
			}
		}
		return $headers;
	}
}

if ( !function_exists('openssl_random_pseudo_bytes'))
{
	function openssl_random_pseudo_bytes($length, $crypto_strong)
	{
		$rand = '';
		for ($i = 0; $i < $length; $i++)
		{
			$rand .= chr(rand(0, 255));
		}
		return $rand;
	}
}

if ( !function_exists('str_starts_with'))
{
	function str_starts_with($haystack, $needle)
	{
		return $needle && (strpos($haystack, $needle) === 0);
	}
}

if ( !function_exists('str_ends_with'))
{
	function str_ends_with($haystack, $needle)
	{
		return substr_compare($haystack, $needle, -strlen($needle)) === 0;
	}
}
