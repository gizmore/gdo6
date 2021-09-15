<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\Logger;
use GDO\Core\GDOException;
use GDO\Util\Strings;

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
 * @version 6.10.3
 * @since 5.0.0
 */
final class Query
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
	public  $order;
	public  $values;
	private $limit;
	private $raw;
	
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
	 * Use this to avoid using the GDO cache. This means the memcache might be still used? This means no single identity?
	 * @return \GDO\DB\Query
	 */
	public function uncached() { return $this->cached(false); }
	public function cached($cached=true) { $this->cached = $cached; return $this; }
	private $cached = true;

	public $buffered = true;

	/**
	 * Mark this query's buffered mode.
	 * @param boolean $buffered
	 * @return self
	 */
	public function buffered($buffered)
	{
	    $this->buffered = !!$buffered;
	    return $this;
	}
	public function unbuffered()
	{
	    return $this->buffered(false);
	}
	
	#############
	### Debug ###
	#############
	/**
	 * Enable logging and verbose output.
	 * @return \GDO\DB\Query
	 */
	public function debug($debug=true) { $this->debug = $debug; return $this; }
	private $debug = false;
	
	#############
	### Clone ###
	#############
	/**
	 * Copy this query.
	 * @return self
	 */
	public function copy()
	{
		$clone = new self($this->table);
		if ($this->raw)
		{
		    $clone->raw = $this->raw;
		}
		else
		{
            $clone->fetchTable = $this->fetchTable;
    		$clone->type = $this->type;
    		$clone->columns = $this->columns;
    		$clone->from = $this->from;
    		$clone->where = $this->where;
    		$clone->join = $this->join;
    		$clone->group = $this->group;
    		$clone->having = $this->having;
            $clone->order = $this->order;
            $clone->limit = $this->limit;
    		$clone->from = $this->from;
    		$clone->write = $this->write;
    		$clone->debug = $this->debug;
    		$clone->cached = $this->cached;
    		return $clone;
		}
	}
	
	/**
	 * Specify which GDO class is used for fetching.
	 * @TODO Rename function
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
	 * @return static
	 */
	public function where($condition, $op="AND")
	{
		$this->where = $this->where ? $this->where . " $op ($condition)" : "($condition)";
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
	
		
	/**
	 * @param string $tableName
	 * @return self
	 */	
	public function from($tableName)
	{
		$this->from = $this->from ? $this->from . ", $tableName" : $tableName;
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
	
	/**
	 * Build a select.
	 * @param string $columns
	 * @return self
	 */
	public function select($columns=null)
	{
		$this->type = self::SELECT;
		if ($columns) # ignore empty
		{
			$this->columns = $this->columns ? 
			     "{$this->columns}, $columns" : " $columns";
		}
		return $this;
	}
	
	/**
	 * Select a field as first column in query.
	 * Useful to build count queries out of filtered tables etc.
	 * @param string $columns
	 * @return self
	 */
	public function selectAtFirst($columns="COUNT(*)")
	{
	    if ($columns)
	    {
	        $this->columns = $this->columns ? 
	           " {$columns}, {$this->columns}" : " $columns";
	    }
	    return $this;
	}
	
	/**
	 * Build a select but reset columns.
	 * @param string $columns
	 * @return self
	 */
	public function selectOnly($columns=null)
	{
	    $this->columns = null;
	    return $this->select($columns);
	}
	
	/**
	 * @param int $count
	 * @param int $start
	 * @return self
	 */
	public function limit($count, $start=0)
	{
		$this->limit = " LIMIT $start, $count";
		return $this;
	}
	
	public function noLimit()
	{
	    $this->limit = null;
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
		return $this->columns ? $this->columns : ' *';
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

	
	public function noOrder()
	{
	    $this->order = null;
	    return $this;
	}
	
	/**
	 * Order clause.
	 * @TODO make it one var. 'foo ASC'
	 * @param string $order
	 * @return self
	 */
	public function order($order)
	{
		if ($order)
		{
		    if ($this->order === null)
		    {
		        $this->order = [$order];
		    }
		    else
		    {
		        $this->order[] = $order;
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
	public function joinObject($key, $join='JOIN', $tableAlias='')
	{
		if (!($gdoType = $this->table->gdoColumn($key)))
		{
			throw new GDOException(t('err_column', [html($key)]));
		}
		
		if ($gdoType instanceof GDT_Join)
		{
			$join = $gdoType->join;
		}
		elseif ( ($gdoType instanceof GDT_Object) ||
			($gdoType instanceof GDT_ObjectSelect) )
		{
			$table = $gdoType->foreignTable();
			$ftbl = $tableAlias ? $tableAlias : $table->gdoTableIdentifier();
			$atbl = $this->table->gdoTableIdentifier();
			$tableAlias = $tableAlias ? " AS {$tableAlias}" : '';
			
			$join = "{$join} {$table->gdoTableIdentifier()}{$tableAlias} ON {$ftbl}.{$table->gdoPrimaryKeyColumn()->identifier()}=$atbl.{$gdoType->identifier()}";
		}
		else
		{
			throw new GDOException(t('err_join_object', [html($key), html($this->table->displayName())]));
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
	    $this->values = $this->values ? array_merge($this->values, $values) : $values;
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
	
	public function noJoins()
	{
	    $this->join = null;
	    return $this;
	}
	
	public function getGroup()
	{
		return $this->group ? " GROUP BY $this->group" : "";
	}
	
	public function getOrderBy()
	{
		return $this->order ? ' ORDER BY ' . implode(', ', $this->order) : '';
	}
	
	public function raw($raw)
	{
	    $this->write = !Strings::startsWith($raw, 'SELECT');
	    $this->raw = $raw;
	    return $this;
	}

	/**
	 * Build the query string.
	 * @return string
	 */
	public function buildQuery()
	{
	    return $this->raw ?
    	    $this->raw :
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
	
	/**
	 * Execute a query.
	 * Returns boolean on writes and a Result on reads.
	 * @see Result
	 * @return Result
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
			return new Result($this->fetchTable, $db->queryRead($query, $this->buffered), $this->cached);
		}
	}
	
}
