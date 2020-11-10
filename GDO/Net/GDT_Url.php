<?php
namespace GDO\Net;
use GDO\DB\GDT_String;
use GDO\Util\Arrays;
use GDO\Core\GDT_Template;
use GDO\UI\WithTitle;
/**
 * URL field.
 * Features link checking.
 * Value is a @see URL.
 * 
 * @author gizmore
 * @version 6.10
 * @since 5.0
 */
class GDT_Url extends GDT_String
{
    use WithTitle;
    
    ##############
    ### Static ###
    ##############
	public static function host() { return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : GWF_DOMAIN; }
	public static function protocol() { return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'; }
	public static function absolute($url) { return sprintf('%s://%s%s', self::protocol(), self::host(), self::relative($url)); }
	public static function relative($url) { return $url; }
	
	public function defaultLabel() { return $this->label('url'); }
	
	public $reachable = false;
	public $allowLocal = false;
	public $allowExternal = true;
	public $schemes = ['http', 'https'];
	public $noFollow = false;
	
	public $min = 0;
	public $max = 1024;
	
	public $icon = 'url';
	
	public function toValue($var)
	{
		return $var ? new URL($var) : null;
	}
	
	public function toVar($value)
	{
	    return $value ? $value->raw : null;
	}
	
	##############
	### Render ###
	##############
	public function renderCell() { return GDT_Template::php('Net', 'cell/url.php', ['field' => $this]); }
	
	###############
	### Options ###
	###############
	public function allowLocal($allowLocal=true)
	{
		$this->allowLocal = $allowLocal;
		return $this;
	}
	
	public function allowExternal($allowExternal=true)
	{
		$this->allowExternal = $allowExternal;
		return $this;
	}
	
	public function reachable($reachable=true)
	{
		$this->reachable = $reachable;
		return $this;
	}
	
	public function schemes(...$schemes)
	{
	    $this->schemes = $schemes;
	    return $this;
	}
	
	public function noFollow($noFollow=true)
	{
	    $this->noFollow = $noFollow;
	    return $this;
	}

	################
	### Validate ###
	################
	public function validate($value)
	{
		if (!parent::validate($value?$value->raw:null))
		{
			return false;
		}
		return $this->validateUrl($value);
	}
	
	public function validateUrl(URL $url=null)
	{
		# null seems allowed
	    if ((!$url) || (null === ($value = $url->raw)))
		{
			return true;
		}

		# Check local
		if ( (!$this->allowLocal) && ($value[0] === '/') )
		{
			return $this->error('err_local_url_not_allowed', [html($value)]);
		}
		
		if ( (!$this->allowExternal) && ($value[0] !== '/') )
		{
			return $this->error('err_external_url_not_allowed', [html($value)]);
		}
		
		# Check schemes (if external). internal are always prefixed with /
		if ($this->allowExternal)
		{
    		if (count($this->schemes))
    		{
        		if (!in_array($url->getScheme(), $this->schemes, true))
        		{
        		    return $this->error('err_url_scheme', [Arrays::implodeHuman($this->schemes)]);
        		}
    		}
		}
		
		# Check reachable
		if ( ($this->reachable) && (!HTTP::pageExists($value)) )
		{
		    if ($value[0] === '/')
		    {
		        return true;
		    }
		    return $this->error('err_url_not_reachable', [html($value)]);
		}
		
		return true;
	}

}
