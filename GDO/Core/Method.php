<?php
namespace GDO\Core;

use GDO\DB\Database;
use GDO\Register\Module_Register;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\UI\WithTitle;
use GDO\Session\GDO_Session;
use GDO\Date\Time;
use GDO\Language\Trans;

/**
 * Abstract baseclass for all methods.
 * 
 * There are some derived method classes for forms, tables and cronjobs.
 * Provides transaction wrapping and permission checks.
 * Provides parameters via ->gdoParameters().
 * 
 * @see MethodTable
 * @see MethodQueryTable
 * @see MethodForm
 * @see MethodCrud
 * @see MethodCronjob
 *
 * @author gizmore
 * @version 6.10.1
 * @since 3.0.0
 */
abstract class Method
{
	use WithName;
	use WithTitle;

	/**
	 * @return GDT_Response
	 */
	public abstract function execute();
	
	/**
	 * @return self
	 */
	public static function make() { return new static(); }
	
	################
	### Override ###
	################
	/**
	 * @return GDT_Response
	 */
	public function init() {}
	public function isAjax() { return false; }
	public function isEnabled() { $m = $this->getModule(); return $m && $m->isEnabled(); }
	public function isUserRequired() { return false; }
	public function isGuestAllowed() { return true; }
	public function isCookieRequired() { return false; }
	public function isSessionRequired() { return false; }
	public function isTransactional() { return false; }
	public function isAlwaysTransactional() { return false; }
	public function isTrivial() { return true; }
	public function getPermission() {}
	public function hasPermission(GDO_User $user) { return true; }
	
	/**
	 * Restrict this method to user types.
	 * @return string
	 */
	public function getUserType() {}
	
	/**
	 * Override this.
	 * Called after init() was not errorneous.
	 * @return ?GDT_Response
	 */
	public function beforeExecute() {}
	
	/**
	 * Override this.
	 * Called after execute() was not errorneous.
	 * @return ?GDT_Response
	 */
	public function afterExecute() {}
	
	/**
	 * Override this.
	 * Should this method save the current URL as last url?
	 * @see Website->hrefBack()
	 * @return boolean
	 */
	public function saveLastUrl() { return true; }
	
	/**
	 * Override this.
	 * Should this method be listed in a sitemap.
	 * @return boolean
	 */
	public function showInSitemap() { return true; }
	
	#############
	### Title ###
	#############
	public function getTitle()
	{
	    $key = $this->getTitleLangKey();
	    return t($key);
	}
	
	public function getTitleLangKey()
	{
	    return strtolower('mtitle_' . $this->getModuleName() . '_' . $this->getMethodName());
	}
	
	###################
	### Description ###
	###################
	public function getDescriptionLangKey()
	{
		return strtolower('mdescr_' . $this->getModuleName() . '_' . $this->getMethodName());
	}
	
	public function getDescription()
	{
	    $key = $this->getDescriptionLangKey();
	    return Trans::hasKey($key) ? t($key) : $this->getTitle();
	}
	
	################
	### Keywords ###
	################
	public function getKeywordsLangKey()
	{
	    return strtolower('mkeywords_' . $this->getModuleName() . '_' . $this->getMethodName());
	}
	
	public function getKeywords()
	{
	    $keywords = sitename() . ',' . t('keywords');
	    $key = $this->getKeywordsLangKey();
	    if (Trans::hasKey($key))
	    {
	        $keywords .= ',' . t($key);
	    }
	    return $keywords;
	}
	
	######################
	### GET Parameters ###
	######################
	/**
	 * Valid GET Parameters as GDT array.
	 * @see GDT
	 * @see gdoParameter()
	 * @see gdoParameterVar()
	 * @see gdoParameterValue()
	 * @return GDT[]
	 */
	public function gdoParameters() { return []; }
	
	/**
	 * Cached GET parameters.
	 * @var GDT[]
	 */
	private $paramCache = null;
	
	/**
	 * Build and/or get the GET parameter cache.
	 * @return GDT[]
	 */
	public function &gdoParameterCache()
	{
		if ($this->paramCache === null)
		{
		    $this->paramCache = [];
		    foreach ($this->gdoParameters() as $gdt)
		    {
		        $this->paramCache[$gdt->name] = $gdt;
		    }
		}
		return $this->paramCache;
	}
	
	/**
	 * @param string $key
	 * @return GDT
	 */
	public function gdoParameter($key, $initial=null)
	{
	    /** @var $gdt GDT **/
	    if ($gdt = @$this->gdoParameterCache()[$key])
	    {
    	    if ($initial !== null)
    	    {
    	        $gdt->var($initial); 
    	    }
    	    
	        $value = $gdt->getValue();
	        if (!$gdt->validate($value))
	        {
	            throw new GDOException($gdt->error);
	        }
	        $gdt->value($value); # copy to var again.
	        return $gdt;
	    }
	}
	
	/**
	 * @param string $key
	 * @return string
	 */
	public function gdoParameterVar($key)
	{
	    $gdt = $this->gdoParameter($key);
	    return $gdt->getRequestVar(null, $gdt->var);
	}
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function gdoParameterValue($key)
	{
		$gdt = $this->gdoParameter($key);
// 		return $gdt->getValue(); # bug!
		return $gdt->toValue($gdt->getVar());
	}
	
	public function methodVars(array $vars)
	{
		foreach ($vars as $key => $value)
		{
			$_REQUEST[$key] = $value;
		}
		return $this;
	}
	
	##############
	### Helper ###
	##############
	/**
	 * @return GDO_Module
	 */
	public function getModule() { return ModuleLoader::instance()->getModule($this->getModuleName()); }
	public function getFormat() { return Application::instance()->getFormat(); }
	public function getSiteName() { return sitename(); }
	public function getMethodName() { return $this->gdoShortName(); }
	public function getModuleName() { $c = static::class; return substr($c, 4, strpos($c, '\\', 6)-4); }
	public function href($app='') { return href($this->getModuleName(), $this->getMethodName(), $app); }
	public function error($key, array $args=null) { Website::topResponse()->add(GDT_Error::responseWith($key, $args)); return GDT_Response::make()->code(405); }
	public function message($key, array $args=null, $log=true) { Website::topResponse()->add(GDT_Success::responseWith($key, $args)); return GDT_Response::make(); }
	public function templatePHP($path, array $tVars=null) { return GDT_Template::responsePHP($this->getModuleName(), $path, $tVars); }
	public function getRBX() { return implode(',', array_map('intval', array_keys(Common::getRequestArray('rbx', [Common::getGetString('id')=>'on'])))); }

	############
	### Shim ###
	############
	/**
	 * @param array $params
	 * @return static
	 */
	public function requestParameters(array $params=null)
	{
	    $_REQUEST = [];
	    if ($params)
	    {
    	    foreach ($params as $key => $value)
    	    {
    	        $_REQUEST[$key] = $value;
    	    }
	    }
	    return $this;
	}
	
	#################
	### Auto HREF ###
	#################
	public function methodHref()
	{
		$append = '';
		foreach ($this->gdoParameterCache() as $gdt)
		{
			$append .= '&' . $gdt->name . '=' . urlencode($gdt->getVar());
		}
		return $this->href($append);
	}
	
	##################
	### Permission ###
	##################
	/**
	 * Check if user has permission to execute method.
	 * Override various function to customize your method:
	 * Methods: getUserType, isUserRequired, getPermission, hasPermission.
	 * @param GDO_User $user
	 * @return boolean
	 */
	public function hasUserPermission(GDO_User $user)
	{
		if ( ($this->isUserRequired()) && (!$user->isAuthenticated()) )
		{
			return false;
		}
		if ( (!$this->isGuestAllowed()) && (!$user->isMember()) )
		{
			return false;
		}
		if ( ($this->getUserType()) && ($this->getUserType() !== $user->getType()) )
		{
			return false;
		}
		if ( ($permission = $this->getPermission()) && (!$user->hasPermission($permission)) )
		{
			return false;
		}
		return $this->hasPermission($user);
	}
	
	############
	### Exec ###
	############
	/**
	 * Test permissions and execute method.
	 * @return GDT_Response
	 */
	public function exec()
	{
		if ($this->isAjax())
		{
			$_GET['fmt'] = $_REQUEST['fmt'] = 'json';
			$_GET['ajax'] = $_REQUEST['ajax'] = '1';
		}
		
		$user = GDO_User::current();
		
		if (!($this->isEnabled()))
		{
			return GDT_Error::responseWith('err_method_disabled');
		}
		
		if ( (!$this->isGuestAllowed()) && (!$user->isMember()) )
		{
			return GDT_Error::responseWith('err_members_only');
		}
		
		if ( ($this->isUserRequired()) && (!$user->isAuthenticated()) )
		{
			if (module_enabled('Register') && Module_Register::instance()->cfgGuestSignup())
			{
				$hrefGuest = href('Register', 'Guest', "&backto=".urlencode($_SERVER['REQUEST_URI']));
				return GDT_Error::responseWith('err_user_required', [$hrefGuest]);
			}
			else
			{
				return GDT_Error::responseWith('err_members_only');
			}
		}
		
		if ( ($this->getUserType()) && ($this->getUserType() !== $user->getType()) )
		{
			return GDT_Error::responseWith('err_user_type', [$this->getUserType()]);
		}
		
		if ( ($permission = $this->getPermission()) && (!$user->hasPermission($permission)) )
		{
			return GDT_Error::responseWith('err_permission_required', [t('perm_'.$permission)]);
		}
		
		if (true !== ($error = $this->hasPermission($user)))
		{
			return $error;
		}
		
		return $this->execWrap();
	}
	
	public function execMethod()
	{
		return $this->execWrap();
	}
	
	public function transactional()
	{
		return
		($this->isAlwaysTransactional()) ||
		($this->isTransactional() && (count($_POST)>0) );
	}
	
	/**
	 * Wrap execution in transaction if desired from method.
	 * @throws \Exception
	 * @return GDT_Response
	 */
	public function execWrap()
	{
	    # Exec
	    $response = $this->executeWithInit();
        
	    if ( (!$response) || (!$response->isError()) )
	    {
	        # SEO
	        Website::setTitle($this->getTitle());
	        Website::addMeta(['keywords', $this->getKeywords(), 'name']);
	        Website::addMeta(['description', $this->getDescription(), 'name']);
	        
	        # Store last URL in session
	        $this->storeLastURL();
	        
	        # Store last activity in user
	        $this->storeLastActivity();
	    }

	    return $response;
	}
	
	public function executeWithInit()
	{
	    $db = Database::instance();
	    $transactional = $this->transactional();
	   
	    try
	    {
	        # Wrap transaction start
	        if ($transactional) $db->transactionBegin();
	        
	        # Init method
	        $response = $this->init();
	        if ($response && $response->isError())
	        {
	            return $response;
	        }

	        # Exec 1.before - 2.execute - 3.after
	        GDT_Hook::callHook('BeforeExecute', $this);
	        $response = $response ? $response->add($this->beforeExecute()) : $this->beforeExecute();
	        $response = $response ? $response->add($this->execute()) : $this->execute();
	        $response = $response ? $response->add($this->afterExecute()) : $this->afterExecute();
	        GDT_Hook::callHook('AfterExecute', $this);
	        
	        # Wrap transaction end
	        if ($transactional) $db->transactionEnd();
	        
	        return $response;
	    }
	    catch (\Throwable $e)
	    {
	        if ($transactional) $db->transactionRollback();
	        throw $e;
	    }
	}
	
	####################
	### Last Actions ###
	####################
	private function storeLastURL()
	{
	    if ($this->saveLastUrl())
	    {
    	    if ($session = GDO_Session::instance())
    	    {
    	        if ( (!Application::instance()->isAjax()) && (!$this->isAjax()) )
    	        {
    	            # Will be saved on process exit.
    	            $session->setVar('sess_last_url', @$_SERVER['REQUEST_URI']);
    	        }
    	    }
	    }
	}
	
	private function storeLastActivity()
	{
	    if (!$this->isAjax())
	    {
	        $user = GDO_User::current();
	        if ($user->isPersisted())
	        {
	            $lastActivity = substr(Time::getDate(), 0, 16) . ':00.000';
	            $user->saveVar('user_last_activity', $lastActivity);
	        }
	    }
	}
	
}
