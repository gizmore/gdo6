<?php
namespace GDO\Util;

/**
 * String utility class.
 * Dedicated to Tim.
 * 
 * @author gizmore
 * @version 6.11.3
 * @since 3.0.0
 */
final class Strings
{
	#########################
	### Substring to/from ###
	#########################
	/**
	 * Get a substring from a string until an occurance of another string.
	 * @param string $s Haystack
	 * @param string $to Needle
	 * @param ?string $default Default result
	 * @return ?string
	 */
	public static function substrTo($s, $to, $default=null)
	{
		if (false !== ($index = strpos($s, $to)))
		{
			return substr($s, 0, $index);
		}
		return $default;
	}
	
	/**
	 * Take the portion of a string after/from a portion. You can nibble tokens with that. slow?
	 * 
	 * @param string $s
	 * @param string $from
	 * @param string $default
	 * @return string
	 */
	public static function substrFrom($s, $from, $default=null)
	{
		if (false !== ($index = strpos($s, $from)))
		{
			return substr($s, $index + strlen($from));
		}
		return $default;
	}
	
	/**
	 * Get a portion of $s from 0 to last occurance of $to.
	 * 
	 * @param string $s
	 * @param string $to
	 * @param string $default
	 * @return string
	 */
	public static function rsubstrTo($s, $to, $default=null)
	{
		if (false !== ($index = strrpos($s, $to)))
		{
			return substr($s, 0, $index);
		}
		return $default;
	}

	/**
	 * Get a portion of $s from the last occurance of $from.
	 * 
	 * @param string $s
	 * @param string $from
	 * @param string $default
	 * @return string
	 */
	public static function rsubstrFrom($s, $from, $default=null)
	{
		if (false !== ($index = strrpos($s, $from)))
		{
			return substr($s, $index + strlen($from));
		}
		return $default;
	}
	
	#######################
	### HTML safe nl2br ###
	#######################
	/**
	 * Changes newline to <br/> but only when no tags are open.
	 * @param string $s
	 * @return string
	 */
	public static function nl2brHTMLSafe($s)
	{
	    $s = trim($s, " \r\n\t");
	    $len = strlen($s);
	    $open = 0;
	    $back = '';
	    for ($i = 0; $i < $len; $i++)
	    {
	        $c = $s[$i];
	        if ($c === '<')
	        {
	            $back .= $c;
	            $open++;
	        }
	        elseif ($c === '>')
	        {
	            $back .= $c;
	            $open--;
	        }
	        elseif ($c === "\r")
	        {
	            # skip
	        }
	        elseif ($c === "\n")
	        {
	            if (!$open)
	            {
	                $back .= "<br/>\n"; # safe to convert
	            }
	            else
	            {
	                $back .= ' '; # Open tag. use space instead.
	            }
	        }
	        else
	        {
	            $back .= $c;
	        }
	    }
	    return $back;
	}
	
	###################
	### Args parser ###
	###################
	/**
	 * Parse a line into args.
	 * @see https://stackoverflow.com/a/18229461/13599483
	 * @param string $line
	 * @return string[]
	 */
	public static function args($line)
	{
	    $pattern = <<<REGEX
/(?:"((?:(?<=\\\\)"|[^"])*)"|'((?:(?<=\\\\)'|[^'])*)'|(\S+))/x
REGEX;
	    /** @var $matches string[] **/
	    preg_match_all($pattern, $line, $matches, PREG_SET_ORDER);
	    
	    # Choose right match
	    $args = [];
	    foreach ($matches as $match)
	    {
	        if (isset($match[3]))
	        {
	            $args[] = $match[3];
	        }
	        elseif (isset($match[2]))
	        {
	            $args[] = str_replace(['\\\'', '\\\\'], ["'", '\\'], $match[2]);
	        }
	        else
	        {
	            $args[] = str_replace(['\\"', '\\\\'], ['"', '\\'], $match[1]);
	        }
	    }
	    return $args;
	}
	
	###################
	### Trim dotted ###
	###################
	public static function dotted($text, $maxlen=50, $dots='…')
	{
		$len = mb_strlen($text);
		if ($len > $maxlen)
		{
			$text = mb_substr($text, 0, $maxlen-1);
			$text .= $dots;
		}
		return $text;
	}

}
