<?php
namespace GDO\Core;
use GDO\DB\Database;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Util\Strings;
use GDO\UI\WithTitle;
/**
 * Abstract baseclass for all methods.
 * There are some derived method classes for forms, tables and cronjobs.
 *
 * @author gizmore
 * @version 6.0
 * @since 1.0
 */
abstract class Method
{
    use WithName;
    use WithTitle;

    /**
     * @return \GDO\Core\GDT_Response
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
    public function getUserType() {}
    public function init() {}
    public function beforeExecute() {}
    public function afterExecute() {}
    
    ######################
    ### GET Parameters ###
    ######################
    public function gdoParameters() {}
    /**
     * @param string $key
     * @return GDT
     */
    public function gdoParameter($key)
    {
        return $this->gdoParameters()[$key];
    }
    
    /**
     * @param string $key
     * @return string
     */
    public function gdoParameterVar($key)
    {
        $this->gdoParameter($key)->getRequestVar();
    }
    
    /**
     * @param string $key
     * @return string
     */
    public function gdoParameterValue($key)
    {
        $gdoType = $this->gdoParameter($key);
        return $gdoType->toValue($gdoType->getRequestVar());
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
        
        if (!($this->isEnabled()))
        {
            return GDT_Error::responseWith('err_method_disabled');
        }
        
        if ( ($this->isUserRequired()) && (!$user->isAuthenticated()) )
        {
            $hrefGuest = href('Register', 'Guest');
            return GDT_Error::responseWith('err_user_required', [$hrefGuest]);
        }
        
        if ( (!$this->isGuestAllowed()) && (!$user->isMember()) )
        {
            return GDT_Error::responseWith('err_members_only');
        }
        
        if ( ($this->getUserType()) && ($this->getUserType() !== $user->getType()) )
        {
            return GDT_Error::responseWith('err_user_type', [$this->getUserType()]);
        }
        
        if ( ($permission = $this->getPermission()) && (!$user->hasPermission($permission)) )
        {
            return GDT_Error::responseWith('err_permission_required', [t('perm_'.$permission)]);
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
    
    public function execWrap()
    {
        $db = Database::instance();
        $transactional = $this->transactional();
        try
        {
            $this->init();
            $this->beforeExecute();
            if ($transactional) $db->transactionBegin();
            $response = $this->execute();
            if ($transactional) $db->transactionEnd();
            $this->afterExecute();
            return $response;
        }
        catch (\Exception $e)
        {
            if ($transactional) $db->transactionRollback();
            throw $e;
        }
    }
}
