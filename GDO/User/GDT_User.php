<?php
namespace GDO\User;

use GDO\DB\GDT_Object;
use GDO\DB\Query;
use GDO\Core\GDO;

/**
 * An autocomplete enabled user field.
 * 
 * Settings:
 * - ghost(): fallback to ghost user for null
 * - fallbackCurrentUser(): fallback to current user for null
 * - withPermission(): only allow users with a certain permission
 * - withType(): only allow users of a certain type
 * 
 * @TODO: rename fallbackCurrentUser()
 * 
 * @author gizmore
 * @version 6.11.1
 * @since 6.0.0
 */
class GDT_User extends GDT_Object
{
	public function defaultLabel() { return $this->label('user'); }
	
	protected function __construct()
	{
	    parent::__construct();
		$this->orderField = 'user_name';
		$this->table(GDO_User::table());
		$this->icon('face');
        $this->withCompletion();
	}

	public function withCompletion()
	{
		return $this->completionHref(href('User', 'Completion', '&_fmt=json'));
	}
	
	#############
	### Ghost ###
	#############
	private $ghost = false;
	public function ghost($ghost=true)
	{
		$this->ghost = $ghost;
		return $this;
	}
	
	###############
	### Current ###
	###############
	public $fallbackCurrentUser = false;
	public function fallbackCurrentUser($fallbackCurrentUser=true)
	{
	    $this->fallbackCurrentUser = $fallbackCurrentUser;
	    return $this;
	}
	
	############
	### Type ###
	############
	public $withType;
	public function withType($withType)
	{
	    $this->withType = $withType;
	    return $this;
	}
	
	############
	### Perm ###
	############
	public $withPermission;
	public function withPermission($withPermission)
	{
	    $this->withPermission = $withPermission;
	    return $this;
	}
	
	#############
	### Value ###
	#############
	/**
	 * Get selected user.
	 * @return GDO_User
	 */
	public function getUser() { return $this->getValue(); }
	
	/**
	 * @return GDO_User
	 */
	public function getValue()
	{
		if ($user = parent::getValue())
		{
		    return $user;
		}
		if ($this->fallbackCurrentUser)
		{
		    return GDO_User::current();
		}
		if ($this->ghost)
		{
			return GDO_User::ghost();
		}
	}
	
	public function displayVar($var)
	{
		if ($gdo = $this->toValue($var))
		{
			return $gdo->displayNameLabel();
		}
	}
	
	public function findByName($name)
	{
		if (str_starts_with($name, GDO_User::GHOST_NAME_PREFIX))
		{
			return null;
		}
		elseif (str_starts_with($name, GDO_User::REAL_NAME_PREFIX))
		{
			return GDO_User::table()->findBy('user_real_name', trim($name, GDO_User::REAL_NAME_PREFIX.GDO_User::REAL_NAME_POSTFIX));
		}
		elseif (str_starts_with($name, GDO_User::GUEST_NAME_PREFIX))
		{
			return GDO_User::table()->findBy('user_guest_name', trim($name, GDO_User::GUEST_NAME_PREFIX));
		}
		else
		{
			return GDO_User::getByName($name);
		}
	}
	
	################
	### Validate ###
	################
	/**
	 * {@inheritDoc}
	 * @see \GDO\DB\GDT_Int::validate()
	 */
	public function validate($value)
	{
	    /** @var $user GDO_User **/
	    $user = $value;
	    
	    if (!parent::validate($value))
	    {
	        return false; # $this->error('err_user');
	    }
	    
	    if ($value === null)
	    {
	        return true; # Null check passed already
	    }
	    
	    if ($this->withType)
	    {
	        if ($user->getType() !== $this->withType)
	        {
	            $typelabel = t('enum_' . $this->withType);
	            return $this->error('err_user_type', [$typelabel]);
	        }
	    }
	    
	    if ($this->withPermission)
	    {
	        if (!$user->hasPermission($this->withPermission))
	        {
	            $permlabel = t('perm_' . $this->withPermission);
	            return $this->error('err_user_no_permission', [$permlabel]);
	        }
	    }
	    
	    return true;
	}
	
	public function plugVar()
	{
	    return '2'; # gizmore in unit tests.
	}
	
	##############
	### Render ###
	##############
	public function renderCell()
	{
	    if ($user = $this->getUser())
	    {
	        return $user->displayNameLabel();
	    }
	    return t('unknown');
	}
	
	public function renderJSON()
	{
	    return $this->renderCell();
	}
	
	##############
	### Filter ###
	##############
	public $noFilter = false;
	public function noFilter($noFilter=true)
	{
		$this->noFilter = $noFilter;
		return $this;
	}
	
	public function filterQuery(Query $query, $rq=null)
	{
		if (!$this->noFilter)
		{
			if ($filter = $this->filterVar($rq))
			{
				$filter = GDO::escapeSearchS($filter);
				$filter = "LIKE '%{$filter}%'";
				$this->filterQueryCondition($query,
				    "user_name $filter OR user_guest_name $filter OR user_real_name $filter");
			}
		}
	}
	
}
