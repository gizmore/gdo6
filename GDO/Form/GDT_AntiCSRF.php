<?php
namespace GDO\Form;

use GDO\Core\GDT_Template;
use GDO\Session\GDO_Session;
use GDO\Util\Random;
use GDO\Core\Application;

/**
 * GDT_Form CSRF protection
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 1.0.0
 */
class GDT_AntiCSRF extends GDT_Hidden
{
    const KEYLEN = 6;
    const MAX_KEYS = 20;
    
    public $name = 'xsrf';
    public $editable = false;
	public function name($name=null) { return $this; }

	protected function __construct()
	{
	    parent::__construct();
	    $this->csrfToken();
	}
	
	###########
	### GDT ###
	###########
	public function getGDOData()
	{
	    # Override GDT_Hidden with null data.
	}
	
	##############
	### Expire ###
	##############
	public $csrfExpire = 60*30; # 0.5 hours is a sensible default.
	public function csrfExpire($csrfExpire)
	{
		$this->csrfExpire = $csrfExpire;
		return $this;
	}
	
	###############
	### Cleanup ###
	###############
	public $csrfMaxTokens = 12; # Last 12 forms should be fine
	public function csrfMaxTokens($csrfMaxTokens)
	{
		$this->csrfMaxTokens = $csrfMaxTokens;
		return $this;
	}
	
	#################
	### Construct ###
	#################
	public function csrfToken()
	{
	    $token = '';
		if (GDO_Session::instance())
		{
		    $token = Random::randomKey(self::KEYLEN);
		    $csrf = $this->loadCSRFTokens();
		    $csrf[$token] = Application::$TIME;
		    $this->saveCSRFTokens($csrf);
		}
		return $token;
	}
	
	###################
	### Load / Save ###
	###################
	private function loadCSRFTokens()
	{
	    $csrf = GDO_Session::get('csrfs');
	    $csrf = json_decode($csrf, true);
	    return $csrf ? $csrf : [];
	}
	
	private function saveCSRFTokens(array $csrf)
	{
	    $count = count($csrf);
	    if ($count > self::MAX_KEYS) # max 2 tokens?
	    {
	        array_slice($csrf, $count - self::MAX_KEYS);
	    }
	    GDO_Session::set('csrfs', json_encode($csrf));
	}
	
	################
	### Validate ###
	################
	public function validate($value)
	{
	    $app = Application::instance();
	    if ($app->isCLI() || $app->isUnitTests())
	    {
	        return true;
	    }
	    
	    # No session, no token
	    if (!GDO_Session::instance())
		{
			return $this->error('err_session_required');
		}

		# Load tokens
		$csrf = $this->loadCSRFTokens();
		
		# Remove expired
		foreach ($csrf as $token => $time)
		{
		    if (Application::$TIME > ($time + $this->csrfExpire))
		    {
		        unset($csrf[$token]);
		    }
		}
		
		# Token not there
		if (!isset($csrf[$value]))
		{
			return $this->error('err_csrf');
		}

		# Remove used  token
		unset($csrf[$value]);
		$this->saveCSRFTokens($csrf);
		
		# All fine
		return true;
	}

	##############
	### Render ###
	##############
	public function renderForm()
	{
		return GDT_Template::php('Form', 'form/csrf.php', ['field'=>$this]);
	}
	
	public function jsonFormValue()
	{
		return $this->csrfToken();
	}
	
}
