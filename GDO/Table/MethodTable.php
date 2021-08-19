<?php
namespace GDO\Table;

use GDO\Core\Method;
use GDO\DB\ArrayResult;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Fields;
use GDO\Core\GDO;

/**
 * A method that displays a table from memory via ArrayResult.
 * It's the base class for more complex methods like MethodQueryTable or MethodQueryCards.
 * The basic API is identical for static memory results and queried data.
 * 
 * @author gizmore
 * @version 6.10.5
 * @since 5.0.0
 * @see ArrayResult
 * @see GDT_Table
 * @see GDT
 * @see GDO
 */
abstract class MethodTable extends Method
{
    public function allParameters()
    {
        return array_merge($this->gdoParameters(),
            $this->table->headers->fields);
    }

    public function gdoParameterVar($key)
    {
        $gdt = $this->gdoParameter($key);
        if ($this->table->headers->hasField($key))
        {
            return $gdt->getRequestVar($this->table->headers->name, $gdt->var);
        }
        return parent::gdoParameterVar($key);
    }

    ################
    ### Abstract ###
    ################
    /**
     * Override this with returning your GDO->table()
     * @return GDO
     */
    public abstract function gdoTable();
    public function gdoTableName() { return 'table'; }

    /**
     * Override this with returning an ArrayResult with data.
     * @return ArrayResult
     */
    public function getResult() { return new ArrayResult([], $this->gdoTable()); }

    /**
     * Override this, if it is required to fetch a different class than your gdoTable().
     * @return GDO
     */
    public function fetchAs() {}

    /**
     * Override this to toggle fetchInto speedup in table rendering to reduce GDO allocations.
     * @return boolean
     */
    public function useFetchInto() { return false; }

    /**
     * Default IPP defaults to config in Module_Table.
     * @see Module_Table::getConfig()
     * @return string
     */
    public function getDefaultIPP() { return Module_Table::instance()->cfgItemsPerPage(); }

    /**
     * Override this.
     * Return an array of GDT[] for the table headers.
     * Defaults to all fields from your gdoTable(). 
     * @return GDT_Fields
     */
    public function gdoHeaders() { return $this->gdoTable()->gdoColumnsCache(); }

    /**
     * The header GDT name.
     * Defaults to 'o' for get parameters.
     * You need to adjust this when showing multiple tables or methods in a single page.
     * @return string
     */
    public function getHeaderName() { return 'o'; }

    /**
     * Override this.
     * Called upon creation of the GDT_Table.
     * @param GDT_Table $table
     */
    public function createTable(GDT_Table $table) {}

    /**
     * @var GDT_Table
     */
    public $table;

    public function __construct()
    {
        $this->table = $this->createCollection();
        $this->table->setupHeaders($this->isSearched(), $this->isPaginated());
    }

    /**
     * Creates the collection GDT.
     * @return GDT_Table|GDT_List
     */
    public function createCollection()
    {
        $this->table = GDT_Table::make($this->gdoTableName());
        return $this->table->gdtTable($this->gdoTable());
    }

    ##################
    ### 5 features ###
    ##################
    /**
     * Override this.
     * Return true if this table shall be able to be ordered by headers.
     * @return boolean
     */
	public function isOrdered() { return true; }

	/**
	 * Override this.
	 * Return true if this table shall be searchable over all columns with one input field.
	 * This is called "HugeQuery" in the GDT_Table implementation.
	 * @return boolean
	 */
	public function isSearched() { return true; } # GDT$searchable

	/**
	 * Override this.
	 * Return true if you want to be able to filter your data by your header columns.
	 * @return boolean
	 */
	public function isFiltered() { return true; } # GDT#filterable

	/**
	 * Override this.
	 * Return true if you want pagination for this table method.
	 * @return boolean
	 */
	public function isPaginated() { return true; } # creates a GDT_Pagemenu

	/**
	 * Override this.
	 * Return true if you want to be able to sort this table data manually.
	 * This requires a GDT_Sort field in your GDO columns / headers as well as MethodSort endpoint.
	 * @return boolean
	 * @see GDT_Sort
	 * @see MethodSort
	 */
	public function isSorted() { return true; } # Uses js/ajax and GDO needs to have GDT_Sort column.

	###
	public function getDefaultOrder()
	{
	    foreach ($this->table->getHeaderFields() as $gdt)
	    {
	        if ($gdt->orderable)
	        {
	            return $gdt->name;
	        }
	    }
	}

	public function getIPP()
	{
	    $o = $this->table->headers->name;
	    $defaultIPP = $this->getDefaultIPP();
	    return $this->isPaginated() ?
	       $this->table->getHeaderField('ipp')->getRequestVar($o, $defaultIPP) :
	       $defaultIPP;
	}

	public function getPage()
	{
	    $o = $this->table->headers->name;
	    return $this->table->getHeaderField('page')->getRequestVar($o, '1');
	}

	public function getSearchTerm()
	{
	    $table = $this->table;
	    return $table->getHeaderField('search')->getRequestVar($table->headers->name);
	}

	###############
	### Execute ###
	###############
	public function beforeExecute()
	{
	    $this->table->result = null;
	}

	public function execute()
	{
		return GDT_Response::makeWith(
		    $this->renderTable());
	}

	public function getTableTitleLangKey()
	{
	    return strtolower('list_'.$this->getModuleName().'_'.$this->getMethodName());
	}

	public function getTableTitle()
	{
	    $key = $this->getTableTitleLangKey();
	    return t($key, [$this->table->countItems()]);
	}

	protected function setupTitle(GDT_Table $table)
	{
	    $table->titleRaw($this->getTableTitle());
	}

	protected function setupCollection(GDT_Table $table)
	{
	    $table->gdo($this->gdoTable());

	    # 5 features
	    $table->ordered($this->isOrdered(), $this->getDefaultOrder());
	    $table->filtered($this->isFiltered());
	    $table->searched($this->isSearched());
	    $table->sorted($this->isSorted());
	    $table->paginated($this->isPaginated(), null, $this->getIPP());

	    # 1 speedup
	    $table->fetchInto($this->useFetchInto());
	}

	public function initTable()
	{
	    $table = $this->table;
	    $this->setupCollection($table);
	    $this->createTable($table);
	    $this->table->addHeaders($this->gdoHeaders());
	    $this->calculateTable($table);
	    $result = $table->getResult();
	    if ($fetchAs = $this->fetchAs())
	    {
	        $table->fetchAs($fetchAs);
	        $result->table = $fetchAs;
	    }
	    $this->setupTitle($table);
	    return $table;
	}

	public function renderTable()
	{
	    return $this->initTable();
	}

	protected function calculateTable(GDT_Table $table)
	{
	    # Exec
	    $result = $this->getResult();

	    # Exec features
	    if ($this->isFiltered())
	    {
	        $result = $result->filterResult($result->getFullData(), $this->gdoTable(), $table->getHeaderFields(), $table->headers->name);
	    }
	    if ($this->isSearched())
	    {
	        $result = $result->searchResult($result->getData(), $this->gdoTable(), $table->getHeaderFields(), $this->getSearchTerm());
	    }
	    if ($this->isOrdered())
	    {
	        $result = $table->multisort($result, $this->getDefaultOrder());
	    }
	    if ($this->isPaginated())
	    {
	        $this->table->pagemenu->items(count($result->getData()));
	        $result = $this->table->pagemenu->paginateResult($result, $this->getPage(), $this->getIPP());
	    }
	    $table->result($result);
	}

	public function renderCLIHelp()
	{
	    $this->calculateTable($this->initTable());
	    return parent::renderCLIHelp();
	}

}
