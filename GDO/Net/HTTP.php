<?php
namespace GDO\Net;

use GDO\Core\GDT_Error;

/**
 * - very simple HTTP get/post using curl.
 * 
 * - simple HTTP Nocache headers.
 * 
 * @author gizmore
 * @version 6.11.2
 * @since 3.0.0
 */
final class HTTP
{
	public static $DEBUG = false;
	#####################
	### head/get/post ###
	#####################
	const DEFAULT_TIMEOUT = 20;
	const DEFAULT_TIMEOUT_CONNECT = 6;
	const USERAGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36';
#	const USERAGENT_LINUX = 'Mozilla/5.0 (X11; Linux x86_64; rv:53.0) Gecko/20100101 Firefox/53.0';
	public static $TIMEOUT = self::DEFAULT_TIMEOUT;
	public static $TIMEOUT_CONNECT = self::DEFAULT_TIMEOUT_CONNECT;

	/**
	 * Check if a page exists.
	 * @param string $url
	 * @return true|false
	 */
	public static function pageExists($url)
	{
		if (@$url[0] === '/')
		{
			$url = GDO_PROTOCOL . '://' . GDT_Url::hostWithPort() . $url;
		}

		if (!($ch = curl_init($url)))
		{
			return false;
		}
		
		if (self::$DEBUG)
		{
			curl_setopt($ch, CURLOPT_VERBOSE, true);
		}
		
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP|CURLPROTO_HTTPS);
		
		# Set the user agent - might help, doesn't hurt
		curl_setopt($ch, CURLOPT_USERAGENT, self::USERAGENT);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 
		# Enable cookie engine with not saving the cookies to disk
		curl_setopt($ch, CURLOPT_COOKIEFILE, "");
		
		# Try to follow redirects
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		
		# Timeout
  		curl_setopt($ch, CURLOPT_TIMEOUT, self::$TIMEOUT);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$TIMEOUT_CONNECT);
 
		/* don't download the page, just the header (much faster in this case) */
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
 
		# Handle HTTPS links
		if (false === ($parts = parse_url($url)))
		{
			return false;
		}
		if(isset($parts['scheme']) && $parts['scheme']=='https')
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); # Should be 1!
			curl_setopt($ch, CURLOPT_SSLVERSION, 1);
		}
 
		$response = curl_exec($ch);

		curl_close($ch);
		
		# Get the status code from HTTP headers
		$matches = [];
		if(preg_match('/HTTP\/[12](?:\.\d+)?\s+(\d+)/', $response, $matches))
		{
			$code = intval($matches[1]);
  		} 
  		else
  		{
  			return false;
  		}
 
  		# See if code indicates success
  		return (($code>=200) && ($code<400)) ||
  		       ($code === 403) ||
  		       ($code === 401);	
	}

	/**
	 * Do a get request to an URL.
	 * @param string $url
	 * @param boolean $returnHeader
	 * @param false|string $cookie
	 * @return string content from curl request.
	 */
	public static function getFromURL($url, $returnHeader=false, $cookie=false, array $httpHeaders=null)
	{
		# Cleanup URL
		$url = trim($url);
		$replace = array(
			" " => "%20",
		);
		$url = str_replace(array_keys($replace), array_values($replace), $url);
		
		$ch = curl_init();
		
		if (self::$DEBUG)
		{
			curl_setopt($ch, CURLOPT_VERBOSE, true);
		}
		
		if ( (!empty($httpHeaders)) && (is_array($httpHeaders)) )
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
		}
		
		
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP|CURLPROTO_HTTPS);
		
		# Try to follow redirects
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		
		# Cookie stuff
		if ($cookie !== false)
		{
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		# Enable cookie engine with not saving the cookies to disk
		curl_setopt($ch, CURLOPT_COOKIEFILE, GDO_PATH."temp/test.cookie");
		
		
		curl_setopt($ch, CURLOPT_USERAGENT, self::USERAGENT);
		
  		curl_setopt($ch, CURLOPT_TIMEOUT, self::$TIMEOUT);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$TIMEOUT_CONNECT);
		
 		curl_setopt($ch, CURLOPT_URL, $url);
 		
// 		$returnHeader = true;
 		if (is_bool($returnHeader))
 		{
 			curl_setopt($ch, CURLOPT_HEADER, $returnHeader);
 		}
 		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		# Handle HTTPS links
		if(str_starts_with($url, 'https://'))
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); # should be 1!
			curl_setopt($ch, CURLOPT_SSLVERSION, 1);
		}
		
		if (!($received = curl_exec($ch)))
		{
			echo GDT_Error::with('err_curl', [
				curl_errno($ch), curl_error($ch), html($url)])->
					render();
		}
		
		curl_close($ch);
		
		return $received;
	}
	
	/**
	 * Send a post request to an URL.
	 * @param string $url
	 * @param string|array $postdata
	 * @param boolean $returnHeader
	 * @param false|array $httpHeaders
	 * @param false|string $cookie
	 * @return string the page content
	 */
	public static function post($url, $postdata=[], $returnHeader=false, $httpHeaders=false, $cookie=false)
	{
		# Clean URL
		if (strlen($url) < 10)
		{
			return false;
		}
		if (false === ($parts = parse_url($url)))
		{
			return false;
		}
		
		$ch = curl_init();
		
		if (self::$DEBUG)
		{
			curl_setopt($ch, CURLOPT_VERBOSE, true);
		}

		# Optional HTTP headers
		if (is_array($httpHeaders))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
		}
		
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP|CURLPROTO_HTTPS);
		
		# Cookie stuff
		if ($cookie !== false) {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		# Enable cookie engine with not saving the cookies to disk
		curl_setopt($ch, CURLOPT_COOKIEFILE, GDO_PATH."temp/test.cookie");
		
		curl_setopt($ch, CURLOPT_URL, $url);
 		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		if($parts['scheme']=='https')
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSLVERSION, 1);
		}
		if ($returnHeader === true) {
			curl_setopt($ch, CURLOPT_HEADER, true);
		}
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, self::USERAGENT);
		
		if (is_array($postdata))
		{
			$data = [];
			foreach ($postdata as $key => $value)
			{
				$data[] = urlencode($key).'='.urlencode($value);
			}
			$postdata = implode("&", $data);
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		if (false === ($received = curl_exec($ch)))
		{
			echo GDT_Error::with('err_curl', [curl_errno($ch), curl_error($ch)])->render();
		}
		curl_close($ch);
		return $received;		
	}
	
	/**
	 * Disable caching for the current page.
	 * Could be split apart from this file.
	 */
	public static function noCache()
	{
		header('Cache-Control: no-cache, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0');
		header('Pragma: no-cache');
		header('Expires: 0');
	}
}
