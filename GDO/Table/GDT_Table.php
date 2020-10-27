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
use GDO\Core\WithFields;

/**
 * A sortable, orderable, filterable, paginatable collection of GDT[] in headers.
 * Supports queried Ressult and GDO\Core\ArrayResult.
 * Quicksearch can crawl multiple fields at once.
 * 
 * @author gizmore
 * 
 * @version 6.10
 * @since 6.00
 */
class GDT_Table extends GDT
{
	use WithHREF;
	use WithTitle;
	use WithHeaders;
	use WithActions;
	use WithFields;
	
	##############
	### Footer ###
	##############
	public $footer;
	public function footer($footer) { $this->footer = $footer; return $this; }
	
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
	
	##################
	### Hide empty ###
	##################
	public $hideEmpty = false;
	public function hideEmpty($hideEmpty=true)
	{
		$this->hideEmpty = $hideEmpty;
		return $this;
	}
	
	################
	### Ordering ###
	################
	public $ordered = false;
	public $orderDefault = null;
	public $orderDefaultAsc = true;
	public function ordered($ordered=true, $defaultOrder=null, $defaultAsc=true)
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
	public function paginate($paginate=true, $href=null, $ipp=0)
	{
		$ipp = $ipp <= 0 ? Module_Table::instance()->cfgItemsPerPage() : (int)$ipp;
		if ($paginate)
		{
		    $href = $href === null ? @$_SERVER['REQUEST_URI'] : $href;
			$this->pagemenu = GDT_PageMenu::make($this->name.'_page');
			$this->pagemenu->href($href);
			$this->pagemenu->ipp($ipp);
			$this->href($href);
			if ($this->result)
			{
				$this->countItems = count($this->result->fullData);
				$this->result->data = array_slice($this->result->fullData, $this->getPageMenu()->getFrom(), $ipp);
			}
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
	
	public $countQuery;
	public function countQuery(Query $query)
	{
	    $this->countQuery = $query;
	    return $this;
	}
	
	public function getQuery()
	{
		return $this->query;
	}
	
	public function getCountQuery()
	{
	    return $this->countQuery;
	}
	
	public function getFilteredQuery(Query $query)
	{
		if ($this->filtered)
		{
			foreach ($this->getHeaderFields() as $gdoType)
			{
			    if ($gdoType->filterable)
			    {
    				$gdoType->filterQuery($query);
			    }
			}
		}
		
		if ($this->searchable)
		{
		    $s = $this->headers->name;
		    if (isset($_REQUEST[$s]['search']))
		    {
		        if ($searchTerm = trim((string)@$_REQUEST[$s]['search'], "\r\n\t "))
		        {
		            $this->bigSearchQuery($query, $searchTerm);
		        }
		    }
		}
		
		return $query;
	}
	
	/**
	 * Build a huge where clause for quicksearch.
	 * Supports multiple terms at once, split via whitespaces.
	 * Objects that are searchable JOIN automatically and offer more searchable fields.
	 * In general, GDT_String and GDT_Int is searchable.
	 * 
	 * @todo GDT_Enum is not searchable yet.
	 * 
	 * @param Query $query
	 * @param string $searchTerm
	 */
	public function bigSearchQuery(Query $query, $searchTerm)
	{
	    $split = preg_split("/\\s+/iD", trim($searchTerm, "\t\r\n "));
        $first = true;
	    foreach ($split as $searchTerm)
	    {
    	    $where = [];
    	    foreach ($this->getHeaderFields() as $gdt)
    	    {
    	        if ($gdt->searchable)
    	        {
    	            if ($condition = $gdt->searchQuery($query, $searchTerm, $first))
    	            {
    	                $where[] = $condition;
    	            }
    	        }
    	    }
    	    $query->where(implode(' OR ', $where));
    	    $first = false;
	    }
	}
	
	public function getHeaderFields()
	{
		return $this->headers ? $this->headers->getFields() : [];
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
			$this->countItems = $this->countQuery ? 
				$this->getFilteredQuery($this->countQuery)->exec()->fetchValue() :
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
		$query = $this->getFilteredQuery($this->query);
		
		$headers = $this->headers;
		$o = $headers ? $headers->name : 's';
		$s = $o;
		
		if ($this->ordered)
		{
		    # Convert single to multiple fake
		    if (isset($_REQUEST[$s]['order_by']))
		    {
		        if (isset($_REQUEST[$s]['order_by']))
		        {
		            $by = $_REQUEST[$s]['order_by'];
		            $_REQUEST[$o][$by] = $_REQUEST[$s]['order_dir'] === 'ASC';
		            unset($_REQUEST[$s]['order_by']);
		            unset($_REQUEST[$s]['order_dir']);
		        }
		    }
		    
			$hasCustomOrder = false;

		    if ($this->headers)
		    {
    			foreach (Common::getRequestArray($o) as $name => $asc)
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
		    }
		    
			if (!$hasCustomOrder)
			{
				if ($this->orderDefault)
				{
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
		if ( ($this->hideEmpty) && ($this->getResult()->numRows() === 0) )
		{
			return '';
		}
		return GDT_Template::php('Table', 'cell/table.php', ['field'=>$this, 'form' => false]);
	}
	
	public function renderForm()
	{
		return GDT_Template::php('Table', 'cell/table.php', ['field'=>$this, 'form' => true]);
	}
	
	public function renderCard()
	{
		return $this->renderCell();
	}
	
	public function renderJSON()
	{
		return array_merge($this->configJSON(), ['data'=>$this->renderJSONData()]);
	}
	
	public function configJSON()
	{
		return array(
			'tableName' => $this->getResult()->table->gdoClassName(),
			'pagemenu' => $this->pagemenu ? $this->getPageMenu()->configJSON() : null,
		    'searchable' => $this->searchable,
			'sortable' => $this->sortable,
			'sortableURL' => $this->sortableURL,
		    'filtered' => $this->filtered,
		    'filterable' => $this->filterable,
		    'orderable' => $this->orderable,
		    'orderDefaultField' => $this->orderDefault,
		    'orderDefaultASC' => $this->orderDefaultAsc,
		);
	}
	
	private function renderJSONData()
	{
		$data = [];
		$result = $this->getResult();
		$table = $result->table;
		while ($gdo = $table->fetch($result))
		{
			$data[] = $gdo->getGDOVars();
		}
		return $data;
	}

}
