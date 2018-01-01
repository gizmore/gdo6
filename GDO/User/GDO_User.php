<?php
namespace GDO\User;
use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_CreatedAt;
use GDO\DB\GDT_DeletedAt;
use GDO\Date\GDT_Date;
use GDO\Date\Time;
use GDO\DB\GDT_Enum;
use GDO\Core\Application;
use GDO\Core\GDT_Template;
use GDO\Language\GDO_Language;
use GDO\Language\GDT_Language;
use GDO\Country\GDT_Country;
use GDO\Mail\GDT_Email;
use GDO\Mail\GDT_EmailFormat;
use GDO\Net\GDT_IP;
use GDO\DB\GDT_UInt;
/**
 * The holy user object.
 * @author gizmore
 * @since 1.0
 * @version 6.0
 */
final class GDO_User extends GDO
{
    const SYSTEM_ID = '1'; # System user is always #1
    
    const BOT = 'bot';
    const GHOST = 'ghost';
    const GUEST = 'guest';
    const MEMBER = 'member';
    
    ###########
    ### GDO ###
    ###########
    public function gdoColumns()
    {
    	return array(
            GDT_AutoInc::make('user_id'),
            GDT_Enum::make('user_type')->enumValues(self::GHOST, self::BOT, self::GUEST, self::MEMBER)->label('type')->notNull()->initial(self::GUEST),
            GDT_Username::make('user_name')->unique(),
            GDT_Username::make('user_guest_name')->unique()->label('guestname'),
            GDT_Realname::make('user_real_name'),
            GDT_Email::make('user_email'),
            GDT_UInt::make('user_level')->notNull()->initial('0')->label('level'),
            GDT_UInt::make('user_credits')->notNull()->initial('0')->label('credits'),
            GDT_EmailFormat::make('user_email_fmt')->notNull()->initial(GDT_EmailFormat::HTML),
            GDT_Gender::make('user_gender'),
            GDT_Date::make('user_birthdate')->label('birthdate'),
            GDT_Country::make('user_country'),
            GDT_Language::make('user_language')->notNull()->initial('en'),
            GDT_Password::make('user_password'),
            GDT_DeletedAt::make('user_deleted_at'),
            GDT_CreatedAt::make('user_last_activity'),
            GDT_CreatedAt::make('user_register_time')->label('registered_at'),
            GDT_IP::make('user_register_ip'),
        );
    }
    
    ##############
    ### Getter ###
    ##############
    public function getID() { return $this->getVar('user_id'); }
    public function getType() { return $this->getVar('user_type'); }
    
    public function getName() { return $this->getVar('user_name'); }
    public function getUserName() { return ($name = $this->getGuestName()) ? $name : $this->getName(); }
    public function getRealName() { return $this->getVar('user_real_name'); }
    public function getGuestName() { return $this->getVar('user_guest_name'); }
    
    public function isBot() { return $this->getType() === self::BOT; }
    public function isGhost() { return $this->getType() === self::GHOST; }
    public function isGuest() { return $this->getType() === self::GUEST; }
    public function isMember() { return $this->getType() === self::MEMBER; }
    
    public function getLevel() { return $this->getVar('user_level'); }
    public function getCredits() { return $this->getVar('user_credits'); }
    public function isAuthenticated() { return !$this->isGhost(); }
    
    public function hasMail() { return !!$this->getMail(); }
    public function getMail() { return $this->getVar('user_email'); }
    public function getMailFormat() { return $this->getVar('user_email_fmt'); }
    public function wantsTextMail() { return $this->getVar('user_email_fmt') === GDT_EmailFormat::TEXT; }
    
    public function getGender() { return $this->getVar('user_gender'); }
    public function getLangISO() { $iso = $this->getVar('user_language'); return $iso ? $iso : GWF_LANGUAGE; }
    public function getLanguage() { return GDO_Language::findById($this->getLangISO()); }
    public function getCountryISO() { return $this->getVar('user_country'); }
    public function getCountry() { return $this->getValue('user_country'); }
    public function getBirthdate() { return $this->getVar('user_birthdate'); }
    public function getAge() { return Time::getAge($this->getBirthdate()); }
    
    public function getRegisterDate() { return $this->getVar('user_register_time'); }
    public function getRegisterIP() { return $this->getVar('user_register_ip'); }
    public function isDeleted() { return $this->getVar('user_deleted_at') !== null; }
    
    ###############
    ### Display ###
    ###############
    public function displayName()
    {
    	return $this->getName();
    }
    
    public function displayNameLabel()
    {
        if ($realName = $this->getRealName())
        {
            return html("'$realName'");
        }
        elseif ($guestName = $this->getGuestName())
        {
            return "~$guestName~";
        }
        elseif ($userName = $this->getName())
        {
            return $userName;
        }
        else
        {
            return t('guest');
        }
    }
    
    public function renderCell()
    {
        return GDT_Template::php('User', 'cell/user.php', ['user'=>$this]);
    }
    
    public function renderChoice()
    {
        return $this->displayNameLabel();
    }
    
    #############
    ### HREFs ###
    #############
    public function href_edit_admin() { return href('Admin', 'UserEdit', "&id={$this->getID()}"); }
    public function href_perm_revoke() { return href('Admin', 'PermissionRevoke', "&user={$this->getID()}&perm=".$this->getVar('perm_perm_id')); }
    
    #############
    ### Perms ###
    #############
    public function loadPermissions()
    {
        if (null === ($cache = $this->tempGet('gdo_permission')))
        {
            $cache = GDO_UserPermission::load($this);
            $this->tempSet('gdo_permission', $cache);
            $this->recache();
        }
        return $cache;
    }
    public function hasPermissionID($permissionId) { return $this->hasPermissionObject(GDO_Permission::getById($permissionId)); }
    public function hasPermissionObject(GDO_Permission $permission) { return $this->hasPermission($permission->getName()); }
    public function hasPermission($permission) { return array_key_exists($permission, $this->loadPermissions()); }
    public function isAdmin() { return $this->hasPermission('admin'); }
    public function isStaff() { return $this->hasPermission('staff'); }
    public function changedPermissions() { $this->tempUnset('gdo_permission'); return $this->recache(); }
    
    ##############
    ### Static ###
    ##############
    /**
     * Get guest ghost user.
     * @return self
     */
    public static function ghost() { return self::table()->blank(['user_id' => '0', 'user_type' => 'ghost']); }
    
    /**
     * Get current user.
     * Not necisarilly via session!
     * @return self
     */
    public static function current() { return isset(self::$CURRENT) ? self::$CURRENT : GDO_Session::user(); }
    
    /**
     * @var self
     */
    public static $CURRENT;
    
    
    public function persistent()
    {
        if ($this->isGhost())
        {
            $this->setVar('user_type', self::GUEST);
            $this->insert();
        }
        return $this;
    }
    
    /**
     * @param string $name
     * @return self
     */
    public static function getByName($name) { return self::getBy('user_name', $name); }
    
    /**
     * @param string $login
     * @return self
     */
    public static function getByLogin($login)
    {
        return self::table()->select('*')->where(sprintf('user_name=%1$s OR user_email=%1$s', self::quoteS($login)))->first()->exec()->fetchObject();
    }
    
    /**
     * Get all admins
     * @return self[]
     */
    public static function admins()
    {
        return self::withPermission('admin');
    }
    
    /**
     * Get all users with a permission.
     * @param string $permission
     * @return self[]
     */
    public static function withPermission($permission)
    {
        return GDO_UserPermission::table()->select('gdo_user.*')->
        joinObject('perm_user_id')->joinObject('perm_perm_id')->
        where("perm_name=".self::quoteS($permission))->
        exec()->
        fetchAllObjectsAs(self::table());
    }
    
}
