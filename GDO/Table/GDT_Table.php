<?php
namespace GDO\Table;

use GDO\Util\Common;
use GDO\DB\ArrayResult;
use GDO\Core\GDO;
use GDO\DB\Query;
use GDO\DB\Result;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_SearchField;
use GDO\UI\WithHREF;
use GDO\UI\WithTitle;
use GDO\UI\WithActions;
use GDO\Core\WithFields;
use GDO\Util\Classes;

/**
 * A filterable, searchable, orderable, paginatable, sortable collection of GDT[] in headers.
 * 
 * WithHeaders GDT control provide the filterable, searchable and orderable.
 * GDT_Pagemenu is used for paginatable.
 * gdoT
 * 
 * Supports queried Ressult and GDO\Core\ArrayResult.
 * Searchable can crawl multiple fields at once.
 * Filterable can only crawl on field.
 * Orderable enables sorting.
 * The table has a GDT_Pagemenu if desired.
 * A GDO with a GDT_Sort
 * 
 * The GDT that are used for this are stored via 'WithHeaders' trait.
 * The Header has a name that is used in $REQUEST vars.
 * $_REQUEST[$headerName][f] for filtering
 * $_REQUEST[$headerName][o][field]=1|0 for multordering in tables
 * $_REQUEST[$headerName][order_by] for single ordering in lists 
 * $_REQUEST[$headerName][search] for searching
 * $_REQUEST[$headerName][page] for pagenum
 * $_REQUEST[$headerName][ipp] for items per page
 * $_REQUEST[$headerName][s][ID]=[ID] for sorting (planned)
 * 
 * @author gizmore
 * 
 * @version 6.10
 * @since 6.00
 * 
 * @see GDO
 * @see GDT
 * @see GDT_PageMenu
 * @see Result
 * @see ArrayResult
 */
class GDT_Table extends GDT
{
	use WithHREF;
	use WithTitle;
	use WithHeaders;
	use WithActions;
	use WithFields;
	
	### 
	public function getHeaderFields()
	{
	    return $this->headers ? $this->headers->getFields() : [];
	}
	
	public function getHeaderField($name)
	{
	    return $this->headers->getField($name);
	}
	
	################
	### Endpoint ###
	################
	public $action;
	protected function __construct() { $this->action = @$_SERVER['REQUEST_URI']; $this->makeHeaders(); }
	public function action($action=null) { $this->action = $action; return $this; }

	##############
	### Footer ###
	##############
	public $footer;
	public function footer($footer) { $this->footer = $footer; return $this; }
	
	##################
	### Hide empty ###
	##################
	public $hideEmpty = false;
	public function hideEmpty($hideEmpty=true)
	{
	    $this->hideEmpty = $hideEmpty;
	    return $this;
	}
	
	####################### 
	### Default headers ###
	#######################
	public function setupHeaders($searched=false, $paginated=false, $ordered=false, $filtered=false, $sorted=false)
	{
	    if ($searched)
	    {
	        $this->addHeader(GDT_SearchField::make('search'));
	    }
	    
	    if ($paginated)
	    {
	        $this->addHeader(GDT_PageNum::make('page'));
	        $this->addHeader(GDT_IPP::make('ipp'));
	    }
	}
	
	######################
	### Drag&Drop sort ###
	######################
	public $sorted;
	private $sortableURL;
	public function sorted($sortableURL=null)
	{
		$this->sorted = $sortableURL !== null;
		$this->sortableURL = $sortableURL;
		return $this;
	}
	
	#################
	### Searching ###
	#################
	public $searched;
	public function searched($searched=true) { $this->searched =$searched; return $this; }
	
	#################
	### Filtering ###
	#################
	public $filtered;
	public function filtered($filtered=true) { $this->filtered = $filtered; return $this; }
	
	################
	### Ordering ###
	################
	public $ordered;
	public $orderDefault;
	public $orderDefaultAsc;
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
	/** @var $pagemnu GDT_PageMenu **/
	public $pagemenu;
	public function paginateDefault($href=null)
	{
		return $this->paginated(true, $href, Module_Table::instance()->cfgItemsPerPage());
	}
	public function paginated($paginated=true, $href=null, $ipp=0)
	{
		$ipp = $ipp <= 0 ? Module_Table::instance()->cfgItemsPerPage() : (int)$ipp;
		if ($paginated)
		{
		    $href = $href === null ? @$_SERVER['REQUEST_URI'] : $href;
			$this->pagemenu = GDT_PageMenu::make('page');
			$this->pagemenu->headers($this->headers);
			$this->pagemenu->href($href);
			$this->pagemenu->ipp($ipp);
			$o = $this->headers->name;
			$this->pagemenu->page($this->headers->getField('page')->getRequestVar("$o", '1', 'page'));
// 			$this->pagemenu->items($this->getResult()->numRows());
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
				$this->result = new ArrayResult([], $this->gdtTable);
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
		$this->query = $this->getFilteredQuery($query);
		return $this;
	}
	
	public $countQuery;
	public function countQuery(Query $query)
	{
	    $this->countQuery = $this->getFilteredQuery($query);
	    return $this;
	}
	
	public function getFilteredQuery(Query $query)
	{
		if ($this->filtered)
		{
		    $rq = $this->headers->name;
		    foreach ($this->getHeaderFields() as $gdoType)
		    {
		        if ($gdoType->filterable)
		        {
		            $gdoType->filterQuery($query, $rq);
		        }
		    }
		}
		
		if ($this->searched)
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
    	    if ($where)
    	    {
    	        $query->where(implode(' OR ', $where));
    	    }
    	    $first = false;
	    }
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
			    $this->countQuery->exec()->fetchValue() :
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
		$o = $headers ? $headers->name : 'o';
		
		if ($this->ordered)
		{
			# Convert single to multiple fake
		    if (isset($_REQUEST[$o]['order_by']))
		    {
                unset($_REQUEST[$o]['o']);
	            $by = $_REQUEST[$o]['order_by'];
	            $_REQUEST[$o]['o'][$by] = $_REQUEST[$o]['order_dir'] === 'ASC';
// 	            unset($_REQUEST[$o]['order_by']);
// 	            unset($_REQUEST[$o]['order_dir']);
		    }
		    
		    $hasCustomOrder = false;
		    
		    if ($this->headers)
		    {
		        if ($cols = Common::getRequestArray($o))
		        {
		            if ($cols = @$cols['o'])
		            {
		                $o = '1';
            			foreach ($cols as $name => $asc)
            			{
            				if ($field = $headers->getField($name))
            				{
            					if ($field->orderable)
            					{
            						if ( (Classes::class_uses_trait($field, 'GDO\\DB\\WithObject')) &&
            						     ($field->orderFieldName() !== $field->name) )
            						{
           						        $query->joinObject($field->name, 'JOIN', "o{$o}");
           						        $query->order("o{$o}.".$field->orderFieldName(), !!$asc);
            						}
            						else
            						{
            						    $query->order($field->orderFieldName(), !!$asc);
            						}
            						$hasCustomOrder = true;
            					}
            				}
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
		    $this->getPageMenu();
			$this->pagemenu->filterQuery($query);
		}
		
		return $this->query->exec();
	}
	
	/**
	 * @return GDT_PageMenu
	 */
	public function getPageMenu()
	{
		if ($this->pagemenu)
		{
		    if ($this->query)
		    {
		        if (!$this->countItems)
		        {
        			$this->pagemenu->items($this->countItems());
		        }
		    }
// 			$this->pagemenu->href($this->href);
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
		return [
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
		];
	}
	
	private function renderJSONData()
	{
		$data = [];
		$result = $this->getResult();
		$table = $result->table;
		while ($gdo = $table->fetch($result))
		{
		    $dat = [];
		    foreach ($gdo->gdoColumnsCache() as $gdt)
		    {
		        if ($json = $gdt->gdo($gdo)->renderJSON())
		        {
		            foreach ($json as $k => $v)
		            {
    		            $dat[$k] = $v;
		            }
		        }
		    }
			$data[] = $dat;
		}
		return $data;
	}

}
