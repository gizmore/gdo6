<?php
namespace GDO\Core;

use GDO\DB\Database;
use GDO\Register\Module_Register;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Util\Strings;
use GDO\UI\WithTitle;
use GDO\User\GDO_Session;
use GDO\Date\Time;

/**
 * Abstract baseclass for all methods.
 * 
 * There are some derived method classes for forms, tables and cronjobs.
 * Provides transaction wrapping and permission checks.
 * 
 * @see MethodTable
 * @see MethodQueryTable
 * @see MethodForm
 * @see MethodCrud
 *
 * @author gizmore
 * @version 6.10
 * @since 1.0
 */
abstract class Method
{
	use WithName;
	use WithTitle;

	/**
	 * @return GDT_Response
	 */
	abstract public function execute();
	
	/**
	 * @return self
	 */
	public static function make() { $class = get_called_class(); return new $class(); }
	
	################
	### Override ###
	################
	public function isAjax() { return false; }
	public function isEnabled() { return true; }
	public function isUserRequired() { return false; }
	public function isGuestAllowed() { return true; }
	public function isCookieRequired() { return false; }
	public function isSessionRequired() { return false; }
	public function isTransactional() { return false; }
	public function isAlwaysTransactional() { return false; }
	public function getPermission() {}
	public function hasPermission(GDO_User $user) { return true; }
	public function getUserType() {}
	public function init() {}
	public function beforeExecute() {}
	public function afterExecute() {}
	public function saveLastUrl() { return true; }
	
	######################
	### GET Parameters ###
	######################
	/**
	 * @return GDT[]
	 */
	public function gdoParameters() { return []; }
	/**
	 * @param string $key
	 * @return GDT
	 */
	public function gdoParameter($key)
	{
		foreach ($this->gdoParameters() as $gdt)
		{
			if ($gdt->name === $key)
			{
				$var = $gdt->getRequestVar(null, $gdt->initial);
				$value = $gdt->toValue($var);
				$gdt->var = $var;
				if (!$gdt->validate($value))
				{
// 					throw new GDOException($gdt->error);
					$gdt->var = $gdt->initial;
				}
				return $gdt;
			}
		}
	}
	
	/**
	 * @param string $key
	 * @return string
	 */
	public function gdoParameterVar($key)
	{
		$gdt = $this->gdoParameter($key);
		return $gdt->var;
	}
	
	/**
	 * @param string $key
	 * @return string
	 */
	public function gdoParameterValue($key)
	{
		$gdt = $this->gdoParameter($key);
		return $gdt->toValue($gdt->var);
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
	public function getFormat() { return Application::instance()->getFormat(); }
	public function getSiteName() { return sitename(); }
	public function getMethodName() { return Strings::rsubstrFrom(get_called_class(), '\\'); }
	public function getModuleName() { return Strings::substrTo(Strings::substrFrom(get_called_class(), '\\'), '\\'); }
	public function module() { return ModuleLoader::instance()->getModule($this->getModuleName()); }
	public function href($app='') { return href($this->getModuleName(), $this->getMethodName(), $app); }
	public function error($key, array $args=null) { return GDT_Error::responseWith($key, $args); }
	public function message($key, array $args=null, $log=true) { return GDT_Success::responseWith($key, $args); }
	public function templatePHP($path, array $tVars=null) { return GDT_Template::responsePHP($this->getModuleName(), $path, $tVars); }
	public function getRBX() { return implode(',', array_map('intval', array_keys(Common::getRequestArray('rbx', [Common::getGetString('id')=>'on'])))); }
	
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
			$_GET['fmt'] = 'json';
			$_GET['ajax'] = '1';
		}
		
		$user = GDO_User::current();
		
		if ($session = GDO_Session::instance())
		{
			if ( ($this->saveLastUrl()) && (!Application::instance()->isAjax()) )
			{
				$session->setVar('sess_last_url', $_SERVER['REQUEST_URI']);
			}
		}
		
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
		
		return $this->execMethod();
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
		$db = Database::instance();
		$transactional = $this->transactional();
		
		try
		{
			# Wrap transaction start
			if ($transactional) $db->transactionBegin();
			
			# Init method
			$this->init();
			
			# Exec 1)before, 2)execute, 3)after
			$response = $this->beforeExecute();
			$response = $response ? $response->add($this->execute()) : $this->execute();
			$response = $response ? $response->add($this->afterExecute()) : $this->afterExecute();
			
			# store activity timestamp
			if (!$this->isAjax())
			{
				$user = GDO_User::current();
				if ($user->isPersisted())
				{
					$user->saveVar('user_last_activity', Time::getDate());
				}
			}

			# Wrap transaction end
			if ($transactional) $db->transactionEnd();
			
			return $response;
		}
		catch (\Exception $e)
		{
			if ($transactional) $db->transactionRollback();
			throw $e;
		}
	}
	
}
