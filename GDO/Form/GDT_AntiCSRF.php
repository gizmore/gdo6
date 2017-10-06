<?php
namespace GDO\Form;
use GDO\Core\GDT_Template;
use GDO\Core\GDT;
use GDO\User\GDO_Session;
use GDO\Util\Random;

/**
 * GDT_Form CSRF protection
 * @author gizmore
 * @version 5.0
 * @since 1.0
 */
class GDT_AntiCSRF extends GDT
{
    public $editable = false;
    public function name($name=null) { return $this; }
    ##############
	### Expire ###
	##############
	public $csrfExpire = 7200; # 2 hours is a sensible default.
	public function csrfExpire($csrfExpire)
	{
		$this->csrfExpire = $csrfExpire;
		return $this;
	}
	
	###############
	### Cleanup ###
	###############
	public $csrfMaxTokens = 30; # Last 30 forms should be fine
	public function csrfMaxTokens($csrfMaxTokens)
	{
		$this->csrfMaxTokens = $csrfMaxTokens;
		return $this;
	}
	
	#################
	### Construct ###
	#################
	public $name = 'xsrf';
	
	public function csrfToken()
	{
		$csrf = '';
		if (GDO_Session::instance())
		{
		    if (!($csrf = GDO_Session::get('xsrf')))
			{
				$csrf = Random::randomKey(8);
				GDO_Session::set('xsrf', $csrf);
			}
		}
		return $csrf;
	}
	
	################
	### Validate ###
	################
	public function validate($value)
	{
	    if (!GDO_Session::instance())
		{
			return $this->error('err_session_required');
		}

		# Check session for token
		$csrf = GDO_Session::get('xsrf');
		if ($csrf !== $value)
		{
			return $this->error('err_csrf');
		}
		
		$csrf = Random::randomKey(8);
		GDO_Session::set('xsrf', $csrf);
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
