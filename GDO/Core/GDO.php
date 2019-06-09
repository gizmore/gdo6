<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\DB\Query;
use GDO\DB\Result;

/**
 * A GDO is a container for GDTs, which values are backed by a database and cache.
 * 
 * @see GDT
 * @see Cache
 * @see Database
 * 
 * @author gizmore@wechall.net
 * @version 6.08
 * @since 1.00
 */
abstract class GDO
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
	public function gdoIsTable() { return true; }
	public function gdoTableIdentifier() { return self::quoteIdentifierS($this->gdoTableName()); }

	################
	### Escaping ###
	################
	public static function escapeIdentifierS($identifier) { return str_replace("`", "\`", $identifier); }
	public static function quoteIdentifierS($identifier) { return "`" . self::escapeIdentifierS($identifier) . "`"; }
	public static function escapeSearchS($var) { return str_replace(['%', "'", '"'], ['\\%', "\\'", '\\"'], $var); }
	
	public static function escapeS($var) { return str_replace(['\\', "'", '"'], ['\\\\', "\\'", '\\"'], $var); }
	public static function quoteS($var)
	{
		if (is_string($var))
		{
			return "'" . self::escapeS($var) . "'";
		}
		elseif ($var === null)
		{
			return "NULL";
		}
		elseif (is_numeric($var))
		{
			return "$var";
		}
		elseif (is_bool($var))
		{
			return $var ? '1' : '0';
		}
		else
		{
			throw new GDOError('err_cannot_quote', [html($var)]);
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
		foreach ($this->gdoColumnsCache() as $gdoType)
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
		return @array_key_exists($key, $this->gdoVars);
	}
	
	/**
	 * @param string $key
	 * @return string
	 */
	public function getVar($key)
	{
		return @$this->gdoVars[$key];
	}
	
	public function setVar($key, $value, $markDirty=true)
	{
		$this->gdoVars[$key] = $value === null ? null : (string)$value;
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
				break; # XXX: early break is only possible because we start all tables with their PKs.
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
		foreach ($this->gdoColumnsCache() as $gdoType)
		{
			if (is_a($gdoType, $className))
			{
				return $gdoType;
			}
		}
	}
	
	public function gdoVarOf($className)
	{
		return $this->getVar($this->gdoColumnOf($className)->name);
	}
	
	public function gdoValueOf($className)
	{
		return $this->getValue($this->gdoColumnOf($className)->name);
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
		$column = #clone $this->gdoColumnsCache()[$key]; # XXX: performance over weird bugs?
		$column = $this->gdoColumnsCache()[$key];
		return $column->gdo($this);
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
	 * @return \GDO\DB\Query
	 */
	public function query()
	{
		return new Query(self::tableFor($this->gdoClassName()));
	}
	
	/**
	 * Find a row by AutoInc Id.
	 * @param string $id
	 * @return self
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
	 * @return \GDO\DB\Query
	 */
	public function select($columns=null)
	{
		return $this->query()->select($columns)->from($this->gdoTableIdentifier());
	}
	
	/**
	 * @param string $condition
	 * @return \GDO\DB\Query
	 * @deprecated Better delete one by one with events!
	 */
	public function deleteWhere($condition)
	{
		return $this->query()->delete($this->gdoTableIdentifier())->where($condition);
	}
	
	public function delete()
	{
		if ($this->persisted)
		{
			$query = $this->deleteWhere($this->getPKWhere());
			$this->beforeDelete($query);
			$query->exec();
			$this->afterDelete();
			$this->uncache();
		}
		return $this;
	}
	
	public function replace()
	{
		if (empty($this->getID()))
		{
			return $this->insert();
		}
		$query = $this->query()->replace($this->gdoTableIdentifier())->values($this->gdoVars);
		return $this->insertOrReplace($query);
	}
	
	public function insert()
	{
		$query = $this->query()->insert($this->gdoTableIdentifier())->values($this->getDirtyVars());
		return $this->insertOrReplace($query);
	}
	
	private function insertOrReplace(Query $query)
	{
		$this->beforeCreate($query);
		$query->exec();
		$this->dirty = false;
		$this->persisted = true;
		$this->afterCreate();
		$this->recache();
		$this->callRecacheHook();
		return $this;
	}
	
	/**
	 * Build a generic update query for the whole table.
	 * @return Query
	 */
	public function update()
	{
		return $this->query()->update($this->gdoTableIdentifier());
	}
	
	/**
	 * Build an entity update query.
	 * @return Query
	 */
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
			if ($setClause = $this->getSetClause())
			{
				$query = $this->updateQuery()->set($setClause);
				$this->beforeUpdate($query);
				$query->exec();
				$this->dirty = false;
				$this->recache(); # save is the only action where we recache!
				$this->callRecacheHook();
				$this->gdoAfterUpdate();
			}
			$this->dirty = false;
		}
		return $this;
	}
	
	public function increase($key, $by=1)
	{
		return $by === 0 ? $this : $this->saveVar($key, $this->getVar($key)+$by);
	}
	
	public function saveVar($key, $value, $withHooks=true, &$worthy=false)
	{
		return $this->saveVars([$key => $value], $withHooks, $worthy);
	}
	
	public function saveVars(array $vars, $withHooks=true, &$worthy=false)
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

		# Call hooks even when not needed. Because its needed on GDT_Files
		if ($withHooks) $this->beforeUpdate($query); # Can do trickery here... not needed?

		if ($worthy)
		{
			$query->exec();
			$this->gdoVars = array_merge($this->gdoVars, $vars);
			$this->recache(); # save is the only action where we recache!
			if ($withHooks) $this->callRecacheHook();
		}

		# Call hooks even when not needed. Because its needed on GDT_Files
		if ($withHooks) $this->afterUpdate();
		
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
	 * @return \GDO\DB\Query
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
			$id = $id ? "{$id}:$id2" : $id2;
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
	public static function getBy($key, $value)
	{
		return self::table()->findWhere(self::quoteIdentifierS($key) . '=' . self::quoteS($value));
	}
	
	/**
	 * Get a row by a single column value.
	 * Throw exception if not found.
	 * @param string $key
	 * @param string $value
	 * @return self
	 */
	public static function findBy($key, $value)
	{
		if ($gdo = self::getBy($key, $value))
		{
			return $gdo;
		}
		return self::notFoundException($value);
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
	
	public function fetchAll(Result $result)
	{
		$back = [];
		while ($gdo = $this->fetch($result))
		{
			$back[] = $gdo;
		}
		return $back;
	}
	
	#############
	### Cache ###
	#############
// 	public function __wakeup()
// 	{
// 		#$this->table();
// 	}
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
	
	public function reload($id)
	{
		if ($this->cache && $this->cache->hasID($id))
		{
			$id = explode(':', $id);
			$i = 0;
			$query = $this->select();
			foreach ($this->gdoPrimaryKeyColumns() as $column)
			{
				$query->where($column->identifier() . '=' . self::quoteS($id[$i++]));
			}
			$object = $query->uncached()->first()->exec()->fetchObject();
			return $object ? $this->cache->recache($object) : null;
		}
	}
	
	public function recache()
	{
		if ($this->table()->cache)
		{
			$this->table()->cache->recache($this);
// 			$this->callRecacheHook();
		}
	}
	
	public function uncache()
	{
		if ($this->table()->cache)
		{
			$this->table()->cache->uncache($this);
// 			$this->callRecacheHook();
		}
	}
	
	public function callRecacheHook()
	{
		if ($this->gdoCached() || $this->memCached())
		{
			GDT_Hook::callWithIPC('CacheInvalidate', $this->gdoClassName(), $this->getID());
		}
	}
	
	
	
	###########
	### All ###
	###########
	/**
	 * @return self[]
	 */
	public function all()
	{
		return self::allWhere('true', $this->gdoPrimaryKeyColumn()->name);
	}
	
	/**
	 * @return self[]
	 */
	public function allWhere($condition='true', $order=null, $asc=true)
	{
		return self::table()->select()->where($condition)->order($order, $asc)->exec()->fetchAllArray2dObject();
	}
	
	###########################
	###  Table manipulation ###
	###########################
	/**
	 * @param string $className
	 * @return self
	 */
	public static function tableFor($className) { return Database::tableS($className); }
	
	/**
	 * Return the GDO instance that is used as table struct.
	 * @return self
	 */
	public static function table() { return self::tableFor(get_called_class()); }
	
	public function createTable($reinstall=false) { return Database::instance()->createTable($this, $reinstall); }
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
	private function beforeCreate(Query $query)
	{
		foreach ($this->gdoColumnsCache() as $gdoType)
		{
			$gdoType->gdo($this)->gdoBeforeCreate($query);
		}
		$this->gdoBeforeCreate();
	}
	
	private function beforeUpdate(Query $query)
	{
		foreach ($this->gdoColumnsCache() as $gdoType)
		{
			$gdoType->gdo($this)->gdoBeforeUpdate($query);
		}
		$this->gdoBeforeUpdate();
	}

	private function beforeDelete(Query $query)
	{
		foreach ($this->gdoColumnsCache() as $gdoType)
		{
			$gdoType->gdo($this)->gdoBeforeDelete($query);
		}
		$this->gdoBeforeDelete();
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
	
	private function afterUpdate()
	{
		# Flags
		$this->dirty = false;
		# Trigger event for AutoCol, EditedAt, EditedBy, etc.
		foreach ($this->gdoColumnsCache() as $gdoType)
		{
			$gdoType->gdo($this)->gdoAfterUpdate();
		}
		$this->gdoAfterUpdate();
	}
	
	private function afterDelete()
	{
		# Flags
		$this->dirty = false;
		$this->persisted = false;
		# Trigger events on GDTs.
		foreach ($this->gdoColumnsCache() as $gdoType)
		{
			$gdoType->gdo($this)->gdoAfterDelete();
		}
		$this->gdoAfterDelete();
	}
	
	# Overrides
	public function gdoBeforeCreate() {}
	public function gdoBeforeUpdate() {}
	public function gdoBeforeDelete() {}
	
	public function gdoAfterCreate() {}
	public function gdoAfterUpdate() {}
	public function gdoAfterDelete() {}
	
	################
	### Hashcode ###
	################
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
		$values = implode("),\n(", $values);
		
		$query = "$insert INTO {$table->gdoTableIdentifier()} (`$names`)\n VALUES\n($values)";
		Database::instance()->queryWrite($query);
	}
	
}
