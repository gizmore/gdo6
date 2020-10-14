<?php
namespace GDO\UI;

/**
 * Add HTML href capabilities.
 * @author gizmore
 * @since 6.00
 * @version 6.10
 */
trait WithHREF
{
	public $href;
	/**
	 * @param string $href
	 * @return self
	 */
	public function href($href=null) { $this->href = $href; return $this; }

	public function htmlHREF() { return sprintf(" href=\"%s\"", html($this->href)); }
	
	/**
	 * Replace a get parameter in URL.
	 * Adds if not found
	 * @param string $key
	 * @param string $value
	 * @return string
	 */
	public function replacedHREF($key, $value, $href=null)
	{
	    $href = $href === null ?  $this->href : $href;
	    
	    $new = "&{$key}=" . urlencode($value);
	    
	    if (strpos($href, "&$key=") !== false)
	    {
	        $key = preg_quote($key);
	        $href = preg_replace("#&{$key}=[^&]+#", $new, $href);
	    }
	    else
	    {
	        $href = $href . $new;
	    }
	    return $href;
	}

}
