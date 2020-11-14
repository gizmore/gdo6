<?php
namespace GDO\User;
use GDO\DB\GDT_Object;
use GDO\DB\Query;
use GDO\Core\GDO;
use GDO\Util\Strings;
/**
 * An autocomplete enabled user field.
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
class GDT_User extends GDT_Object
{
	public function defaultLabel() { return $this->label('user'); }
	
	public function __construct()
	{
		$this->orderField = 'user_name';
		$this->table(GDO_User::table());
		$this->withCompletion();
		$this->icon('face');
	}

	public function withCompletion()
	{
		return $this->completionHref(href('User', 'Completion'));
	}
	
	private $ghost = false;
	public function ghost($ghost=true)
	{
		$this->ghost = $ghost;
		return $this;
	}
	
	/**
	 * @return GDO_User
	 */
	public function getUser()
	{
		if (!($user = $this->getValue()))
		{
			if ($this->ghost)
			{
				$user = GDO_User::ghost();
			}
		}
		return $user;
	}
	
	public function displayVar()
	{
		if ($gdo = $this->getUser())
		{
			return $gdo->displayNameLabel();
		}
	}
	
	public function findByName($name)
	{
		if (Strings::startsWith($name, GDO_User::GHOST_NAME_PREFIX))
		{
			return null;
		}
		elseif (Strings::startsWith($name, GDO_User::REAL_NAME_PREFIX))
		{
			return GDO_User::table()->findBy('user_real_name', trim($name, GDO_User::REAL_NAME_PREFIX.GDO_User::REAL_NAME_POSTFIX));
		}
		elseif (Strings::startsWith($name, GDO_User::GUEST_NAME_PREFIX))
		{
			return GDO_User::table()->findBy('user_guest_name', trim($name, GDO_User::GUEST_NAME_PREFIX));
		}
		else
		{
			return GDO_User::getByName($name);
		}
	}
	
	public function renderCell()
	{
		if ($user = $this->getUser())
		{
			return $user->displayName();
		}
		return t('unknown');
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
				$this->filterQueryCondition($query, "user_name $filter OR user_guest_name $filter OR user_real_name $filter");
			}
		}
	}
	
}
