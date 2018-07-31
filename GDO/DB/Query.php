<?php
namespace GDO\DB;
use GDO\Core\GDO;
use GDO\Core\Logger;
/**
 * Query builder.
 * Part of the GDO DBA code.
 * You should use GDO classes to create queries.
 * 
 * @example GDO_User::table()->select('*')->execute()->fetchAll();
 * 
 * @see GDO
 * @see Result
 * @see Database
 * 
 * @author gizmore
 * @version 6.07
 * @since 5.00
 */
class Query
{
	# Type constants
    const SELECT = "SELECT";
    const INSERT = "INSERT INTO";
    const REPLACE = "REPLACE INTO";
    const UPDATE = "UPDATE";
    const DELETE = "DELETE FROM";
    
	/**
	 * The table to manipulate.
	 * @var GDO
	 */
	public $table;
	
	/**
	 * The fetch object gdo table.
	 * @var GDO
	 */
	public $fetchTable;
	
	# query parts
	private $columns;
	private $where;
	private $join;
	private $group;
	private $having;
	private $from;
	private $type;
	private $set;
	private $order;
	public  $values;
	private $limit;
	
	private $write = false; # Is it a write query?
	
	public function __construct(GDO $table)
	{
		$this->table = $table;
		$this->fetchTable = $table;
	}
	
	#############
	### Cache ###
	#############
	/**
	 * Use this to avoid using the GDO cache.
	 * @return \GDO\DB\Query
	 */
	public function uncached() { return $this->cached(false); }
	public function cached($cached=true) { $this->cached = $cached; return $this; }
	private $cached = true;

	#############
	### Debug ###
	#############
	/**
	 * Enable logging and verbose output.
	 * @return \GDO\DB\Query
	 */
	public function debug() { $this->debug = true; return $this; }
	private $debug = false;
	
	#############
	### Clone ###
	#############
	/**
	 * Copy this query.
	 * Used to build pagination queries from selects.
	 * @return \GDO\DB\Query
	 */
	public function copy()
	{
		$clone = new self($this->table);
// 		$clone->columns = $this->columns;
		$clone->type = $this->type;
		$clone->from = $this->from;
		$clone->where = $this->where;
		$clone->join = $this->join;
// 		$clone->group = $this->group;
// 		$clone->having = $this->having;
		$clone->from = $this->from;
		$clone->write = $this->write;
		return $clone;
	}
	
	/**
	 * Specify which GDO class is used for fetching.
	 * @todo Rename function
	 * @param GDO $fetchTable
	 * @return \GDO\DB\Query
	 */
	public function fetchTable(GDO $fetchTable)
	{
		$this->fetchTable = $fetchTable;
		return $this;
	}
	
	public function update($tableName)
	{
		$this->type = self::UPDATE;
		$this->write = true;
		return $this->from($tableName);
	}
	
	public function insert($tableName)
	{
		$this->type = self::INSERT;
		$this->write = true;
		return $this->from($tableName);
	}
	
	public function replace($tableName)
	{
		$this->type = self::REPLACE;
		$this->write = true;
		return $this->from($tableName);
	}
	
	/**
	 * @param string $condition
	 * @param string $op
	 * @return self
	 */
	public function where($condition, $op="AND")
	{
		if ($this->where)
		{
			$this->where.= " $op ($condition)";
		}
		else
		{
			$this->where= "($condition)";
		}
		return $this;
	}
	
	public function orWhere($condition)
	{
		return $this->where($condition, "OR");
	}
	
	public function getWhere()
	{
		return $this->where ? " WHERE {$this->where}" : "";
	}
	
	/**
	 * @param string $condition
	 * @param string $op
	 * @return self
	 */
	public function having($condition, $op="AND")
	{
		if ($this->having)
		{
			$this->having .= " $op ($condition)";
		}
		else
		{
			$this->having= "($condition)";
		}
		return $this;
	}
	
	public function getHaving()
	{
		return $this->having ? " HAVING {$this->having}" : "";
	}
	
	public function from($tableName)
	{
		if ($this->from)
		{
			$this->from .= ',' . $tableName;
		}
		else
		{
			$this->from = $tableName;
		}
		return $this;
	}
	
	public function fromSelf()
	{
		return $this->from($this->table->gdoTableIdentifier());
	}
	
	public function getFrom()
	{
		return $this->from ? " {$this->from}" : "";
	}
	
	public function select($columns=null)
	{
		$this->type = self::SELECT;
		if ($columns)
		{
    		if ($this->columns)
    		{
    			$this->columns .= ", $columns";
    		}
    		else
    		{
    			$this->columns = " $columns";
    		}
		}
		return $this;
	}
	
	public function limit($count, $start=0)
	{
		$this->limit = " LIMIT $start, $count";
		return $this;
	}
	
	/**
	 * Limit results to one.
	 * @return self
	 */
	public function first()
	{
		return $this->limit(1);
	}
		
	public function getLimit()
	{
		return $this->limit ? $this->limit : '';
	}
	
	public function getSelect()
	{
		return $this->write ? '' : ($this->getSelectColumns() . " FROM");
	}
	
	private function getSelectColumns()
	{
	    return $this->columns ? $this->columns : '*';
	}
	
	public function delete($tableName)
	{
		$this->type = self::DELETE;
		$this->write = true;
		return $this->from($tableName);
	}
	
	/**
	 * Build part of the SET clause.
	 * @param string $set
	 * @return \GDO\DB\Query
	 */
	public function set($set)
	{
		if ($this->set)
		{
			$this->set .= ',' . $set;
		}
		else
		{
			$this->set = $set;
		}
		return $this;
	}
	
	public function getSet()
	{
		return $this->set ? " SET {$this->set}" : "";
	}
	
	public function order($column=null, $ascending=true)
	{
		if ($column)
		{
			$order = $column . ($ascending ? ' ASC' : ' DESC');
			if ($this->order)
			{
				$this->order .= ',' . $order;
			}
			else
			{
				$this->order = $order;
			}
		}
		return $this;
	}
	
	public function join($join)
	{
		if ($this->join)
		{
			$this->join .= " $join";
		}
		else
		{
			$this->join = " $join";
		}
		return $this;
	}
	
	/**
	 * Automatically build a join based on a GDT_Object column of this queries GDO table.
	 * @param string $key the GDO
	 * @param string $join
	 * @return \GDO\DB\Query
	 * @see GDO
	 */
	public function joinObject($key, $join='JOIN')
	{
		$gdoType = $this->table->gdoColumn($key);
		if ($gdoType instanceof GDT_Join)
		{
		    $join = $gdoType->join;
		}
		else # GDT_Object
		{
		    $table = $gdoType->foreignTable();
		    $ftbl = $table->gdoTableIdentifier();
		    $atbl = $this->table->gdoTableIdentifier();
		    $join = "{$join} {$table->gdoTableIdentifier()} ON  $ftbl.{$table->gdoAutoIncColumn()->identifier()}=$atbl.{$gdoType->identifier()}";
		}
		return $this->join($join);
	}
	
	public function group($group)
	{
		$this->group = $this->group ? "{$this->group},{$group}" : $group;
		return $this;
	}
	
	public function values(array $values)
	{
		$this->values = $values;
		return $this;
	}
	
	public function getValues()
	{
		if (!$this->values)
		{
			return '';
		}
		$fields = [];
		$values = [];
		foreach ($this->values as $key => $value)
		{
			$fields[] = GDO::quoteIdentifierS($key);
			$values[] = GDO::quoteS($value);
		}
		$fields = implode(',', $fields);
		$values = implode(',', $values);
		return " ($fields) VALUES ($values)";
	}
	
	public function getJoin()
	{
		return $this->join ? " {$this->join}" : "";
	}
	
	public function getGroup()
	{
		return $this->group ? " GROUP BY $this->group" : "";
	}
	
	public function getOrderBy()
	{
		return $this->order ? " ORDER BY {$this->order}" : "";
	}

	/**
	 * Execute a query.
	 * Returns boolean on writes and a Result on reads.
	 * @see \GDO\DB\Result
	 * @return \GDO\DB\Result
	 */
	public function exec()
	{
		$db = Database::instance();

		$query = $this->buildQuery();

		if ($this->debug)
		{
			echo "{$query}\n";
			Logger::rawLog('query', $query);
		}
		
		if ($this->write)
		{
			return $db->queryWrite($query);
		}
		else
		{
			return new Result($this->fetchTable, $db->queryRead($query), $this->cached);
		}
	}
	
	/**
	 * Build the query string.
	 * @return string
	 */
	public function buildQuery()
	{
		return
			$this->type .
			$this->getSelect() .
			$this->getFrom() . 
			$this->getValues() .
			$this->getJoin() .
			$this->getSet() .
			$this->getWhere() .
			$this->getGroup() .
			$this->getHaving() .
			$this->getOrderBy() .
			$this->getLimit(); 
	}

}
