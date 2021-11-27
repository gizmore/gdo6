<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_CreatedAt;
use GDO\DB\GDT_DeletedAt;
use GDO\Date\Time;
use GDO\DB\GDT_Enum;
use GDO\Core\GDT_Template;
use GDO\Language\GDO_Language;
use GDO\Language\GDT_Language;
use GDO\Country\GDT_Country;
use GDO\Mail\GDT_Email;
use GDO\Mail\GDT_EmailFormat;
use GDO\Net\GDT_IP;
use GDO\DB\GDT_UInt;
use GDO\Date\GDT_Timezone;
use GDO\Avatar\GDT_Avatar;
use GDO\DB\Cache;
use GDO\Country\GDO_Country;
use GDO\DB\GDT_Index;
use GDO\Session\GDO_Session;

/**
 * The holy user object.
 * I really like users that use my stuff, e.g: www.wechall.net
 * 
 * @author gizmore
 * @link https://www.wechall.net
 * @link https://mettwitze.gizmore.org
 * @version 6.11.0
 * @since 1.0.0
 */
final class GDO_User extends GDO
{
    ### User type notation prefixes
    const REAL_NAME_PREFIX = '´';
    const REAL_NAME_POSTFIX = '`';
    const GUEST_NAME_PREFIX = '~';
	const GHOST_NAME_PREFIX = '~~';
			
    ### User types
	const BOT = 'bot';
	const SYSTEM = 'system';
	const GHOST = 'ghost';
	const GUEST = 'guest';
	const MEMBER = 'member';
	
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return [
			GDT_AutoInc::make('user_id'),
			GDT_Username::make('user_name')->unique(),
			GDT_Enum::make('user_type')->enumValues(self::SYSTEM, self::GHOST, self::BOT, self::GUEST, self::MEMBER)->label('type')->notNull()->initial(self::GUEST),
			GDT_Username::make('user_guest_name')->unique()->label('guestname'),
			GDT_Realname::make('user_real_name'),
			GDT_Email::make('user_email')->searchable(false)->orderable(false),
			GDT_Level::make('user_level'),
			GDT_UInt::make('user_credits')->notNull()->initial('0')->label('credits')->icon('money')->searchable(false),
			GDT_Gender::make('user_gender'),
			GDT_Country::make('user_country'),
			GDT_Language::make('user_language')->notNull()->initial(GDO_LANGUAGE)->searchable(false)->cascadeRestrict(),
			GDT_Password::make('user_password'),
		    GDT_DeletedAt::make('user_deleted_at'),
		    GDT_CreatedAt::make('user_last_activity')->label('last_activity')->format('short'),
			GDT_CreatedAt::make('user_register_time')->label('registered_at')->format('short'),
		    GDT_Timezone::make('user_timezone')->notNull()->initial('1')->cascadeRestrict(),
			GDT_IP::make('user_register_ip')->useCurrent(),
		    # Indexes
		    GDT_Index::make()->indexColumns('user_last_activity'),
		    GDT_Index::make()->indexColumns('user_type'),
		];
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
	
	public function isBot() { return $this->isType(self::BOT); }
	public function isGhost() { return $this->isType(self::GHOST); }
	public function isAnon() { return $this->isGuest() && (!$this->getGuestName()); }
	
	public function isGuest($andAuthenticated=true)
	{
	    $a = $andAuthenticated;
	    return $this->isType(self::GUEST) ? 
	       ($a ? (!!$this->getGuestName()) : true) : # guest type checks for auth or true
	       false; # non guest
	}
	
	public function isMember() { return $this->isType(self::MEMBER); }
	public function isType($type) { return $this->getType() === $type; }
	
	/**
	 * Check if it is a legit user.
	 * Either a guest with name or a member.
	 * @return boolean
	 */
	public function isUser()
	{
	    switch ($this->getType())
	    {
	        case self::MEMBER:
	            return true;
	        case self::GUEST:
        	    return !!$this->getGuestName();
	        default:
	            return false;
	    }
	}
	
	public function getCredits() { return $this->getVar('user_credits'); }
	public function isAuthenticated() { return $this->isUser(); }
	
	public function hasMail() { return !!$this->getMail(); }
	public function getMail() { return $this->getVar('user_email'); }
	public function getMailFormat() { return $this->getVar('user_email_fmt'); }
	public function wantsTextMail() { return $this->getVar('user_email_fmt') === GDT_EmailFormat::TEXT; }
	
	public function getGender() { return $this->getVar('user_gender'); }
	public function getLangISO() { $iso = $this->getVar('user_language'); return $iso ? $iso : GDO_LANGUAGE; }
	public function getLanguage() { return GDO_Language::findById($this->getLangISO()); }
	public function getCountryISO() { return $this->getVar('user_country'); }
	public function getCountry() { $c = $this->getValue('user_country'); return $c ? $c : GDO_Country::unknownCountry(); }
	public function hasTimezone() { return $this->getVar('user_timezone') != 1; }
	public function getTimezone() { return $this->hasTimezone() ? $this->getVar('user_timezone') : GDO_Session::get('timezone', 1); }
	public function getAge() { return Time::getAge($this->getBirthdate()); }
	public function displayAge() { return Time::displayAge($this->getBirthdate()); }
	
	public function getRegisterDate() { return $this->getVar('user_register_time'); }
	public function displayRegisterAge() { return Time::displayAge($this->getRegisterDate()); }
	public function displayRegisterDate() { return Time::displayDate($this->getRegisterDate()); }
	public function getRegisterIP() { return $this->getVar('user_register_ip'); }
	public function isDeleted() { return $this->getVar('user_deleted_at') !== null; }
	
	################
	### Timezone ###
	################
	/**
	 * Get the appropiate timezone object for this user.
	 * @return \DateTimeZone
	 */
	public function getTimezoneObject()
	{
	    return Time::getTimezoneObject($this->getTimezone());
	}
	
	###############
	### Display ###
	###############
	public function displayName()
	{
		return $this->displayNameLabel();
	}
	
	public function displayType()
	{
		return t('enum_' . $this->getType());
	}
	
	public function displayNameLabel()
	{
		if ($realName = $this->getRealName())
		{
			return "´{$realName}`";
		}
		elseif ($guestName = $this->getGuestName())
		{
			return "~$guestName~";
		}
		elseif ($userName = $this->getName())
		{
			return $userName;
		}
		elseif ($this->isGuest())
		{
			return '~~' . t('guest')  . '~~';
		}
		else
		{
			return '~~' . t('ghost') . '~~';
		}
	}
	
	#############
	### HREFs ###
	#############
	public function href_edit_admin() { return href('Admin', 'UserEdit', "&user={$this->getID()}"); }
	public function href_perm_revoke() { return href('Admin', 'PermissionRevoke', "&user={$this->getID()}&perm=".$this->getVar('perm_perm_id')); }
	
	#############
	### Perms ###
	#############
	public function loadPermissions()
	{
		if ($this->isPersisted())
		{
			if (null === ($cache = $this->tempGet('gdo_permission')))
			{
				if ($cache = GDO_UserPermission::load($this))
				{
					$this->tempSet('gdo_permission', $cache);
					$this->recache();
				}
			}
			return $cache;
		}
		return [];
	}
	public function hasPermissionID($permissionId)
	{
	    if ($permissionId)
	    {
    	    $permission = GDO_Permission::findById($permissionId);
    	    return $this->hasPermissionObject($permission);
	    }
	    return true;
	}
	public function hasPermissionObject(GDO_Permission $permission) { return $this->hasPermission($permission->getName()); }
	public function hasPermission($permission) { return array_key_exists($permission, $this->loadPermissions()); }
	public function isAdmin() { return $this->hasPermission('admin'); }
	public function isStaff() { return $this->hasPermission('staff') || $this->hasPermission('admin'); }
	public function changedPermissions()
	{
	    $this->tempUnset('gdo_permission');
	    return $this->recache();
	}
	
	public function getLevel()
	{
	    $level = $this->getVar('user_level');
	    $permLevel = $this->getPermissionLevel();
	    return (int)max([$level, $permLevel]);
	}
	
	public function getPermissionLevel()
	{
	    $max = 0;
	    if ($perms = $this->loadPermissions())
	    {
		    foreach ($perms as $level)
		    {
		        if ($level > $max)
		        {
		            $max = $level;
		        }
		    }
	    }
	    return $max;
	}
	
	##############
	### Static ###
	##############
	/**
	 * Get guest ghost user.
	 * @return self
	 */
	public static function ghost()
	{
	    return self::blank(['user_id' => '0', 'user_type' => 'ghost']);
	}
	
	private static $SYSTEM;
	public function isSystem() { return $this->getID() === '1'; }
	public static function system()
	{
	    if (!self::$SYSTEM)
	    {
	        self::$SYSTEM = self::findById('1');
// 	        if (!(self::$SYSTEM = self::findById('1')))
// 	        {
// 	            self::$SYSTEM = self::blank([
// 	                'user_id' => '1',
// 	                'user_type' => 'system',
// 	            ])->replace();
// 	        }
	    }
        return self::$SYSTEM;
	}
	
	/**
	 * Get current user.
	 * Not neccisarilly via session!
	 * @return self
	 */
	public static function current() { return self::$CURRENT; }
	
	public static function setCurrent(GDO_User $user)
	{
	    self::$CURRENT = $user;
	    return $user;
	}

	/**
	 * @var GDO_User
	 */
	private static $CURRENT;
	
	/**
	 * @return GDO_User
	 */
	public function persistent()
	{
	    if ($session = GDO_Session::instance())
	    {
    		if ($this->isGhost())
    		{
    			$this->setVar('user_type', self::GUEST);
    			$this->insert();
    			$session->setVar('sess_user', $this->getID());
    		}
	    }
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return self
	 */
	public static function getByName($name)
	{
	    if ( ($user = self::getBy('user_name', $name)) ||
	         ($user = self::getBy('user_guest_name', trim($name, self::GUEST_NAME_PREFIX))) )
	    {
	        return $user;
	    }
	}
	
	/**
	 * @param string $login
	 * @return self
	 */
	public static function getByLogin($login)
	{
		return self::table()->select()->where(sprintf('user_name=%1$s OR user_email=%1$s', self::quoteS($login)))->first()->exec()->fetchObject();
	}
	
	#######################
	### With Permission ###
	#######################
	/**
	 * Get all admins
	 * @return self[]
	 */
	public static function admins()
	{
		return self::withPermission('admin');
	}
	
	/**
	 * Get all staff members.
	 * @return self[]
	 */
	public static function staff()
	{
		return self::withPermission('staff');
	}
	
	/**
	 * Get all users with a permission.
	 * @param string $permission
	 * @return self[]
	 */
	public static function withPermission($permission)
	{
	    # @TODO This triggers a bug when all admin users are reloaded they poison the cache with outdated data. 
	    #       The reason is not very known, as even ->uncache() does not help.
	    #       On uncache results, cache shall flow in the different direction.
	    #       The loaded cache data shall be updated with current cache? not sure if that's the bug.
// 	    $key = "all-{$permission}-users";
// 	    if (false === ($cache = Cache::get($key)))
// 	    {
	        $query = GDO_UserPermission::table()->select('gdo_user.*');
	        $query->joinObject('perm_user_id')->joinObject('perm_perm_id');
    	    $query->where("perm_name=".self::quoteS($permission));
    	    $query->uncached();
    	    $result = $query->exec();
    	    $cache = $result->fetchAllObjectsAs(self::table());
// 	        Cache::set($key, $cache);
// 	    }
	    return $cache;
	}
	
	##############
	### Render ###
	##############
	public function renderList()
	{
	    return GDT_Template::php('User', 'list/list_user.php', ['user' => $this]);
	}
	
	public function renderCell()
	{
	    return GDT_Template::php('User', 'cell/user.php', ['user'=>$this]);
	}
	
	public function renderChoice()
	{
	    $pre = '';
	    if (module_enabled('Avatar'))
	    {
	        # ugly switch to html output for the avatar.
	        $old = @$_REQUEST['_fmt'];
	        $_REQUEST['_fmt'] = 'html';
	        $pre = GDT_Avatar::make()->user($this)->addClass('fl')->renderCell(); # html avatar
	        if (!$old)
	        {
	            unset($_REQUEST['_fmt']);
	        }
	        else
	        {
	            $_REQUEST['_fmt'] = $old;
	        }
	    }
	    return $pre . $this->displayNameLabel();
	}
	
	public function renderJSON()
	{
		return [
			'user_id' => (int)$this->getID(),
			'user_name' => $this->getName(),
			'user_real_name' => $this->getRealName(),
			'user_guest_name' => $this->getGuestName(),
			'user_email' => $this->getMail(),
			'user_gender' => $this->getGender(),
			'user_type' => $this->getType(),
			'user_level' => (int)$this->getLevel(),
			'user_credits' => (int)$this->getCredits(),
			'user_email_fmt' => $this->getMailFormat(),
			'user_language' => $this->getLangISO(),
			'user_country' => $this->getCountryISO(),
		    'user_timezone' => $this->getTimezone(),
			'permissions' => $this->loadPermissions(),
		];
	}
	
	#############
	### Count ###
	#############
	private static $MEMBERCOUNT = null;
	public static function getMemberCount()
	{
	    if (self::$MEMBERCOUNT === null)
	    {
	        $key = "gdo_user_membercount";
	        if (false === (self::$MEMBERCOUNT = Cache::get($key)))
	        {
	            self::$MEMBERCOUNT = self::table()->countWhere('user_type="member"');
	            Cache::set($key, self::$MEMBERCOUNT);
	        }
	    }
	    return self::$MEMBERCOUNT;
	}
	
}

# Initial valid state
GDO_User::setCurrent(GDO_User::ghost());
