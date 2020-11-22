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
	public $fullData;
	/**
	 * @var GDO[]
	 */
	public $data;
	
	private $index;
	
	public function __construct(array $data, GDO $table)
	{
		$this->data = $this->fullData = array_values($data);
		$this->table = $table;
		$this->reset();
	}

	#############
	### Table ###
	#############
	public function reset() { $this->index = 0; return $this; }
	public function numRows() { return count($this->data); }
	public function fetchRow() { return array_values($this->fetchAssoc()); }
	public function fetchAssoc() { return $this->fetchObject()->getGDOVars(); }
	public function fetchAs(GDO $table) { return $this->fetchObject(); }
	public function fetchObject() { return isset($this->data[$this->index]) ? $this->data[$this->index++] : null; }

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
	            if (null != ($filter = $gdt->filterVar($rq)))
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
	 * Deepsearch a static result.
	 * @param GDO[] $data
	 * @param GDO $table
	 * @param GDT[] $filters
	 * @param string $searchTerm
	 */
	public function searchResult(array $data, GDO $table, array $filters, $searchTerm)
	{
	    foreach ($filters as $gdt)
	    {
    	    $hits = [];
	        if ($gdt->searchable)
	        {
	            foreach ($data as $gdo)
	            {
	            }
	        }
	    }
	    return new self($data, $table);
	}
	
}
