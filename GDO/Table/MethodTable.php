<?php
namespace GDO\Table;

use GDO\Core\Method;
use GDO\DB\ArrayResult;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Fields;
use GDO\Core\GDO;

/**
 * A method that displays a table from memory ArrayResult.
 * 
 * @author gizmore
 * @version 6.10
 * @since 3.0
 */
abstract class MethodTable extends Method
{
    ################
    ### Abstract ###
    ################
    /**
     * @return GDO
     */
    public abstract function gdoTable();
    
    /**
     * @return ArrayResult
     */
    public function getResult() { return new ArrayResult([], $this->gdoTable()); }
    
    public function getDefaultIPP() { return Module_Table::instance()->cfgItemsPerPage(); }
    
    /**
     * @return GDT_Fields
     */
    public function gdoHeaders() { return $this->gdoTable()->gdoColumnsCache(); }
    
    public function getHeaderName() { return 'o'; }
    
    /**
     * On creation.
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
     * @return GDT_Table|GDT_List
     */
    public function createCollection()
    {
        $this->table = GDT_Table::make();
        return $this->table->gdtTable($this->gdoTable());
    }
    
    ##################
    ### 5 features ###
    ##################
	public function isOrdered() { return true; } # GDT$orderable
	public function isSearched() { return true; } # GDT$searchable
	public function isFiltered() { return true; } # GDT#filterable
	public function isPaginated() { return true; } # creates a GDT_Pagemenu
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
	
	public function getDefaultOrderDir()
	{
	    return true;
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
	public function execute()
	{
		return $this->renderTable();
	}
	
	protected function setupTitlePrefix() { return 'method_table'; }
	protected function setupTitle(GDT_Table $table)
	{
	    if ($prefix = $this->setupTitlePrefix())
	    {
	        $key = strtolower(sprintf('%s_%s_%s', $prefix, $this->getModuleName(), $this->gdoShortName()));
	        $numItems = $table->getPageMenu()->numItems;
	        $table->title($key, [$numItems]);
    	    return $this->title($key, [$numItems]);
	    }
	    return $this;
	}
	
	protected function setupCollection(GDT_Table $table)
	{
	    $table->gdo($this->gdoTable());
	    
	    # 5 features
	    $table->ordered($this->isOrdered(), $this->getDefaultOrder(), $this->getDefaultOrderDir());
	    $table->filtered($this->isFiltered());
	    $table->searched($this->isSearched());
	    $table->sorted($this->isSorted());
	    $table->paginated($this->isPaginated(), null, $this->getIPP());
	}
	
	public function renderTable()
	{
        $table = $this->table;
	    $this->setupCollection($table);
	    $this->createTable($table);
	    $this->table->addHeaders($this->gdoHeaders());
	    $this->calculateTable($table);
	    $table->getResult();
	    $this->setupTitle($table);
	    return GDT_Response::makeWith($table);
	}
	
	protected function calculateTable(GDT_Table $table)
	{
	    # Exec
	    $result = $this->getResult();
	    
	    # Exec features
	    if ($this->isFiltered())
	    {
	        $result = $result->filterResult($result->fullData, $this->gdoTable(), $table->getHeaderFields(), $table->headers->name);
	    }
	    if ($this->isSearched())
	    {
	        $result = $result->searchResult($result->data, $this->gdoTable(), $table->getHeaderFields(), $this->getSearchTerm());
	    }
	    if ($this->isOrdered())
	    {
	        $result = $table->multisort($result, $this->getDefaultOrder(), $this->getDefaultOrderDir());
	    }
	    if ($this->isPaginated())
	    {
	        $this->table->pagemenu->items(count($result->data));
	        $result = $this->table->pagemenu->paginateResult($result, $this->getPage(), $this->getIPP());
	    }
	    $table->result($result);
	}
	
}
