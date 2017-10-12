<?php
namespace GDO\Table;
use GDO\Util\Common;
use GDO\DB\ArrayResult;
use GDO\Core\GDO;
use GDO\DB\Query;
use GDO\DB\Result;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\UI\WithHREF;
use GDO\UI\WithTitle;
use GDO\UI\WithActions;
/**
 * A sortable, orderable, filterable, paginatable collection of GDT[] in headers.
 * Supports queried and GDO\Core\ArrayResult.
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
class GDT_Table extends GDT
{
	use WithHREF;
	use WithTitle;
	use WithHeaders;
	use WithActions;
	
	################
	### Endpoint ###
	################
	public $action;
	public function __construct() { $this->action = $_SERVER['REQUEST_URI']; }
	public function action($action=null) { $this->action = $action; return $this; }

	######################
	### Drag&Drop sort ###
	######################
	private $sortable;
	private $sortableURL;
	public function sortable($sortableURL=null)
	{
		$this->sortable = $sortableURL !== null;
		$this->sortableURL = $sortableURL;
		return $this;
	}
	
	#################
	### Filtering ###
	#################
	public $filtered;
	public function filtered($filtered=true)
	{
		$this->filtered = $filtered;
		return $this;
	}
	
	################
	### Ordering ###
	################
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
	
	##################
	### Pagination ###
	##################
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

	####################
	### ItemsPerPage ###
	####################
	private $ipp = 10;
	public function ipp($ipp)
	{
		$this->ipp = $ipp;
		return $this;
	}
	
	###
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
			    $this->result = new ArrayResult([]);
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
		return $this->query->copy();
	}
	
	public function getFilteredQuery()
	{
		$query = $this->getQuery();
		if ($this->filtered)
		{
			foreach ($this->getHeaderFields() as $gdoType)
			{
				$gdoType->filterQuery($query);
			}
		}
		return $query;
	}
	
	public function getHeaderFields()
	{
	    return $this->headers->getFields();
	}
	
	/**
	 * @var int
	 */
	private $countItems = null;
	/**
	 * @return int the total number of matching rows. 
	 */
	public function countItems()
	{
		if ($this->countItems === null)
		{
			$this->countItems = $this->query ? 
				$this->getFilteredQuery()->select('COUNT(*)')->exec()->fetchValue() :
				$this->getResult()->numRows();
		}
		return $this->countItems;
	}
	
	/**
	 * Query the final result.
	 * @return \GDO\DB\Result
	 */
	public function queryResult()
	{
		$query = $this->query;
	    $headers = $this->headers;
		if ($this->filtered)
		{
			foreach ($headers->fields as $gdoType)
			{
				$gdoType->filterQuery($query);
			}
		}
		if ($this->ordered)
		{
		    $hasCustomOrder = false;
		    foreach (Common::getRequestArray($headers->name) as $name => $asc)
			{
			    if ($field = $headers->getField($name))
				{
					if ($field->orderable)
					{
						$query->order($field->orderFieldName(), !!$asc);
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
	
	##############
	### Render ###
	##############
	public function renderCell()
	{
	    return GDT_Template::php('Table', 'cell/table.php', ['field'=>$this]);
	}
	
	public function renderJSON()
	{
		return array(
			'tableName' => $this->result->table->gdoClassName(),
			'pagemenu' => $this->pagemenu ? $this->getPageMenu()->initJSON() : null,
			'sortable' => $this->sortable,
			'sortableURL' => $this->sortableURL,
		);
	}
}
