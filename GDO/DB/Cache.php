<?php
namespace GDO\DB;

use GDO\Core\GDO;

/**
 * Cache is a global object cache, where each fetched object (with the same key) from the database results in the same instance.
 * This way you can never have two dangling out of sync users in your application.
 * It also saves a bit mem.
 * Of course this comes with a slight overhead.
 * As GDO6 was written from scratch with this in mind, the overhead is quite small.
 * 
 * Suprising is the additional use of memcached (did not plan this) which adds a second layer of caching.
 * 
 * There are a few global memcached keys scattered across the application, fetching all rows or similiar stuff.
 * Those GDOs usually dont use memcached on a per row basis and gdoMemcached is false.
 * 
 * gdo_modules
 * gdo_country
 * gdo_language
 * 
 * The other memcached keys work on a per row basis with table_name_id as key.
 * 
 * @author gizmore
 * @version 6.10
 * @since 5.0
 * @license MIT
 */
class Cache
{
	private static $MEMCACHED; # Memcached server

	public $all;       # All rows. @see GDO->allCached()
    public $pkNames;   # Primary Key Column Names
    public $pkColumns; # Primary Key Columns
    public $tableName; # Cached transformed table name
	
	/**
	 * @TODO no result should return null?
	 * @param string $key
	 * @return boolean
	 */
	public static function get($key) { return GWF_MEMCACHE ? self::$MEMCACHED->get(GWF_MEMCACHE_PREFIX.$key) : false; }
	public static function set($key, $value) { if (GWF_MEMCACHE) self::$MEMCACHED->set(GWF_MEMCACHE_PREFIX.$key, $value); }
	public static function remove($key) { if (GWF_MEMCACHE) self::$MEMCACHED->delete(GWF_MEMCACHE_PREFIX.$key); }
	public static function flush() { if (GWF_MEMCACHE) { self::$MEMCACHED->flush(); Cache::cooldown(); } }
	public static function init()
	{
		if (GWF_MEMCACHE)
		{
			self::$MEMCACHED = new \Memcached();
			self::$MEMCACHED->addServer(GWF_MEMCACHE_HOST, GWF_MEMCACHE_PORT);
		}
	}
	
	###############
	### Preheat ### (not working)
	###############
// 	private static $HEAT = [];
	/**
	 * Load memcached GDOs into the process cache.
	 * @param string $key
	 * @param GDO[] $objects
	 */
	public static function heat($key, array $objects)
	{
// 	    if (!isset(self::$HEAT[$key]))
// 	    {
// 	        self::$HEAT[$key] = true;
// 	        foreach ($objects as $gdo)
// 	        {
// 	            if (!$gdo->gdoCached())
// 	            {
// 	                return;
// 	            }
// 	            $gdo->table()->cache->recache($gdo);
// 	        }
// 	    }
	}
	
	/**
	 * Re-init heat after a cache flush.
	 */
	public static function cooldown()
	{
// 	    self::$HEAT = [];
	}
	#################
	### GDO Cache ###
	#################
	/**
	 * The table object is fine to keep clean?
	 * @var GDO
	 */
	private $table;
	
	/**
	 * Zero alloc, one item dummy queue.
	 * @var GDO
	 */
	private $dummy;
	
	private $klass;
	
	/**
	 * The cache
	 * @var GDO[]
	 */
	private $cache = [];

	public function __construct(GDO $gdo)
	{
		$this->table = $gdo;
		$this->klass = $gdo->gdoClassName();
		$this->tableName = strtolower($gdo->gdoShortName());
		$this->newDummy();
	}

	private function newDummy()
	{
		$this->dummy = new $this->klass();
	}
	
	/**
	 * Try GDO Cache and Memcached.
	 * @param string $id
	 * @return GDO
	 */
	public function findCached(...$ids)
	{
		$id = implode(':', $ids);
		if (!isset($this->cache[$id]))
		{
			if ($mcached = self::get($this->tableName . $id))
			{
				$this->cache[$id] = $mcached;
			}
		}
		return isset($this->cache[$id]) ? $this->cache[$id] : null;
	}
	
	public function hasID($id)
	{
		return isset($this->cache[$id]);
	}
	
	/**
	 * Only GDO Cache / No memcached initializer.
	 * @param array $assoc
	 * @return GDO
	 */
	public function initCached(array $assoc)
	{
		$this->dummy->setGDOVars($assoc);
		$key = $this->dummy->getID();
		if (!isset($this->cache[$key]))
		{
			$this->cache[$key] = $this->dummy->setPersisted();
			$this->newDummy();
		}
		else
		{
			$this->cache[$key]->setGDOVars($assoc);
		}
// 		$this->cache[$key]->afterRead();
		return $this->cache[$key];
	}
	
	public function clearCache()
	{
	    $this->cache = [];
	    $this->all = null;
	}
	
	public function recache(GDO $object)
	{
		$back = $object;
		
		# GDO cache
		if ($object->gdoCached())
		{
    		$id = $object->getID();

    		# GDO single cache
			if (isset($this->cache[$id]))
			{
				$old = $this->cache[$id];
				$old->setGDOVars($object->getGDOVars());
// 				$old->tempReset(); # Why no more?
				$back = $old;
			}
			else
			{
				$this->cache[$id] = $object;
			}
			
			# @TODO check if true: GDO-ALL cache is, in theory, always sync by single identity. true?
// 			if (isset($this->all[$id]))
// 			{
// 			    $this->all[$id] = $back;
// 			}
		}
		
		# Memcached
		if (GWF_MEMCACHE && $back->memCached())
		{
		    self::$MEMCACHED->replace(GWF_MEMCACHE_PREFIX.$back->gkey(), $back, GWF_MEMCACHE_TTL);
		}
		
		return $back;
	}
	
	public function uncache(GDO $object)
	{
		$this->uncacheID($object->getID());
	}

	public function uncacheID($id)
	{
		$className = $this->table->gdoClassName();
		unset($this->cache[$id]);
		if (GWF_MEMCACHE)
		{
			self::$MEMCACHED->delete(GWF_MEMCACHE_PREFIX.$className . $id);
		}
	}
	
	/**
	 * memcached + gdo cache initializer
	 * @param array $assoc
	 * @return GDO
	 */
	public function initGDOMemcached(array $assoc)
	{
		$this->dummy->setGDOVars($assoc);
		$key = $this->dummy->getID();
		if (!isset($this->cache[$key]))
		{
			$gkey = $this->dummy->gkey();
			if (false === ($mcached = self::get(GWF_MEMCACHE_PREFIX.$gkey)))
			{
				$mcached = $this->dummy->setPersisted();
				if (GWF_MEMCACHE)
				{
					self::$MEMCACHED->set(GWF_MEMCACHE_PREFIX.$gkey, $mcached, GWF_MEMCACHE_TTL);
				}
    			$this->newDummy();
			}
			$this->cache[$key] = $mcached;
		}
		else
		{
			$this->cache[$key]->setGDOVars($assoc)->setPersisted();
		}
		return $this->cache[$key];
	}
	
}

# No memcached stub shim so it won't crash.
if (!class_exists('Memcached', false))
{
	require 'Memcached.php';
}
