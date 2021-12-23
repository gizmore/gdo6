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
use GDO\Core\GDOException;

/**
 * A filterable, searchable, orderable, paginatable, sortable collection of GDT[] in headers.
 *
 * WithHeaders GDT control provide the filtered, searched, ordered, paginated and sorted.
 * GDT_Pagemenu is used for paginatable.
 *
 * Supports queried Result and ArrayResult.
 *
 * Searched can crawl multiple fields at once via huge query.
 * Filtered can crawl on individual fields.
 * Ordered enables ordering by fields.
 * Paginated enables pagination via GDT_Pagemenu.
 * Sorted enables drag and drop sorting via GDT_Sort and Table::Method::Sorting.
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
 * @version 6.10.3
 * @since 6.0.0
 *       
 * @see GDO
 * @see GDT
 * @see GDT_PageMenu
 * @see Result
 * @see ArrayResult
 * @see MethodQueryTable
 */
class GDT_Table extends GDT
{
	use WithHREF;
	use WithTitle;
	use WithHeaders;
	use WithActions;
	use WithFields;

	public function defaultName()
	{
		'table';
	}

	# ##########
	# ## GDT ###
	# ##########
	protected function __construct()
	{
		parent::__construct();
		$this->action = @$_SERVER['REQUEST_URI'];
		// $this->makeHeaders();
	}

	public function gdo(GDO $gdo = null)
	{
		$this->gdtTable = $gdo->table();
		return parent::gdo();
	}

	# ####################
	# ## Header fields ###
	# ####################
	public function getHeaderFields()
	{
		return $this->headers ? $this->headers->getFields() : [];
	}

	public function getHeaderField($name)
	{
		return $this->headers->getField($name);
	}

	# ###############
	# ## Endpoint ###
	# ###############
	public $action;

	public function action($action = null)
	{
		$this->action = $action;
		return $this;
	}

	# #############
	# ## Footer ###
	# #############
	public $footer;

	public function footer($footer)
	{
		$this->footer = $footer;
		return $this;
	}

	# #################
	# ## Hide empty ###
	# #################
	public $hideEmpty = false;

	public function hideEmpty($hideEmpty = true)
	{
		$this->hideEmpty = $hideEmpty;
		return $this;
	}

	# #################
	# ## Fetch Into ###
	# #################
	public $fetchInto = true;

	public function fetchInto($fetchInto)
	{
		$this->fetchInto = $fetchInto;
		return $this;
	}

	# ######################
	# ## Default headers ###
	# ######################
	public function setupHeaders($searched = false, $paginated = false)
	{
		# @TODO what about ordered and sorted and filtered?
		if ($searched)
		{
			$this->addHeader(GDT_SearchField::make('search'));
		}

		if ($paginated)
		{
			$o = $this->headers->name;
			$this->addHeader(GDT_PageNum::make('page')->table($this));
			$gdtIPP = GDT_IPP::make('ipp')->initial($this->getDefaultIPP());
			$this->addHeader($gdtIPP);
			$this->paginated(true, null,
			$gdtIPP->getRequestVar($o, $gdtIPP->var));
		}
	}

	public function getDefaultIPP()
	{
		return Module_Table::instance()->cfgItemsPerPage();
	}

	# #####################
	# ## Drag&Drop sort ###
	# #####################
	public $sorted;

	private $sortableURL;

	public function sorted($sortableURL = null)
	{
		$this->sorted = $sortableURL !== null;
		$this->sortableURL = $sortableURL;
		return $this;
	}

	# ################
	# ## Searching ###
	# ################
	public $searched;

	public function searched($searched = true)
	{
		$this->searched = $searched;
		return $this;
	}

	# ################
	# ## Filtering ###
	# ################
	public $filtered;

	public function filtered($filtered = true)
	{
		$this->filtered = $filtered;
		return $this;
	}

	# ###############
	# ## Ordering ###
	# ###############
	public $ordered;

	public $orderDefault;

	public function ordered($ordered = true, $defaultOrder = null)
	{
		$this->ordered = $ordered;
		$this->orderDefault = $defaultOrder;
		return $this;
	}

	# #################
	# ## Pagination ###
	# #################
	/** @var $pagemnu GDT_PageMenu **/
	public $pagemenu;

	public function paginateDefault($href = null)
	{
		return $this->paginated(true, $href,
		Module_Table::instance()->cfgItemsPerPage());
	}

	public function paginated($paginated = true, $href = null, $ipp = 0)
	{
		$ipp = $ipp <= 1 ? Module_Table::instance()->cfgItemsPerPage() : (int) $ipp;
		if ($paginated)
		{
			$href = $href === null ? $this->action : $href;
			$this->pagemenu = GDT_PageMenu::make('page');
			$this->pagemenu->headers($this->headers);
			$this->pagemenu->href($href);
			$this->pagemenu->ipp($ipp);
			$o = $this->headers->name;
			$this->pagemenu->page(
			$this->headers->getField('page')
				->getRequestVar("$o", '1', 'page'));
			$this->href($href);
		}
		return $this->ipp($ipp);
	}

	# ###################
	# ## ItemsPerPage ###
	# ###################
	private $ipp = 10;

	public function ipp($ipp)
	{
		$this->ipp = $ipp;
		return $this;
	}

	# ##
	public $result;

	public function result(Result $result)
	{
		if ( !$this->fetchAs)
		{
			$this->fetchAs = $result->table;
		}
		$this->result = $result;
		return $this;
	}

	/**
	 *
	 * @return Result
	 */
	public function getResult()
	{
		if (!$this->result)
		{
			$this->result = $this->queryResult();
		}
		return $this->result;
	}

	public $query;

	public function query(Query $query)
	{
		if ( !$this->fetchAs)
		{
			$this->fetchAs = $query->table;
		}
		$this->query = $this->getFilteredQuery($query);
		return $this;
	}

	public $countQuery;

	public function countQuery(Query $query)
	{
		$this->countQuery = $this->getFilteredQuery($query->copy());
		return $this;
	}

	private $filtersApplied = false;

	public function getFilteredQuery(Query $query)
	{
		if ($this->filtered)
		{
			$rq = $this->headers->name;
			foreach ($this->getHeaderFields() as $gdt)
			{
				if ($gdt->filterable)
				{
					$gdt->filterQuery($query, $rq);
				}
			}
		}

		if ($this->searched)
		{
			$s = $this->headers->name;
			if (isset($_REQUEST[$s]['search']))
			{
				if ($searchTerm = trim($_REQUEST[$s]['search'], "\r\n\t "))
				{
					$this->bigSearchQuery($query, $searchTerm);
				}
			}
		}

		return $this->getOrderedQuery($query);
	}

	private function getOrderedQuery(Query $query)
	{
		$headers = $this->headers;
		$o = $headers ? $headers->name : 'o';

		$hasCustomOrder = false;

		if ($this->ordered)
		{
			# Convert single to multiple fake
			if (isset($_REQUEST[$o]['order_by']))
			{
				unset($_REQUEST[$o]['o']);
				$by = $_REQUEST[$o]['order_by'];
				$_REQUEST[$o]['o'][$by] = $_REQUEST[$o]['order_dir'] === 'ASC';
				// unset($_REQUEST[$o]['order_by']);
				// unset($_REQUEST[$o]['order_dir']);
			}

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
									$asc = $asc ? ' ASC' : ' DESC';
									if ((Classes::class_uses_trait($field,
									'GDO\\DB\\WithObject')) &&
									($field->orderFieldName() !== $field->name))
									{
										$query->joinObject($field->name, 'JOIN',
										"o{$o}");
										$query->order(
										"o{$o}." . $field->orderFieldName() .
										$asc);
									}
									else
									{
										$query->order(
										$field->orderFieldName() . $asc);
									}
									$hasCustomOrder = true;
								}
							}
						}
					}
				}
			}
		}

		if ( !$hasCustomOrder)
		{
			if ($this->orderDefault)
			{
				$query->order($this->orderDefault, $this->orderDefaultAsc);
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
			foreach ($this->gdtTable->gdoColumnsCache() as $gdt)
			{
				if ($gdt->searchable)
				{
					if ($condition = $gdt->searchQuery($query, $searchTerm,
					$first))
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
	 *
	 * @var int
	 */
	public $countItems = null;

	/**
	 *
	 * @return int the total number of matching rows.
	 */
	public function countItems()
	{
		if ($this->countItems === null)
		{
			if ($this->countQuery)
			{
				$this->countItems = $this->countQuery->selectOnly('COUNT(*)')
					->noOrder()
					->noLimit()
					->first()
					->exec()
					->fetchValue();
			}
			else
			{
				$this->countItems = $this->getResult()->numRows();
			}
		}
		return $this->countItems;
	}

	/**
	 * Query the final result.
	 *
	 * @return \GDO\DB\Result
	 */
	public function queryResult()
	{
		return $this->query->exec();
	}

	/**
	 *
	 * @return GDT_PageMenu
	 */
	public function getPageMenu()
	{
		if ($this->pagemenu)
		{
			if ($this->query)
			{
				if ($this->countItems === null)
				{
					$this->pagemenu->items($this->countItems());
				}
			}
		}
		return $this->pagemenu;
	}

	public $fetchAs;

	public function fetchAs(GDO $fetchAs = null)
	{
		$this->fetchAs = $fetchAs;
		return $this;
	}

	# #############
	# ## Render ###
	# #############
	public function renderCell()
	{
		if (($this->hideEmpty) && ($this->getResult()->numRows() === 0))
		{
			return '';
		}
		return GDT_Template::php('Table', 'cell/table.php',
		[
			'field' => $this,
			'form' => false
		]);
	}

	public function renderForm()
	{
		return GDT_Template::php('Table', 'cell/table.php',
		[
			'field' => $this,
			'form' => true
		]);
	}

	public function renderCard()
	{
		return $this->renderCell();
	}

	public function renderJSON()
	{
		$json = array_merge($this->configJSON(),
		[
			'data' => $this->renderJSONData(),
		]);
		return $json;
	}

	public function configJSON()
	{
		return array_merge(parent::configJSON(),
		[
			'tableName' => $this->getResult()->table->gdoClassName(),
			'pagemenu' => $this->pagemenu ? $this->getPageMenu()->configJSON() : null,
			'total' => (int) ($this->pagemenu ? $this->pagemenu->numItems : $this->getResult()->numRows()),
			'searchable' => $this->searchable,
			'sorted' => $this->sorted,
			'sortableURL' => $this->sortableURL,
			'filtered' => $this->filtered,
			'filterable' => $this->filterable,
			'orderable' => $this->orderable,
			'orderDefaultField' => $this->orderDefault,
			'orderDefaultASC' => $this->orderDefaultAsc,
		]);
	}

	protected function renderJSONData()
	{
		$data = [];
		$result = $this->getResult();
		$table = $result->table;
		while ($gdo = $table->fetch($result))
		{
			$dat = [];
			foreach ($this->getHeaderFields() as $gdt)
			{
				if ($gdt->name && $gdt->isSerializable())
				{
					$json = $gdt->gdo($gdo)->renderJSON();
					if (is_array($json))
					{
						foreach ($json as $k => $v)
						{
							$dat[$k] = $v;
						}
					}
					else
					{
						$dat[$gdt->name] = $json;
					}
				}
			}
			$data[] = $dat;
		}
		return $data;
	}

	public function renderXML()
	{
		$xml = "<data>\n";
		while ($gdo = $this->result->fetchObject())
		{
			$xml .= "<row>\n";
			foreach ($this->getHeaderFields() as $gdt)
			{
				if ($gdt->name && $gdt->isSerializable())
				{
					$xml .= $gdt->gdo($gdo)->renderXML();
				}
			}
			$xml .= "</row>\n";
		}
		$xml .= "</data>\n";
		return $xml;
	}

	public function renderCLI()
	{
		$p = $this->getPageMenu();
		if ($p && $p->getPageCount() > 1)
		{
			$items = [];
			while ($gdo = $this->result->fetchObject())
			{
				$items[] = $gdo->renderCLI();
			}
			return t('cli_pages',
			[
				$this->renderTitle(),
				$p->getPage(),
				$p->getPageCount(),
				implode(', ', $items)
			]);
		}
		else
		{
			$items = [];
			while ($gdo = $this->result->fetchObject())
			{
				$items[] = $gdo->renderCLI();
			}
			return t('cli_page', [
				$this->renderTitle(),
				implode(', ', $items)
			]);
		}
	}

	# ###############
	# ## Page for ###
	# ###############
	/**
	 * Calculate the page for a gdo.
	 * We do this by examin the order from our filtered query.
	 * We count(*) the elements that are before or after orderby.
	 *
	 * @param GDO $gdo
	 * @throws GDOException
	 */
	public function getPageFor(GDO $gdo)
	{
		if ($this->result instanceof ArrayResult)
		{
			throw new GDOException("@TODO implement getPageFor() ArrayResult");
		}
		else
		{
			$q = $this->query->copy(); # ->noJoins();
			foreach ($q->order as $i => $column)
			{
				$subq = $gdo->entityQuery()
					->from($gdo->gdoTableName() . " AS sq{$i}")
					->selectOnly($column)
					->buildQuery();
				$order = stripos($column, 'DESC') ? '0' : '1';
				$cmpop = $order ? '<' : '>';
				$q->where("{$column} {$cmpop} ( {$subq} )");
			}
			$q->selectOnly('COUNT(*)')->noOrder();
			$itemsBefore = $q->exec()->fetchValue();
			return $this->getPageForB($itemsBefore);
		}
	}

	private function getPageForB($itemsBefore)
	{
		$ipp = $this->getPageMenu()->ipp;
		return intval(($itemsBefore + 1) / $ipp) + 1;
	}

}
