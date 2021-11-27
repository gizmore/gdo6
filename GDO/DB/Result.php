<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\GDT;

/**
 * A Database query result.
 * Use fetchTable() to control the object type for fetching objects. 
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.0.0
 * @see ArrayResult
 */
class Result
{
    /**
     * @var GDO
     */
	public $table;
	private $result;
	private $useCache;
	
	public function __construct(GDO $table, $result, $useCache)
	{
		$this->table = $table;
		$this->result = $result;
		$this->useCache = $useCache;
	}
	
	/**
	 * Shouldn't it be as safe and as fast to just rely on their destructors?
	 */
	public function __destruct()
	{
	    $this->free();
	}
	
	public function free()
	{
	    if ($this->result)
	    {
	        mysqli_free_result($this->result);
	        $this->result = null;
	    }
	}
	
	################
	### Num rows ###
	################
	/**
	 * @return int
	 */
	public function numRows()
	{
		return mysqli_num_rows($this->result);
	}
	
	public function affectedRows()
	{
	    return Database::instance()->affectedRows();
	}
	
	#############
	### Fetch ###
	#############
	/**
	 * Fetch the first value of the next row.
	 * @TODO rename to fetchVar()
	 * @return string
	 */
	public function fetchValue()
	{
		if ($row = $this->fetchRow())
		{
			return $row[0];
		}
	}
	
	public function fetchRow()
	{
		return mysqli_fetch_row($this->result);
	}
	
	public function fetchAllRows()
	{
		$allRows = [];
		while ($row = mysqli_fetch_row($this->result))
		{
			$allRows[] = $row;
		}
		return $allRows;
	}
	
	
	/**
	 * @return string[]
	 */
	public function fetchAssoc()
	{
		return mysqli_fetch_assoc($this->result);
	}
	
	public function fetchAllAssoc()
	{
		$data = [];
		while ($row = $this->fetchAssoc())
		{
			$data[] = $row;
		}
		return $data;
	}
	
	/**
	 * @return GDO
	 */
	public function fetchObject()
	{
		return $this->fetchAs($this->table);
	}
	
	/**
	 * @param GDO $table
	 * @return GDO
	 */
	public function fetchAs(GDO $table)
	{
		if ($gdoData = $this->fetchAssoc())
		{
			if ($this->useCache && $table->cached())
			{
				return $table->initCached($gdoData);
			}
			elseif ($table->cached())
			{
			    return $table->initCached($gdoData, false);
			}
			else
			{
				$class = $table->gdoClassName();
				/** @var $object GDO **/
				$object = new $class();
				return $object->setGDOVars($gdoData)->setPersisted();
			}
		}
	}
	
	public function fetchInto(GDO $gdo)
	{
	    if ($gdoVars = $this->fetchAssoc())
	    {
	        return $gdo->tempReset()->setGDOVars($gdoVars)->setPersisted();
	    }
	}

	/**
	 * @return GDO[]
	 */
	public function fetchAllObjects($json=false)
	{
		return $this->fetchAllObjectsAs($this->table, $json);
	}
	
	/**
	 * @return GDO[]
	 */
	public function fetchAllObjectsAs(GDO $table, $json=false)
	{
		$objects = [];
		while ($object = $this->fetchAs($table, $json))
		{
			$objects[] = $json ? $object->toJSON() : $object;
		}
		return $objects;
	}

	/**
	 * Fetch all 2 column rows as a 0 => 1 assoc array.
	 * @return string[]
	 */
	public function fetchAllArray2dPair()
	{
		$array2d = [];
		while ($row = $this->fetchRow())
		{
			$array2d[$row[0]] = $row[1];
		}
		return $array2d;
	}
	
	public function &fetchAllArray2dObject(GDO $table=null, $json=false)
	{
		$table = $table ? $table : $this->table;
		$array2d = [];
		while ($object = $this->fetchAs($table))
		{
			$array2d[$object->getID()] = $json ? $object->toJSON() : $object;
		}
		return $array2d;
	}
	
	public function fetchAllArrayAssoc2dObject(GDO $table=null)
	{
		$table = $table ? $table : $this->table;
		$array2d = [];
		$firstKey = '';
		while ($object = $this->fetchAs($table))
		{
			$firstKey = $firstKey ? $firstKey : array_keys($object->getGDOVars())[0];
			$array2d[$object->getVar($firstKey)] = $object;
		}
		return $array2d;
	}
	
	/**
	 * Fetch all, but only a single column as simple array.
	 * @return string[]
	 */
	public function fetchAllValues()
	{
		$values = [];
		while ($value = $this->fetchValue())
		{
			$values[] = $value;
		}
		return $values;
	}
	
	public function fetchColumn()
	{
	    return $this->fetchAllValues();
	}
	
	############
	### JSON ###
	############
	/**
	 * @param GDT[] $headers
	 * @return string[]
	 */
	public function renderJSON(array $headers)
	{
		$data = [];
		while ($gdo = $this->fetchObject())
		{
			$row = [];
			foreach($headers as $gdoType)
			{
				$row[] = $gdoType->gdo($gdo)->gdoRenderCell();
			}
			$data[] = $row;
		}
		return $data;
	}
}
