<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\DB\GDT_AutoInc;
use GDO\DB\Query;
use GDO\DB\Result;
use GDO\DB\GDT_Int;
use GDO\DB\GDT_String;
use GDO\DB\GDT_Enum;
use GDO\DB\GDT_Name;
use GDO\Language\Trans;

/**
 * - GDO -
 * 
 * A GDO is a container for GDTs, which values are backed by a database and caches.
 * Values are stored in the $gdoVars array.
 * When a GDT column is used, the $gdoVars are copied into the GDT,
 * which make this framework tick fast with a low memory footprint.
 * It safes memory to only keep the GDTs once per Table.
 * Please note that almost all vars are considered string in GDO6. 
 * 
 * @see GDT
 * @see Cache
 * @see Database
 * @see Query
 * 
 * @author gizmore@wechall.net
 * @version 6.10.1
 * @since 3.2.0
 * @license MIT
 */
abstract class GDO
{
    use WithName;

    const MYISAM = 'myisam'; # Faster writes
    const INNODB = 'innodb'; # Foreign keys
    const MEMORY = 'memory'; # Temp tables @TODO not working? => remove
    
    /**
     * Override this function to create GDO database columns for your GDO.
     * Any GDT can be used, most of them support table creation code via GDT_Int, GDT_String or GDT_Enum.
     * @see GDT_Int
     * @see GDT_String
     * @see GDT_Enum
     * @return GDT[]
     */
    public abstract function gdoColumns();
    
    public function gdoCached() { return true; }
    public function memCached() { return $this->gdoCached(); }
    public function cached() { return $this->gdoCached() || (GDO_MEMCACHE && $this->memCached()); }
    
    public function gdoTableName() { return self::table()->cache->tableName; }
    public function gdoDependencies() { return null; }
    
    public function gdoEngine() { return self::INNODB; } # @see self::MYISAM
    public function gdoAbstract() { return false; }
    public function gdoIsTable() { return true; }
    public function gdoTableIdentifier() { return $this->gdoTableName(); }
    
    ################
    ### Escaping ###
    ################
    public static function escapeIdentifierS($identifier) { return str_replace("`", "\\`", $identifier); }
    public static function quoteIdentifierS($identifier) { return "`" . self::escapeIdentifierS($identifier) . "`"; }
    public static function escapeSearchS($var) { return str_replace(['%', "'", '"'], ['\\%', "\\'", '\\"'], $var); }
    public static function escapeS($var) { return str_replace(['\\', "'", '"'], ['\\\\', '\\\'', '\\"'], $var); }
    public static function quoteS($var)
    {
        if (is_string($var))
        {
            return sprintf('"%s"', self::escapeS($var));
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
    ### Construct ###
    #################
    public static $COUNT = 0;
    public function __construct()
    {
        self::$COUNT++;
    }
    
    public function __wakeup()
    {
        self::$COUNT++;
        $this->recache = false;
    }
    
    #################
    ### Persisted ###
    #################
    private $persisted = false;
    public function isPersisted() { return $this->persisted; }
    public function setPersisted($persisted = true) { $this->persisted = $persisted; return $this; }
    
    public $isTable = false;
    public function isTable() { return $this->isTable; }
    
    ########################
    ### Custom temp vars ###
    ########################
    /**
     * @var mixed[]
     */
    public $temp = null;
    public function tempReset() { $this->temp = null; }
    public function tempGet($key) { return @$this->temp[$key]; }
    public function tempSet($key, $value) { if (!isset($this->temp)) $this->temp = []; $this->temp[$key] = $value; return $this; }
    public function tempUnset($key) { unset($this->temp[$key]); return $this; }
    public function tempHas($key) { return isset($this->temp[$key]); }
    
    ##############
    ### Render ###
    ##############
    public function display($key)
    {
        return html($this->gdoVars[$key]);
    }
    
    public function renderChoice()
    {
        return $this->displayName();
    }
    
    public function renderJSON()
    {
        return $this->toJSON();
    }
    
    public function toJSON()
    {
        $values = [];
        foreach ($this->gdoColumnsCache() as $gdoType)
        {
            if ($gdoType->isSerializable())
            {
                if ($data = $gdoType->gdo($this)->getGDOData())
                {
                    foreach ($data as $k => $v)
                    {
                        $values[$k] = $v;
                    }
                }
            }
        }
        return $values;
    }
    
    ############
    ### Vars ###
    ############
    
    /**
     * Mark vars as dirty.
     * Either true for all, false for none, or an assoc array with field mappings.
     * @var boolean,boolean[]
     */
    private $dirty = false;

    /**
     * Entity gdt vars.
     * @var string[]
     */
    private $gdoVars;
    
    public function &getGDOVars() { return $this->gdoVars; }
    
    /**
     * @param string $key
     * @return bool
     */
    public function hasVar($key)
    {
        return array_key_exists($key, $this->gdoVars);
    }
    
    public function hasColumn($key)
    {
        return array_key_exists($key, $this->gdoColumnsCache());
    }
    
    /**
     * @param string $key
     * @return string
     */
    public function getVar($key)
    {
        return @$this->gdoVars[$key];
    }
    
    /**
     * @param string $key
     * @param string $var
     * @param boolean $markDirty
     * @return self
     */
    public function setVar($key, $var, $markDirty=true)
    {
        # @TODO: Better use temp? @see Vote/Up
        if (!$this->hasColumn($key))
        {
            $this->gdoVars[$key] = $var;
            return $this;
        }
        
        $gdt = $this->gdoColumn($key)->var($var);
        $d = false;
        if ($data = $gdt->getGDOData())
        {
            foreach ($data as $k => $v)
            {
                if ($this->gdoVars[$k] !== $v)
                {
                    $this->gdoVars[$k] = $v === null ? null : (string)$v;
                    $d = true;
                }
            }
        }
        return $markDirty && $d ? $this->markDirty($key) : $this;
    }
    
    public function setVars(array $vars=null, $markDirty=true)
    {
        foreach ($vars as $key => $value)
        {
            $this->setVar($key, $value, $markDirty);
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
        return is_bool($this->dirty) ? $this->dirty : count($this->dirty) > 0;
    }
    
    /**
     * Get gdoVars that have been changed.
     * @return string[]
     */
    public function getDirtyVars()
    {
        if ($this->dirty === true)
        {
            $vars = [];
            foreach ($this->gdoColumnsCache() as $gdt)
            {
                if ($data = $gdt->gdo($this)->getGDOData())
                {
                    foreach ($data as $k => $v)
                    {
                        $vars[$k] = $v;
                    }
                }
            }
            return $vars;
        }
        elseif ($this->dirty === false)
        {
            return [];
        }
        else
        {
            $vars = [];
            foreach ($this->dirty as $name)
            {
                if ($data = $this->gdoColumn($name)->getGDOData())
                {
                    foreach ($data as $k => $v)
                    {
                        $vars[$k] = $v;
                    }
                }
            }
            return $vars;
        }
    }
    
    ###############
    ### Columns ###
    ###############
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
     * Get the primary key columns for a table.
     * @return \GDO\Core\GDT[]
     */
    public function gdoPrimaryKeyColumns()
    {
        $cache = self::table()->cache;
        
        if (isset($cache->pkColumns))
        {
            return $cache->pkColumns;
        }
        
        $columns = [];
        foreach ($this->gdoColumnsCache() as $column)
        {
            if ($column->isPrimary())
            {
                $columns[$column->name] = $column;
            }
            else
            {
                break; # early break is possible because we start all tables with their PKs.
            }
        }
        
        $cache->pkColumns = $columns;
        
        return $columns;
    }
    
    public function gdoPrimaryKeyValues()
    {
        $values = [];
        foreach ($this->gdoPrimaryKeyColumns() as $gdt)
        {
            $values[$gdt->name] = $this->getVar($gdt->name);
        }
        return $values;
    }
    
    /**
     * Get primary key column names.
     * @return string[]
     */
    public function gdoPrimaryKeyColumnNames()
    {
        $cache = self::table()->cache;
        
        if (isset($cache->pkNames))
        {
            return $cache->pkNames;
        }
        
        $names = [];
        foreach ($this->gdoColumnsCache() as $column)
        {
            if ($column->isPrimary())
            {
                $names[] = $column->name;
            }
            else
            {
                break; # Assume PKs are first until no more PKs
            }
        }
        
        $cache->pkNames = $names;
        
        return $names;
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
            if (is_a($gdoType, $className, true))
            {
                return $gdoType->gdo($this);
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
    public function gdoAutoIncColumn() { return $this->gdoColumnOf(GDT_AutoInc::class); }
    
    /**
     * Get the GDOs name identifier column, if any.
     * @return \GDO\DB\GDT_Name
     */
    public function gdoNameColumn() { return $this->gdoColumnOf(GDT_Name::class); }
    
    /**
     * Get the GDT column for a key.
     * @param string $key
     * @return GDT
     */
    public function gdoColumn($key, $throw=true)
    {
        /** @var $gdt GDT **/
        if ($gdt = $this->gdoColumnsCache()[$key])
        {
            return $gdt->gdo($this);
        }
        elseif ($throw)
        {
            throw new GDOError('err_unknown_gdo_column', [html($key)]);
        }
    }
    
    /**
     * Get a copy of a GDT column.
     * @deprecated avoid!
     * @param string $key
     * @return GDT
     */
    public function gdoColumnCopy($key)
    {
        /** @var $column GDT **/
        $column = clone $this->gdoColumnsCache()[$key];
        return $column->gdo($this);#->var($column->initial);
    }
    
    /**
     * Get all GDT columns except those listed.
     * @param string[] ...$except
     * @return GDT[]
     */
    public function gdoColumnsExcept(...$except)
    {
        $columns = array();
        foreach (array_keys($this->gdoColumnsCache()) as $key)
        {
            if (!in_array($key, $except, true))
            {
                $columns[$key] = $this->gdoColumn($key);
            }
        }
        return $columns;
    }
    
    /**
     * Get a copy of all GDT columns except those listed.
     * @deprecated avoid!
     * @param string[] ...$except
     * @return GDT[]
     */
    public function gdoColumnsCopyExcept(...$except)
    {
        $columns = array();
        foreach (array_keys($this->gdoColumnsCache()) as $key)
        {
            if (!in_array($key, $except, true))
            {
                $columns[$key] = $this->gdoColumnCopy($key);
            }
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
        return new Query(self::table());
    }
    
    /**
     * Find a row by AutoInc Id.
     * @param string $id
     * @return static
     */
    public function find($id=null, $exception=true)
    {
        if ($id && ($gdo = $this->getById($id)))
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
     * @return string
     */
    public function countWhere($condition='true')
    {
        return $this->select('COUNT(*)', false)->where($condition)->
          noOrder()->exec()->fetchValue();
    }
    
    /**
     * Find a row by condition. Throws GDO::notFoundException.
     * @param string $where
     * @return self
     */
    public function findWhere($condition)
    {
        if (!($gdo = $this->getWhere($condition)))
        {
            self::notFoundException(html($condition));
        }
        return $gdo;
    }
    
    /**
     * Get a row by condition.
     * @param string $condition
     * @return self
     */
    public function getWhere($condition)
    {
        return $this->select()->where($condition)->
            first()->exec()->fetchObject();
    }
    
    /**
     * @param string $columns
     * @return \GDO\DB\Query
     */
    public function select($columns='*', $withHooks=true)
    {
        $query = $this->query()->select($columns)->from($this->gdoTableIdentifier());
        if ($withHooks)
        {
            $this->beforeRead($query);
        }
        return $query;
    }
    
    public function delete()
    {
        return $this->deleteB();
    }
    
    /**
     * Delete multiple rows, but still one by one to trigger all events correctly.
     * @param string $condition
     * @return \GDO\DB\Query
     */
    public function deleteWhere($condition)
    {
        $deleted = 0;
        $result = $this->table()->select()->where($condition)->exec();
        while ($gdo = $result->fetchObject())
        {
            $deleted++;
            $gdo->deleteB();
        }
        return $deleted;
    }
    
    private function deleteB()
    {
        if ($this->persisted)
        {
            $query = $this->query()->delete($this->gdoTableIdentifier())->where($this->getPKWhere());
            $this->beforeDelete($query);
            $query->exec();
            $this->afterDelete();
            $this->persisted = false;
            $this->uncache();
        }
        return $this;
        
    }
    
    public function replace($withHooks=true)
    {
        $id = $this->getID();
        if ( (!$id) || preg_match('#^[:0]+$#D', $id) )
        {
            return $this->insert();
        }
        $query = $this->query()->replace($this->gdoTableIdentifier())->values($this->gdoPrimaryKeyValues())->values($this->getDirtyVars());
        return $this->insertOrReplace($query, $withHooks);
    }
    
    public function insert($withHooks=true)
    {
        $query = $this->query()->insert($this->gdoTableIdentifier())->values($this->getDirtyVars());
        return $this->insertOrReplace($query, $withHooks);
    }
    
    private function insertOrReplace(Query $query, $withHooks)
    {
        if ($withHooks)
        {
            $this->beforeCreate($query);
        }
        $query->exec();
        $this->dirty = false;
        $this->persisted = true;
        if ($withHooks)
        {
            $this->afterCreate();
//             $this->cache(); # not needed for new rows?
        }
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
    
    /**
     * @return self
     */
    public function save($withHooks=true)
    {
        if (!$this->persisted)
        {
            return $this->insert();
        }
//         if ($this->isDirty())
//         {
            if ($setClause = $this->getSetClause())
            {
                $query = $this->updateQuery()->set($setClause);
                
                if ($withHooks)
                {
                    $this->beforeUpdate($query);
                }
                
                $query->exec();
                $this->dirty = false;
                
                if ($withHooks)
                {
                    $this->recache(); # save is the only action where we recache!
                }

                if ($withHooks)
                {
                    $this->afterUpdate();
                }
            }
//         }
        return $this;
    }
    
    public function increase($key, $by=1)
    {
        return $by === 0 ? $this : $this->saveVar($key, $this->getVar($key)+$by);
    }
    
    public function saveVar($key, $var, $withHooks=true, &$worthy=false)
    {
        return $this->saveVars([$key => $var], $withHooks, $worthy);
    }
    
    /**
     * @param array $vars
     * @param boolean $withHooks
     * @param boolean $worthy
     * @return \GDO\Core\GDO
     */
    public function saveVars(array $vars, $withHooks=true, &$worthy=false)
    {
        $worthy = false; # Anything changed?
        $query = $this->updateQuery();
        foreach ($vars as $key => $var)
        {
            if (array_key_exists($key, $this->gdoVars))
            {
                if ($var !== $this->gdoVars[$key])
                {
                    $query->set(sprintf("%s=%s", self::quoteIdentifierS($key), self::quoteS($var)));
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
            foreach ($vars as $key => $var)
            {
                $this->gdoVars[$key] = $var;
            }
            if ($withHooks)
            {
                $this->recache();
            }
        }

        # Call hooks even when not needed. Because its needed on GDT_Files
        if ($withHooks) $this->afterUpdate();
        
        return $this;
    }
    
    public function saveValue($key, $value, $withHooks=true)
    {
        $var = $this->gdoColumn($key)->toVar($value);
        return $this->saveVar($key, $var, $withHooks);
    }
    
    public function saveValues(array $values, $withHooks=true)
    {
        $vars = [];
        foreach ($values as $key => $value)
        {
            $this->gdoColumn($key)->setGDOValue($value);
            $vars[$key] = $this->getVar($key);
        }
        return $this->saveVars($vars, $withHooks);
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
                if (!$column->virtual)
                {
                    if ( ($this->dirty === true) || (isset($this->dirty[$column->name])) )
                    {
                        foreach ($column->gdo($this)->getGDOData() as $k => $v)
                        {
                            if ($setClause !== '')
                            {
                                $setClause .= ',';
                            }
                            $setClause .= $k . '=' . self::quoteS($v);
                        }
                    }
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
        $instance = new $class();
        $instance->gdoVars = $gdoVars;
        return $instance;
    }
    
    /**
     * Raw initial string data.
     * @param array $initial data to copy
     * @return array the new blank data1
     */
    public static function blankData(array $initial = null)
    {
        $table = self::table();
        $gdoVars = [];
        foreach ($table->gdoColumnsCache() as $column)
        {
            # init gdt with initial var.
            if (isset($initial[$column->name]))
            {
                $column->var($initial[$column->name]);
            }
            else
            {
                $column->var($column->initial);
            }
            
            # loop over blank data
            if ($data = $column->blankData())
            {
                foreach ($data as $k => $v)
                {
                    if (isset($initial[$k]))
                    {
                        # override with initial
                        $gdoVars[$k] = $initial[$k];
                    }
                    else
                    {
                        # Use blank data as is
                        $gdoVars[$k] = $v;
                    }
                }
            }
        }
        return $gdoVars;
    }
    
    /**
     * Create a new entity instance.
     * @return self
     */
    public static function blank(array $initial = null)
    {
        return self::entity(self::blankData($initial))->dirty()->setPersisted(false);
    }
    
    public function dirty($dirty=true)
    {
        $this->dirty = $dirty;
        return $this;
    }
    
    ##############
    ### Get ID ###
    ##############
    /**
     * Id cache
     * @var $id string
     */
    public function getID()
    {
        $id = '';
        foreach ($this->gdoPrimaryKeyColumnNames() as $name)
        {
            $id2 = $this->getVar($name);
            $id = $id ? "{$id}:{$id2}" : $id2;
        }
        return $id;
    }
    
    /**
     * Display a translated table name with ID.
     * @see Trans
     * @return string
     */
    public function displayName()
    {
        return $this->gdoHumanName() . "#" . $this->getID();
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
    public static function getBy($key, $var)
    {
        return self::table()->getWhere(self::quoteIdentifierS($key) . '=' . self::quoteS($var));
    }
    
    /**
     * Get a row by a single column value.
     * Throw exception if not found.
     * @param string $key
     * @param string $value
     * @return self
     */
    public static function findBy($key, $var)
    {
        if (!($gdo = self::getBy($key, $var)))
        {
            self::notFoundException($var);
        }
        return $gdo;
    }
    
    /**
     * @param array $vars
     * @return self
     */
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
     * @param string ...$id
     * @return self
     */
    public static function getById(...$id)
    {
        $table = self::table();
        if ( (!$table->cached()) || (!($object = $table->cache->findCached(...$id))) )
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
        throw new GDOError('err_gdo_not_found', [self::table()->gdoHumanName(), html($id)]);
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
    /**
     * @var \GDO\DB\Cache
     */
    public $cache;
    
    public function initCache() { $this->cache = new Cache($this); }
    
    public function initCached(array $row)
    {
        return GDO_MEMCACHE && $this->memCached() ? 
           $this->cache->initGDOMemcached($row) :
           $this->cache->initCached($row);
    }
    
    public function gkey()
    {
        return self::table()->cache->tableName . $this->getID();
    }
    
    public function reload($id)
    {
        $table = self::table();
        if ($table->cached() && $table->cache->hasID($id))
        {
            $i = 0;
            $id = explode(':', $id);
            $query = $this->select();
            foreach ($this->gdoPrimaryKeyColumns() as $column)
            {
                $query->where($column->identifier() . '=' . self::quoteS($id[$i++]));
            }
            $object = $query->uncached()->first()->exec()->fetchObject();
            return $object ? $table->cache->recache($object) : null;
        }
    }
    
    /**
     * This function triggers a recache, also over IPC, if IPC is enabled.
     */
    public function recache()
    {
        if ($this->cached())
        {
            self::table()->cache->recache($this);
        }
    }
    
    public function recacheMemcached()
    {
        if (GDO_MEMCACHE && $this->memCached())
        {
            $this->table()->cache->recache($this);
        }
    }
    
    public $recache = false;
    public function recaching()
    {
        $this->recache = true;
        return $this;
    }
    
    public function cache()
    {
        if ($this->cached())
        {
            self::table()->cache->recache($this);
        }
    }
    
    /**
     * @deprecated Untested and why does it exist?
     */
    public function uncache()
    {
        if ($this->table()->cache)
        {
            $this->table()->cache->uncache($this);
        }
    }
    
    public function clearCache()
    {
        if ($this->cached())
        {
            $cache = self::table()->cache;
            $cache->clearCache();
            Cache::flush(); # @TODO Find a way to only remove memcached entries for this single GDO.
        }
        return $this;
    }

    ###########
    ### All ###
    ###########
    /**
     * @return self[]
     */
    public function &all($order=null, $asc=true)
    {
        $order = $order ? $order : $this->gdoPrimaryKeyColumn()->name;
        return self::allWhere('true', $order, $asc);
    }
    
    /**
     * @return self[]
     */
    public function &allWhere($condition='true', $order=null, $asc=true)
    {
        return self::table()->select()->
            where($condition)->order($order, $asc)->
            exec()->fetchAllArray2dObject();
    }
    
    public function uncacheAll()
    {
        $cache = self::table()->cache;
        $cache->all = null;
        Cache::remove($this->cacheAllKey());
        return $this;
    }
    
    public function cacheAllKey()
    {
        return 'all_' . $this->gdoTableName();
    }
    
    /**
     * Get all rows via allcache.
     * @param string $order
     * @param boolean $asc
     * @return self[]
     */
    public function &allCached($order=null, $asc=true)
    {
        if ($this->cached())
        {
            # Already cached
            $cache = self::table()->cache;
            if (isset($cache->all))
            {
                return $cache->all;
            }
        }
        else
        {
            # No caching at all
            return $this->allWhere('true', $order, $asc);
        }
        
        if (!$this->memCached())
        {
            # Memcached
            $all = $this->allWhere('true', $order, $asc);
            $cache->all = $all;
            return $all;
        }
        else
        {
            # GDO cached
            $key = $this->cacheAllKey();
            if (false === ($all = Cache::get($key)))
            {
                $all = $this->allWhere('true', $order, $asc);
                Cache::set($key, $all);
                $cache->all = $all;
            }
            return $all;
        }
    }
    
    public function removeAllCache()
    {
        $key = 'all_' . $this->gdoTableName();
        Cache::remove($key);
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
    public static function table() { return Database::tableS(static::class); }
    
    public function createTable($reinstall=false) { return Database::instance()->createTable($this, $reinstall); }
    public function dropTable() { return Database::instance()->dropTable($this); }
    public function truncate() { return Database::instance()->truncateTable($this); }
    
    /**
     * @return GDT[]
     */
    public function &gdoColumnsCache() { return Database::columnsS(static::class); }
    
    /**
     * @return GDT[]
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
    
    private function beforeRead(Query $query)
    {
        foreach ($this->gdoColumnsCache() as $gdoType)
        {
            $gdoType->gdo($this)->gdoBeforeRead($query);
        }
        $this->gdoBeforeRead();
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

    public function afterCreate()
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
    
    public function afterRead()
    {
        foreach ($this->gdoColumnsCache() as $gdoType)
        {
            $gdoType->gdo($this)->gdoAfterRead();
        }
        $this->gdoAfterRead();
    }
    
    public function afterUpdate()
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
    
    public function afterDelete()
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
    public function gdoBeforeRead() {}
    public function gdoBeforeUpdate() {}
    public function gdoBeforeDelete() {}
    
    public function gdoAfterCreate() {}
    public function gdoAfterRead() {}
    public function gdoAfterUpdate() {}
    public function gdoAfterDelete() {}
    
    ################
    ### Hashcode ###
    ################
    /**
     * Generate a hashcode from gdo vars.
     * This is often used in approval tokens or similar.
     * @return string
     */
    public function gdoHashcode()
    {
        return self::gdoHashcodeS($this->gdoVars);
    }
    
    /**
     * Generate a hashcode from an associative array.
     * @param array $gdoVars
     * @return string
     */
    public static function gdoHashcodeS(array $gdoVars)
    {
//         ksort($gdoVars); # Ensure order of vars stay the same.
        return substr(
            md5(str_repeat(GDO_SALT, 3).
                json_encode($gdoVars).
                str_repeat(GDO_SALT, 3)), 0, 16);
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
    
    #############
    ### Order ###
    #############
    public function getDefaultOrder()
    {
        foreach ($this->gdoColumnsCache() as $gdt)
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
    
    ############
    ### Lock ###
    ############
    public function lock($lock, $timeout=10)
    {
        $result = Database::instance()->lock($lock, $timeout);
        return mysqli_fetch_field($result) === '1';
    }
    
    public function unlock($lock)
    {
        $result = Database::instance()->unlock($lock);
        return mysqli_fetch_field($result) === '1';
    }
    
}
