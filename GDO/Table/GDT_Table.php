<?php
namespace GDO\Table;
use GDO\UI\WithLabel;
use GDO\Util\Common;
use GDO\DB\ArrayResult;
use GDO\Core\GDO;
use GDO\DB\Query;
use GDO\DB\Result;
use GDO\Core\WithFields;
use GDO\Core\GDT;
use GDO\UI\GDT_Bar;
use GDO\Core\GDT_Template;
use GDO\UI\WithHREF;

class GDT_Table extends GDT
{
	use WithFields;
	use WithHREF;
	use WithLabel;
	
	public function __construct()
	{
	    $this->action = $_SERVER['REQUEST_URI'];
	}
	public $action;
	public function action($action=null) { $this->action = $action; return $this; }
	public function defaultLabel() { return $this->noLabel(); }

	
	private $sortable;
	private $sortableURL;
	public function sortable($sortableURL=null)
	{
		$this->sortable = $sortableURL !== null;
		$this->sortableURL = $sortableURL;
		return $this;
	}
	
	public $filtered;
	public function filtered($filtered=true)
	{
		$this->filtered = $filtered;
		return $this;
	}
	
	public $ordered;
	public $orderDefault;
	public $orderDefaultAsc = true;
	public function ordered($ordered=true, $defaultOrder=null, $defaultAsc = true)
	{
		$this->ordered = $ordered;
		$this->orderDefault = $defaultOrder;
		$this->orderDefaultAsc = $defaultAsc;
		return $this;
	}
	
	public $pagemenu;
	public function paginateDefault($href=null)
	{
	    return $this->paginate(true, $href, Module_Table::instance()->cfgItemsPerPage());
	}
	
	public function paginate($paginate=true, $href=null, $ipp=10)
	{
		if ($paginate)
		{
		    $href = $href === null ? $_SERVER['REQUEST_URI'] : $href;
		    $this->pagemenu = GDT_PageMenu::make($this->name.'_page');
			$this->pagemenu->href($href);
			$this->href($href);
		}
		return $this->ipp($ipp);
	}
	
	private $ipp = 10;
	public function ipp($ipp)
	{
		$this->ipp = $ipp;
		return $this;
	}
	
	public $result;
	public function result(Result $result)
	{
		if (!$this->fetchAs)
		{
			$this->fetchAs = $result->table;
		}
		$this->result = $result;
		return $this;
	}
	
	/**
	 * @return Result
	 */
	public function getResult()
	{
		if (!($this->result))
		{
			if (!($this->result = $this->queryResult()))
			{
				return new ArrayResult([]);
			}
		}
		return $this->result;
	}
		
	public $query;
	public function query(Query $query)
	{
		if (!$this->fetchAs)
		{
			$this->fetchAs = $query->table;
		}
		$this->query = $query;
		return $this;
	}
	
	public function getQuery()
	{
		return $this->query->clone();
	}
	
	public function getFilteredQuery()
	{
		$query = $this->getQuery();
		if ($this->filtered)
		{
			foreach ($this->getFields() as $gdoType)
			{
				$gdoType->filterQuery($query);
			}
		}
		return $query;
	}
	
	private $countItems = false;
	public function countItems()
	{
		if ($this->countItems === false)
		{
			$this->countItems = $this->query ? 
				$this->getFilteredQuery()->select('COUNT(*)')->exec()->fetchValue() :
				$this->getResult()->numRows();
		}
		return $this->countItems;
	}
	
	public function queryResult()
	{
		$query = $this->query;
		if ($this->filtered)
		{
			foreach ($this->getFields() as $gdoType)
			{
				$gdoType->filterQuery($query);
			}
		}
		if ($this->ordered)
		{
		    $hasCustomOrder = false;
			foreach (Common::getRequestArray('o') as $name => $asc)
			{
				if ($field = $this->getField($name))
				{
					if ($field->orderable)
					{
						$query->order($name, !!$asc);
						$hasCustomOrder = true;
					}
				}
			}
			if (!$hasCustomOrder)
			{
			    if ($this->orderDefault)
			    {
			        $ascdesc = $this->orderDefaultAsc ? 1 : 0;
// 			        $_REQUEST['o'] = [$this->orderDefault => $ascdesc];
// 			        $_SERVER['REQUEST_URI'] .= "&o[$this->orderDefault]=$ascdesc";
			        $query->order($this->orderDefault, $this->orderDefaultAsc);
			    }
			}
		}
		if ($this->pagemenu)
		{
			$this->pagemenu->filterQuery($query);
		}
		return $query->exec();
	}
	
	/**
	 * @return GDT_PageMenu
	 */
	public function getPageMenu()
	{
		if ($this->pagemenu)
		{
			$this->pagemenu->items($this->countItems());
			$this->pagemenu->href($this->href);
		}
		return $this->pagemenu;
	}
	
	public $fetchAs;
	public function fetchAs(GDO $fetchAs=null)
	{
		$this->fetchAs = $fetchAs;
		return $this;
	}
	
	###############
	### Actions ###
	###############
	private $actions;
	public function actions()
	{
		if (!$this->actions)
		{
		    $this->actions = GDT_Bar::make();
		}
		return $this->actions;
	}
	
	##############
	### Render ###
	##############
	public function renderCell()
	{
	    return GDT_Template::php('Table', 'cell/table.php', ['field'=>$this]);
	}
	
	public function initJSON()
	{
		return json_encode(array(
			'tableName' => $this->result->table->gdoClassName(),
			'pagemenu' => $this->pagemenu ? $this->getPageMenu()->initJSON() : null,
			'sortable' => $this->sortable,
			'sortableURL' => $this->sortableURL,
		));
	}
}
