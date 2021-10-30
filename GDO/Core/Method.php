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
use GDO\DB\Cache;
use GDO\File\FileUtil;
use GDO\File\Filewalker;
use GDO\Util\Strings;
use GDO\Util\CLI;
use GDO\Form\GDT_Submit;
use GDO\User\PermissionException;

/**
 * Abstract baseclass for all methods.
 * 
 * There are some derived method classes for forms, tables and cronjobs.
 * Provides transaction wrapping and permission checks.
 * Provides parameters via ->gdoParameters().
 * 
 * @TODO Rename init() to onInit()
 * 
 * @see MethodForm
 * @see MethodCrud
 * @see MethodTable
 * @see MethodQueryTable
 * @see MethodCronjob
 *
 * @author gizmore
 * @version 6.10.4
 * @since 3.0.0
 */
abstract class Method
{
	use WithName;
	use WithTitle;
	
	public static $CURRENT = null;

	/**
	 * @return GDT_Response
	 */
	public abstract function execute();
	
	/**
	 * @return self
	 */
	public static function make() { return new static(); }
	
	/**
	 * Get method identifier as css ID.
	 * @return string
	 */
	public function gdoMethodId()
	{
	    return strtolower(
	        $this->getModuleName() . '-' .
	        $this->getMethodName());
	}
	
	################
	### Override ###
	################
	/**
	 * @return GDT_Response
	 */
	public function init() {} # @TODO rename to onInit()
	public function isCLI() { return true; }
	public function isAjax() { return false; }
	public function isEnabled() { $m = $this->getModule(); return $m && $m->isEnabled(); }
	public function showSidebar() { return true; }
	public function isSEOIndexed() { return true; }
	public function isUserRequired() { return false; }
	public function isGuestAllowed() { return true; }
	public function isCookieRequired() { return false; }
	public function isSessionRequired() { return false; }
	public function isTransactional() { return false; }
	public function isAlwaysTransactional() { return false; }
	public function isTrivial() { return true; }
	public function isLockingSession() { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
	public function getPermission() {}
	public function hasPermission(GDO_User $user) { return true; }
	
	/**
	 * Override this.
	 * Should this method save the current URL as last url?
	 * @see Website->hrefBack()
	 * @return boolean
	 */
	public function saveLastUrl() { return true; }
	
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
	 * Should this method be listed in a sitemap?
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
	private $allParamCache = null;
	
	/**
	 * Build and/or get the GET parameter cache.
	 * @return GDT[]
	 */
	public function &gdoParameterCache()
	{
		if ($this->paramCache === null)
		{
		    $this->paramCache = [];
		    if ($params = $this->allParameters())
		    {
		        foreach ($params as $gdt)
    		    {
    		        $this->paramCache[$gdt->name] = $gdt;
    		    }
		    }
		}
		return $this->paramCache;
	}
	
	/**
	 * @param string $key
	 * @return GDT
	 */
	public function gdoParameter($key, $throw=true)
	{
	    /** @var $gdt GDT **/
	    if ($gdt = $this->gdoParameterCache()[$key])
	    {
	        $gdt->var($gdt->initial);
    	    $gdt->getRequestVar($gdt->formVariable(), $gdt->var);
	        $value = $gdt->getValue();
	        if (!$gdt->validate($value))
	        {
	            if ($throw)
	            {
	                throw new GDOParameterException($gdt, $value);
	            }
                return null;
	        }
	        else
	        {
	            $gdt->value($value); # copy to var again.
	        }
	        return $gdt;
	    }
	    elseif ($throw)
	    {
	        $this->errorParameter($key);
	    }
	}
	
	public function setGDOParamter($key, $var)
	{
	    $gdt = $this->gdoParameterCache()[$key];
	    $gdt->initial($var);
	    return $this;
	}
	
	/**
	 * Get a paramter by label. Useful for CLI.
	 * @param string $label
	 * @return GDT
	 */
	public function gdoParameterByLabel($label, $throw=true)
	{
	    foreach ($this->gdoParameterCache() as $gdt)
	    {
	        $lbl = $gdt->displayLabel();
	        if  ($gdt->name === $label)
	        {
	            return $gdt;
	        }
	        if (strcasecmp($lbl, $label) === 0)
	        {
	            return $gdt;
	        }
	        $lbl = $gdt->displayCLILabel();
	        if (strcasecmp($lbl, $label) === 0)
	        {
	            return $gdt;
	        }
	    }
	    if ($throw)
	    {
	        $this->errorParameter($label);
	    }
	}
	
	protected function errorParameter($label)
	{
	    throw new GDOError('err_unknown_parameter', [
	        $this->getModuleName(),
	        $this->getMethodName(),
	        html($label),
	    ]);
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
		return $gdt->toValue($gdt->getRequestVar(null, $gdt->var));
	}
	
	public function methodVars(array $vars)
	{
		foreach ($vars as $key => $value)
		{
			$_REQUEST[$key] = $value;
		}
		return $this;
	}
	
	/**
	 * @return GDT[]
	 */
	public function allParameters()
	{
	    return $this->gdoParameters();
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
	
	public function error($key, array $args=null, $code=405, $log=true)
	{
	    Website::topResponse()->addField(GDT_Error::with($key, $args, $code, $log));
	    return GDT_Response::make()->code($code);
	}
	
	public function errorRedirect($key, array $args=null, $code=405, $url=null, $time=null, $log=true)
	{
	    Website::redirectError($key, $args, $url, $time);
	    return $this->error($key, $args, $code, $log);
	}
	
	public function message($key, array $args=null, $log=true)
	{
	    Website::topResponse()->addField(GDT_Success::with($key, $args, 200, $log));
	    return GDT_Response::make();
	}
	
	public function messageRedirect($key, array $args=null, $url=null, $time=null, $log=true)
	{
	    Website::redirectMessage($key, $args, $url, $time);
	    return GDT_Response::make();
	}
	
	public function php($path, array $tVars=null)
	{
	    return GDT_Template::php($this->getModuleName(), $path, $tVars);
	}
	
	/**
	 * @param string $path
	 * @param array $tVars
	 * @return \GDO\Core\GDT_Template
	 */
	public function templatePHP($path, array $tVars=null)
	{
	    return GDT_Template::templatePHP(
	        $this->getModuleName(), $path, $tVars);
	}
	
	public function responsePHP($path, array $tVars=null)
	{
	    return GDT_Template::responsePHP(
	        $this->getModuleName(), $path, $tVars);
	}
	
	/**
	 * Get all selected checkboxes of a table column.
	 * @TODO get rid and move it to GDT_Checkbox. New option: allowToggleAll(true)
	 * @deprecated
	 * @return string
	 */
	public function getRBX()
	{
	    return implode(',', array_map('intval', 
	        array_keys(Common::getRequestArray('rbx',
	            [Common::getGetString('id')=>'on'])))); }

	############
	### Shim ###
	############
	/**
	 * @param array $params
	 * @return static
	 */
	public function requestParameters(array $params=null)
	{
	    $_REQUEST = $_GET = [];
	    if ($params)
	    {
    	    foreach ($params as $key => $value)
    	    {
    	        $_REQUEST[$key] = $_GET[$key] = $value;
    	    }
	    }
	    return $this;
	}
	
	#################
	### Auto HREF ###
	#################
	public function methodHref($append='')
	{
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
		if ( ($this->isUserRequired()) && (!$user->isUser()) )
		{
			return false;
		}
		if ( (!$this->isGuestAllowed()) && (!$user->isMember()) )
		{
			return false;
		}
		
		if ($mt = $this->getUserType())
		{
		    $ut = $user->getType();
		    if (is_array($mt))
		    {
		        if (!in_array($ut, $mt))
		        {
		            return false;
		        }
		    }
		    elseif ($ut !== $mt)
		    {
		        return false;
		    }
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
			$_GET['_ajax'] = $_REQUEST['_ajax'] = '1';
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
		
		if ($mt = $this->getUserType())
		{
		    $ut = $user->getType();
		    if (is_array($mt))
		    {
		        if (!in_array($ut, $mt))
		        {
		            return GDT_Error::responseWith(
		                'err_user_type', [implode(' / ', $this->getUserType())]);
		        }
		    }
		    elseif ($ut !== $mt)
		    {
		        return GDT_Error::responseWith('err_user_type', [$this->getUserType()]);
		    }
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
	        $this->setupSEO();
	    }

	    return $response;
	}
	
	public function setupSEO()
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
	
	public function executeWithInit()
	{
	    $db = Database::instance();
	    $transactional = $this->transactional();
	    try
	    {
	        self::$CURRENT = $this;
	        
	        # Wrap transaction start
	        if ($transactional) $db->transactionBegin();
	        
	        # Init method
	        $response = GDT_Response::newWith();
	        $response = GDT_Response::make();
	        
	        $response->addField($this->init());
	        
	        if ($response->isError())
	        {
	            if ($transactional) $db->transactionEnd();
	            return $response;
	        }
	        
	        # Exec 1.before - 2.execute - 3.after
	        GDT_Hook::callHook('BeforeExecute', $this, $response);
	        
	        $response = GDT_Response::make();
	        if ($response->hasError()) { return $response; }
	        
	        $response->addField($this->beforeExecute());
	        if ($response->hasError()) { return $response; }
	        
	        $response->addField($this->execute());

	        if (!$response->isError())
	        {
	            $response->addField($this->afterExecute());
    	        GDT_Hook::callHook('AfterExecute', $this, $response);
	        }
	        
	        # Wrap transaction end
	        if ($transactional) $db->transactionEnd();
	        
	        return $response;
	    }
	    catch (PermissionException $e)
	    {
	        if ($transactional) $db->transactionRollback();
	        return GDT_Response::makeWith(GDT_Error::make()->textRaw($e->getMessage()));
	    }
	    catch (\Throwable $e)
	    {
	        if ($transactional) $db->transactionRollback();
	        throw $e;
	    }
	    finally
	    {
// 	        self::$CURRENT = null;
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
	
	##################
	### File Cache ###
	##################
	public function fileCached() { return false; }
	public function fileCacheExpire() { return GDO_MEMCACHE_TTL; }
	
	public function fileCacheKey()
	{
	    $params = $this->gdoParameterCache();
	    $p = '';
	    foreach ($params as $gdt)
	    {
	        if ($gdt->name)
	        {
	            if ($v = $this->gdoParameterVar($gdt->name))
	            {
	                $p .= '_' . $gdt->name . '_' . FileUtil::saneFilename($v);
	            }
	        }
	    }
	    
	    $fmt = Application::instance()->getFormat();
	    return sprintf('method_%s_%s_%s_%s.%s',
	        $this->getModuleName(), $this->getMethodName(),
	        Trans::$ISO, $p, $fmt);
	}
	
	public function fileCacheKeyGroup()
	{
	    return sprintf('method_%s_%s_',
	        $this->getModuleName(), $this->getMethodName());
	}
	
	/**
	 * Get the cached content for this method, iso, fmt
	 * @return string|boolean
	 */
	public function fileCacheContent()
	{
	    if (!$this->hasUserPermission(GDO_User::current()))
	    {
	        return false;
	    }
	    $key = $this->fileCacheKey();
	    $content = Cache::fileGet($key, $this->fileCacheExpire());
	    return $content;
	}
	
	public function fileUncache()
	{
	    $start = $this->fileCacheKeyGroup();
	    Filewalker::traverse(Cache::filePath(), null, function($entry, $path) use ($start){
	        if (Strings::startsWith($entry, $start))
	        {
	            unlink($path);
	        }
	    });
	}

	/**
	 * Get the temp path for a method. Like temp/Module/Method/
	 * @param string $path - append
	 * @return string
	 */
	public function tempPath($path='')
	{
	    $module = $this->getModule();
	    $tempDir = $module->tempPath($this->gdoShortName());
	    @mkdir($tempDir, GDO_CHMOD, true);
	    $tempDir .= '/';
	    return $tempDir . $path;
	}
	
	###########
	### CLI ###
	###########
	public function getCLITrigger()
	{
	    return sprintf('%s.%s', $this->getModuleName(),  $this->getMethodName());
	}
	
	public function renderCLIHelp()
	{
	    return CLI::renderCLIHelp($this, $this->gdoParameterCache());
	}
	
	/**
	 * @return GDT_Submit[]
	 */
	public function getButtons()
	{
	    return [];
	}

}
