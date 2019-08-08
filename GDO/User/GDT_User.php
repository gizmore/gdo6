<?php
namespace GDO\User;
use GDO\DB\GDT_Object;
use GDO\DB\Query;
use GDO\Core\GDO;
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
	
	public function findByName($name)
	{
		return GDO_User::getByName($name);
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
	
	public function filterQuery(Query $query)
	{
		if (!$this->noFilter)
		{
			if ($filter = $this->filterValue())
			{
				$filter = GDO::escapeS($filter);
				$this->filterQueryCondition($query, "user_name LIKE '%$filter%'");
			}
		}
	}

}
