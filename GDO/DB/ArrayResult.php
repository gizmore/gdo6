<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\GDT;

/**
 * Mimics a GDO Result from database.
 * Used in, e.g. Admin_Modules overview, as its loaded from FS.
 * 
 * @author gizmore
 * @version 6.10
 * @since 5.00
 */
final class ArrayResult extends Result
{
    /**
     * @var GDO[]
     */
    private $data;
    
    /**
	 * @var GDO[]
	 */
	private $fullData;
	
	/**
	 * @var int
	 */
	private $index;
	
	public function __construct(array &$data, GDO $table)
	{
		$this->data = &$data;
		$this->fullData = &$data;
		$this->table = $table;
		$this->reset();
	}
	
	public function data(array &$data)
	{
	    $this->data = &$data;
	    return $this;
	}
	
	public function fullData(array &$fullData)
	{
	    $this->fullData = &$fullData;
	    return $this;
	}
	
	public function &getData()
	{
	    return $this->data;
	}
	
	public function &getFullData()
	{
	    return $this->fullData;
	}
	
	#############
	### Table ###
	#############
	public function reset() { $this->index = 0; return $this; }
	public function numRows() { return count($this->data); }
	public function fetchRow() { return array_values($this->fetchAssoc()); }
	public function fetchAssoc() { return $this->fetchObject()->getGDOVars(); }
	public function fetchAs(GDO $table) { return $this->fetchObject(); }
	/**
	 * @return GDO
	 */
	public function fetchObject()
	{
	    if ($this->index >= count($this->data))
	    {
	        return null;
	    }
	    $slice = array_slice($this->data, $this->index++, 1);
	    return array_pop($slice);
	}
	public function fetchInto(GDO $gdo)
	{
	    if ($o = $this->fetchObject())
	    {
	        return $gdo->setGDOVars($o->getGDOVars());
	    }
	}
	
	
	##############
	### Filter ###
	##############
	/**
	 *
	 * @param GDO[] $data
	 * @param GDO $table
	 * @param GDT[] $filters
	 * @return ArrayResult
	 */
	public function filterResult(array $data, GDO $table, array $filters, $rq)
	{
	    foreach ($filters as $gdt)
	    {
	        if ($gdt->filterable)
	        {
	            $filter = trim($gdt->filterVar($rq));
	            if ( ($filter !== null) && ($filter !== '') )
	            {
	                $keep = [];
	                foreach ($data as $gdo)
	                {
	                    if ($gdt->gdo($gdo)->filterGDO($gdo, $filter))
    	                {
    	                    $keep[] = $gdo;
    	                }
	                }
	                $data = $keep;
	            }
	        }
	    }
	    $this->data = $data;
	    return $this;
	}

	##############
	### Search ###
	##############
	/**
	 * Deepsearch a static result. Like a global table search.
	 * @param GDO[] $data
	 * @param GDO $table
	 * @param GDT[] $filters
	 * @param string $searchTerm
	 */
	public function searchResult(array $data, GDO $table, array $filters, $searchTerm)
	{
	    if ($searchTerm !== null)
	    {
	        $hits = [];
            foreach ($data as $gdo)
            {
        	    foreach ($filters as $gdt)
        	    {
        	        if ($gdt->searchable)
        	        {
       	                if ($gdt->gdo($gdo)->searchGDO($searchTerm))
       	                {
       	                    $hits[] = $gdo;
       	                    break;
        	            }
        	        }
        	    }
            }
            $data = $hits;
	    }
	    $this->data = $data;
	    return $this;
	}
	
}
