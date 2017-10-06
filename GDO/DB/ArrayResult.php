<?php
namespace GDO\DB;
use GDO\Core\GDO;
use GDO\Core\GDT;
/**
 * Mimics a GDO Result from database.
 * Used in, e.g. Admin_Modules overview, as its loaded from FS.
 * 
 * @author gizmore
 * @since 5.00
 * @version 6.05
 */
final class ArrayResult extends Result
{
	/**
	 * @var GDO[]
	 */
	public $data;
	
	private $index;
	
	public function __construct(array $data, GDO $table)
	{
		$this->data = array_values($data);
		$this->table = $table;
		$this->reset();
	}
	
	/**
	 * 
	 * @param array $data
	 * @param GDO $table
	 * @param GDT[] $filters
	 * @return ArrayResult
	 */
	public static function filtered(array $data, GDO $table, array $filters)
	{
	    foreach ($filters as $filter)
	    {
    	    $filtered = [];
	        foreach ($data as $gdo)
	        {
	            if (!$filter->gdo($gdo)->filterGDO($gdo))
	            {
	                $filtered[] = $gdo;
	            }
	        }
	        $data = $filtered;
	    }
	    return new self($data, $table);
	}
	
	public function reset()
	{
		$this->index = 0;
		return $this;
	}
	
	public function numRows()
	{
		return count($this->data);
	}

	public function fetchRow()
	{
		return array_values($this->fetchAssoc());
	}

	public function fetchAssoc()
	{
		return $this->fetchObject()->getGDOVars();
	}
	
	public function fetchAs(GDO $table)
	{
		return $this->fetchObject();
	}
	
	public function fetchObject()
	{
		return isset($this->data[$this->index]) ? $this->data[$this->index++] : null;
	}
}
