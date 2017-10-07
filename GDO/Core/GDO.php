<?php
namespace GDO\Core;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\DB\Query;
use GDO\DB\Result;
/**
 * A gdo is a container for GDTs which values are backed by a database and cache.
 * @author gizmore
 * @version 6.06
 * @since 1.00
 */
abstract class GDO #extends GDT
{
	use WithName;

	const MYISAM = 'myisam';
	const INNODB = 'innodb';
	const MEMORY = 'memory';
	
	/**
	 * @return GDT[]
	 */
	public abstract function gdoColumns();
	
	public function gdoCached() { return true; }
	public function memCached() { return $this->gdoCached(); }
	
	public function gdoTableName() { return strtolower(self::gdoShortNameS()); }
	public function gdoDependencies() { return null; }
	
	public function gdoEngine() { return self::INNODB; }
	public function gdoAbstract() { return false; }
	public function gdoTableIdentifier() { return self::quoteIdentifierS($this->gdoTableName()); }

	################
	### Escaping ###
	################
	public static function escapeIdentifierS($identifier) { return str_replace("`", "\`", $identifier); }
	public static function quoteIdentifierS($identifier) { return "`" . self::escapeIdentifierS($identifier) . "`"; }
	public static function escapeSearchS($value) { return str_replace(array('%', "'", '"'), array('\\%', "\\'", '\\"'), $value); }
	
	public static function escapeS($value) { return str_replace(array("'", '"'), array("\\'", '\\"'), $value); }
	public static function quoteS($value)
	{
		if (is_string($value))
		{
			return "'" . self::escapeS($value) . "'";
		}
		elseif ($value === null)
		{
			return "NULL";
		}
		elseif (is_numeric($value))
		{
			return "$value";
		}
		elseif (is_bool($value))
		{
			return $value ? '1' : '0';
		}
		else
		{
			throw new GDOError('err_cannot_quote', [html($value)]);
		}
	}
	
	#################
	### Persisted ###
	#################
	private $persisted = false;
	public function isPersisted() { return $this->persisted; }
	public function setPersisted($persisted = true) { $this->persisted = $persisted; return $this; }
	
	########################
	### Custom temp vars ###
	########################
	/**
	 * @var mixed[]
	 */
	private $temp;
	public function tempReset() { $this->temp = null; }
	public function tempGet($key) { return @$this->temp[$key]; }
	public function tempSet($key, $value) { if (!$this->temp) $this->temp = []; $this->temp[$key] = $value; }
	public function tempUnset($key) { unset($this->temp[$key]); }
	
	##############
	### Render ###
	##############
	public function display($key)
	{
		return html($this->gdoVars[$key]);
	}
	
	public function edisplay($key)
	{
		echo $this->display($key);
	}
	
	public function toJSON()
	{
		$values = [];
		foreach ($this->gdoColumnsCache() as $key => $gdoType)
		{
			if ($data = $gdoType->gdo($this)->getGDOData())
			{
				$values = array_merge($values, $data);
			}
		}
		return $values;
	}
	
	############
	### Vars ###
	############
	private $gdoVars;
	private $dirty = false;
	public function getGDOVars() { return $this->gdoVars; }
	
	public function hasVar($key=null)
	{
		return isset($this->gdoVars[$key]);
	}
	
	public function getVar($key)
	{
		return @$this->gdoVars[$key];
	}
	
	public function setVar($key, $value, $markDirty=true)
	{
		$this->gdoVars[$key] = $value;
		return $markDirty ? $this->markDirty($key) : $this;
	}
	
	public function setVars(array $vars=null, $markDirty=true)
	{
	    if ($vars)
	    {
    		foreach ($vars as $key => $value)
    		{
    			$this->setVar($key, $value, $markDirty);
    		}
	    }
		return $this;
	}
	
	public function setValue($key, $value, $markDirty=true)
	{
		if ($vars = $this->gdoColumn($key)->value($value)->getGDOData())
		{
			$this->setVars($vars, $markDirty);
		}
		return $this;
	}
	
	public function setGDOVars(array $vars, $dirty=false)
	{
		$this->gdoVars = $vars;
		$this->dirty = $dirty;
		return $this;
	}
	
	/**
	 * @param string[] $keys
	 * @return string[]
	 */
	public function getVars(array $keys)
	{
		$back = [];
		foreach ($keys as $key)
		{
			if ($data = $this->gdoColumn($key)->getGDOData())
			{
				$back = array_merge($back, $data);
			}
		}
		return $back;
	}
	
	/**
	 * Get the gdo value of a column.
	 * @param string $key
	 * @return mixed
	 */
	public function getValue($key)
	{
		return $this->gdoColumn($key)->getValue();
	}
	
	#############
	### Dirty ###
	#############
	public function markClean($key)
	{
		if ($this->dirty === false)
		{
			$this->dirty = array_keys($this->gdoVars);
			unset($this->dirty[$key]);
		}
		elseif (is_array($this->dirty))
		{
			unset($this->dirty[$key]);
		}
		return $this;
	}
	
	public function markDirty($key)
	{
		if ($this->dirty === false)
		{
			$this->dirty = [];
		}
		if ($this->dirty !== true)
		{
			$this->dirty[$key] = true;
		}
		return $this;
	}
	
	public function isDirty()
	{
		return $this->dirty === false ? false : (count($this->dirty) > 0);
	}
	
	/**
	 * Get gdoVars that have been changed.
	 * @return string[]
	 */
	public function getDirtyVars()
	{
		if ($this->dirty === true)
		{
			return $this->getVars(array_keys($this->gdoColumnsCache()));
		}
		elseif ($this->dirty === false)
		{
			return [];
		}
		else
		{
			return $this->getVars(array_keys($this->dirty));
		}
	}
	
	###############
	### Columns ###
	###############
	/**
	 * Get the primary key columns for a table.
	 * @return GDT[]
	 */
	public function gdoPrimaryKeyColumns()
	{
		$columns = [];
		foreach ($this->gdoColumnsCache() as $column)
		{
			if ($column->isPrimary())
			{
				$columns[$column->name] = $column;
			}
			else
			{
				break; # XXX: early break is only possible if we start all tables with their PKs.
			}
		}
		return $columns;
	}
	
	/**
	 * Get the first primary key column
	 * @return GDT
	 */
	public function gdoPrimaryKeyColumn()
	{
		foreach ($this->gdoColumnsCache() as $column)
		{
			if ($column->isPrimary())
			{
				return $column;
			}
		}
	}
	
	/**
	 * Get the first column of a specified GDT.
	 * Useful to make GDTs more automated. E.g. The auto inc column syncs itself on gdoAfterCreate.
	 *
	 * @param string $className
	 * @return \GDO\Core\GDT
	 */
	public function gdoColumnOf($className)
	{
		foreach ($this->gdoColumnsCache() as $name => $gdoType)
		{
			if (is_a($gdoType, $className))
			{
				return $gdoType;
			}
		}
	}
	
	/**
	 * Get the GDOs AutoIncrement column, if any.
	 * @return \GDO\DB\GDT_AutoInc
	 */
	public function gdoAutoIncColumn() { return $this->gdoColumnOf('GDO\DB\GDT_AutoInc'); }
	
	/**
	 * Get the GDOs name identifier column, if any.
	 * @return \GDO\DB\GDT_Name
	 */
	public function gdoNameColumn() { return $this->gdoColumnOf('GDO\DB\GDT_Name'); }
	
	/**
	 * Get the Type for a key.
	 * @param string $key
	 * @return GDT
	 */
	public function gdoColumn($key)
	{
		return $this->gdoColumnsCache()[$key]->gdo($this);
	}
	
	public function gdoColumnsExcept(...$except)
	{
		$columns = $this->gdoColumnsCache();
		foreach ($except as $ex)
		{
			unset($columns[$ex]);
		}
		return $columns;
	}
	
	##########
	### DB ###
	##########
	/**
	 * Create a new query for this GDO table.
	 * @return Query
	 */
	public function query()
	{
		return new Query(self::tableFor($this->gdoClassName()));
	}
	
	/**
	 * Find a row by AutoInc Id.
	 * @param string $id
	 * @return self
	 * @see GDO
	 */
	public function find($id=null, $exception=true)
	{
		if ( (!empty($id)) && ($gdo = $this->getById($id)) )
		{
			return $gdo;
		}
		if ($exception)
		{
			self::notFoundException(html($id));
		}
	}
	
	/**
	 * @param string $where
	 * @return int
	 */
	public function countWhere($condition='true')
	{
		return (int) $this->query()->select("COUNT(*)")->from($this->gdoTableIdentifier())->where($condition)->exec()->fetchValue();
	}
	
	/**
	 * @param string $where
	 * @return self
	 */
	public function findWhere($condition)
	{
		return $this->query()->select('*')->from($this->gdoTableIdentifier())->where($condition)->first()->exec()->fetchObject();
	}
	
	/**
	 * @param string $columns
	 * @return Query
	 */
	public function select($columns=null)
	{
		return $this->query()->select($columns)->from($this->gdoTableIdentifier());
	}
	
	/**
	 * @param string $condition
	 * @return Query
	 */
	public function deleteWhere($condition)
	{
		return $this->query()->delete($this->gdoTableIdentifier())->where($condition);
	}
	
	public function delete()
	{
		if ($this->persisted)
		{
			$this->deleteWhere($this->getPKWhere())->exec();
			$this->persisted = false;
			$this->dirty = false;
		}
		return $this;
	}
	
	public function replace()
	{
		if (empty($this->getID()))
		{
			return $this->insert();
		}
		$this->query()->replace($this->gdoTableIdentifier())->values($this->gdoVars)->exec();
		$this->dirty = false;
		$this->persisted = true;
		$this->gdoAfterUpdate();
		return $this;
	}
	
	public function insert()
	{
		$this->query()->insert($this->gdoTableIdentifier())->values($this->getDirtyVars())->exec();
		$this->afterCreate();
		return $this;
	}
	
	public function update()
	{
		return $this->query()->update($this->gdoTableIdentifier());
	}
	
	public function updateQuery()
	{
		return $this->entityQuery()->update($this->gdoTableIdentifier());
	}
	
	public function save()
	{
		if (!$this->persisted)
		{
			return $this->insert();
		}
		if ($this->isDirty())
		{
			$this->updateQuery()->set($this->getSetClause())->exec();
			$this->dirty = false;
			$this->recache(); # save is the only action where we recache!
			$this->gdoAfterUpdate();
		}
		return $this;
	}
	
	public function increase($key, $by=1)
	{
		return $by === 0 ? $this : $this->saveVar($key, $this->getVar($key)+$by);
	}
	
	public function saveVar($key, $value)
	{
		return $this->saveVars([$key => $value]);
	}
	
	public function saveVars(array $vars, $withHooks=true)
	{
		$worthy = false; # Anything changed?
		$query = $this->updateQuery();
		foreach ($vars as $key => $value)
		{
			if (array_key_exists($key, $this->gdoVars))
			{
				if ($this->gdoVars[$key] != $value)
				{
					$query->set(sprintf("%s=%s", self::quoteIdentifierS($key), self::quoteS($value)));
					$this->markClean($key);
					$worthy = true; # We got a change
				}
			}
		}
		if ($worthy)
		{
			if ($withHooks) $this->beforeUpdate($query); # Can do trickery here... not needed?
			$query->exec();
			$this->gdoVars = array_merge($this->gdoVars, $vars);
			$this->recache(); # save is the only action where we recache!
			if ($withHooks) $this->gdoAfterUpdate();
		}
		return $this;
	}
	
	public function saveValue($key, $value)
	{
		$this->gdoColumn($key)->setGDOValue($value);
		return $this->saveVar($key, $this->getVar($key));
	}
	
	public function saveValues(array $values)
	{
		$vars = array();
		foreach ($values as $key => $value)
		{
			$this->gdoColumn($key)->setGDOValue($value);
			$vars[$key] = $this->getVar($key);
		}
		return $this->saveVars($vars);
	}
	
	/**
	 * @return Query
	 */
	public function entityQuery()
	{
		if (!$this->persisted)
		{
			throw new GDOError('err_save_unpersisted_entity', [$this->gdoClassName()]);
		}
		return $this->query()->where($this->getPKWhere());
	}
	
	public function getSetClause()
	{
		$setClause = '';
		if ($this->dirty !== false)
		{
			foreach ($this->gdoColumnsCache() as $column)
			{
				if ( ($this->dirty === true) || (isset($this->dirty[$column->name])) )
				{
					if ($setClause !== '')
					{
						$setClause .= ',';
					}
					$setClause .= $column->identifier() . "=" . $this->quoted($column->name);
				}
			}
		}
		return $setClause;
	}
	
	####################
	### Primary Keys ###
	####################
	/**
	 * Get the primary key where condition for this row.
	 * @return string
	 */
	public function getPKWhere()
	{
		$where = "";
		foreach ($this->gdoPrimaryKeyColumns() as $column)
		{
			if ($where !== '')
			{
				$where .= ' AND ';
			}
			$where .= $column->identifier() . ' = ' . $this->quoted($column->name);
		}
		return $where;
	}
	
	public function quoted($key) { return self::quoteS($this->getVar($key)); }
	
	################
	### Instance ###
	################
	/**
	 * @param array $gdoVars
	 * @return self
	 */
	public static function entity(array $gdoVars)
	{
		$class = self::gdoClassNameS();
		$instance = new $class;
		$instance->gdoVars = $gdoVars;
		return $instance;
	}
	
	/**
	 * raw initial string data.
	 * @param array $initial
	 * @return array
	 */
	public static function blankData(array $initial = null)
	{
		$table = self::table();
		$gdoVars = [];
		foreach ($table->gdoColumnsCache() as $column)
		{
		    /** @var $column \GDO\Core\GDT **/
			if ($data = $column->blankData())
			{
				$gdoVars = array_merge($gdoVars, $data);
			}
		}
		
		if ($initial)
		{
			# Merge only existing keys
			$gdoVars = array_intersect_key($initial, $gdoVars) + $gdoVars;
		}
		return $gdoVars;
	}
	
	/**
	 * @return self
	 */
	public static function blank(array $initial = null)
	{
		return self::entity(self::blankData($initial))->dirty();
	}
	
	public function dirty($dirty=true)
	{
		$this->dirty = $dirty;
		return $this;
	}
	
	##############
	### Get ID ###
	##############
	public function getID()
	{
	    $id = '';
		foreach ($this->gdoPrimaryKeyColumns() as $name => $column)
		{
			$id2 = $this->getVar($name);
			$id .= $id ? "{$id}:$id2" : $id2;
		}
		return $id;
	}
	
	public function displayName()
	{
		return $this->gdoClassName() . "#" . $this->getID();
	}
	
	##############
	### Get by ###
	##############
	/**
	 * Get a row by a single arbritary column value.
	 * @param string $key
	 * @param string $value
	 * @return self
	 */
	public static function getBy($key, $value=null)
	{
		return self::table()->findWhere(self::quoteIdentifierS($key) . '=' . self::quoteS($value));
	}
	
	public static function getByVars(array $vars)
	{
		$query = self::table()->select();
		foreach ($vars as $key => $value)
		{
			$query->where(self::quoteIdentifierS($key) . '=' . self::quoteS($value));
		}
		return $query->first()->exec()->fetchObject();
	}
	
	/**
	 * Get a row by auto inc column.
	 * @param string $id
	 * @return self
	 */
	public static function getById(...$id)
	{
		$table = self::table();
		if ( (!$table->cache) || (!($object = $table->cache->findCached(...$id))) )
		{
			$i = 0;
			$query = $table->select();
			foreach ($table->gdoPrimaryKeyColumns() as $column)
			{
				$query->where($column->identifier() . '=' . self::quoteS($id[$i++]));
			}
			$object = $query->first()->exec()->fetchObject();
		}
		return $object;
	}
	
	/**
	 * @param string ...$id
	 * @return self
	 */
	public static function findById(...$id)
	{
		if ($object = self::getById(...$id))
		{
			return $object;
		}
		self::notFoundException(implode(':', $id));
	}
	
	public static function findByGID($id)
	{
		return self::findById(...explode(':', $id));
	}
	
	public static function notFoundException($id)
	{
		throw new GDOError('err_gdo_not_found', [self::table()->gdoHumanName(), $id]);
	}
	
	/**
	 * Fetch from result set as this table.
	 * @param Result $result
	 * @return self
	 */
	public function fetch(Result $result)
	{
		return $result->fetchAs($this);
	}
	
	#############
	### Cache ###
	#############
	public function __wakeup() { $this->table(); }
	/**
	 * @var \GDO\DB\Cache
	 */
	public $cache;
	public function initCache() { $this->cache = new Cache($this); }
	public function initCached(array $row)
	{
		return $this->memCached() ? $this->cache->initGDOMemcached($row) : $this->cache->initCached($row);
	}
	public function gkey()
	{
		return $this->gdoClassName() . $this->getID();
	}
	public function recache()
	{
		if ($this->table()->cache)
		{
			$this->table()->cache->recache($this);
		}
	}
	
	public function uncache()
	{
		if ($this->gdoCached())
		{
			$this->table()->cache->uncache($this);
		}
	}
	
	/**
	 * @return self[]
	 */
	public function all()
	{
		return self::allWhere();
	}
	
	/**
	 * @return self[]
	 */
	public function allWhere($condition='true')
	{
		return self::table()->select()->where($condition)->exec()->fetchAllArray2dObject();
	}
	
	###########################
	###  Table manipulation ###
	###########################
	/**
	 * @param string $className
	 * @return self
	 */
	public static function tableFor($className) { return Database::tableS($className); }
	public static function table() { return self::tableFor(get_called_class()); }
	
	public function createTable() { return Database::instance()->createTable($this); }
	public function dropTable() { return Database::instance()->dropTable($this); }
	public function truncate() { return Database::instance()->truncateTable($this); }
	
	/**
	 * @return \GDO\Core\GDT[]
	 */
	public function gdoColumnsCache() { return Database::columnsS($this->gdoClassName()); }
	
	/**
	 * @return \GDO\Core\GDT[]
	 */
	public function getGDOColumns(array $names)
	{
		$columns = [];
		foreach ($names as $key)
		{
			$columns[$key] = $this->gdoColumn($key);
		}
		return $columns;
	}
	
	##############
	### Events ###
	##############
	private function beforeUpdate(Query $query)
	{
		foreach ($this->gdoColumnsCache() as $gdoType)
		{
			$gdoType->gdo($this)->gdoBeforeUpdate($query);
		}
	}
	
	private function afterCreate()
	{
		# Flags
		$this->dirty = false;
		$this->persisted = true;
		# Trigger event for AutoCol, EditedAt, EditedBy, etc.
		foreach ($this->gdoColumnsCache() as $gdoType)
		{
			$gdoType->gdo($this)->gdoAfterCreate();
		}
		$this->gdoAfterCreate();
	}
	
	# Overrides
	public function gdoAfterCreate() {}
	public function gdoAfterUpdate()
	{
		if ($this->gdoCached() || $this->memCached())
		{
			GDT_Hook::call('CacheInvalidate', $this->gdoClassName(), $this->getID());
		}
	}
	public function gdoAfterDelete() {}
	
	public function gdoHashcode()
	{
		return self::gdoHashcodeS($this->gdoVars);
	}
	
	public static function gdoHashcodeS(array $gdoVars)
	{
		return substr(md5(md5(md5(str_repeat(GWF_SALT, 4)).json_encode(array_values($gdoVars)))), 0, 16);
	}
	
	##############
	### Render ###
	##############
	/**
	 * Create a response from card rendering.
	 * @return \GDO\Core\GDT_Response
	 */
	public function responseCard() { return GDT_Response::makeWithHTML($this->renderCard()); }
	###############
	### Sorting ###
	###############
	/**
	 * Sort GDO[] by a field.
	 * @param GDO[] $array
	 * @param string $columnName
	 * @param bool $ascending
	 */
	public function sort(array &$array, $columnName, $ascending=true)
	{
		return $this->gdoColumn($columnName)->sort($array, $ascending);
	}
	
	#######################
	### Bulk Operations ###
	#######################
	/**
	 * Mass insertion.
	 * @param GDT[] $fields
	 * @param array $data
	 */
	public static function bulkReplace(array $fields, array $data, $chunkSize=100)
	{
		self::bulkInsert($fields, $data, $chunkSize, 'REPLACE');
	}
	public static function bulkInsert(array $fields, array $data, $chunkSize=100, $insert='INSERT')
	{
		foreach (array_chunk($data, $chunkSize) as $chunk)
		{
			self::_bulkInsert($fields, $chunk, $insert);
		}
	}
	private static function _bulkInsert(array $fields, array $data, $insert='INSERT')
	{
		$names = [];
		$table = self::table();
		foreach ($fields as $field)
		{
			$names[] = $field->name;
		}
		$names = implode('`, `', $names);
		
		$values = [];
		foreach ($data as $row)
		{
			$brow = [];
			foreach ($row as $col)
			{
				$brow[] = self::quoteS($col);
			}
			$values[] = implode(',', $brow);
		}
		$values = implode('), (', $values);
		
		$query = "$insert INTO {$table->gdoTableIdentifier()} (`$names`) VALUES ($values)";
		Database::instance()->queryWrite($query);
	}
	
}
