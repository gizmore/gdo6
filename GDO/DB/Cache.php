<?php
namespace GDO\DB;

use GDO\Core\GDO;
/**
 * Cache is a global object cache, where each fetched object (with the same key) from the database results in the same instance.
 * This way you can never have two dangling out of sync users in your application.
 * It also saves a bit mem.
 * Of course this comes with a slight overhead.
 * As GDO5 was written from scratch with this in mind, the overhead is quite small.
 * 
 * New and unplanned is the use of memcached :)
 * 
 * There are a few global memcached keys scattered across the application, fetching all rows.
 * Those GDOs dont use memcached on a per row basis
 * 
 * gdo_modules
 * gdo_country
 * gdo_language
 * 
 * The other memcached keys work on a per row basis with table_name_id as key.
 * 
 * @author gizmore
 * @since 5.0
 * @version 5.0
 * @license MIT
 */
class Cache
{
	private static $MEMCACHED;
	public static function get($key) { return self::$MEMCACHED->get(GWF_MEMCACHE_PREFIX.$key); }
	public static function set($key, $value) { self::$MEMCACHED->set(GWF_MEMCACHE_PREFIX.$key, $value); }
	public static function remove($key) { self::$MEMCACHED->delete(GWF_MEMCACHE_PREFIX.$key); }
	public static function flush() { self::$MEMCACHED->flush(); }
	public static function init()
	{
		self::$MEMCACHED = new \Memcached();
		self::$MEMCACHED->addServer(GWF_MEMCACHE_HOST, GWF_MEMCACHE_PORT);
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
		$this->newDummy();
	}

	private function newDummy()
	{
		$this->dummy = new $this->klass();
	}
	
	/**
	 * @param string $id
	 * @return GDO
	 */
	public function findCached(...$ids)
	{
		$id = implode(':', $ids);
		if (!isset($this->cache[$id]))
		{
			if ($mcached = self::get($this->klass . $id))
			{
				$this->cache[$id] = $mcached;
			}
		}
		return @$this->cache[$id];
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
		return $this->cache[$key];
	}
	
	public function recache(GDO $object)
	{
	    if ($object->gdoCached())
	    {
	        $this->cache[$object->getID()] = $object;
	    }
		if ($object->memCached())
		{
			self::$MEMCACHED->replace(GWF_MEMCACHE_PREFIX.$object->gkey(), $object, GWF_MEMCACHE_TTL);
		}
	}
	
	public function uncache(GDO $object)
	{
		$this->uncacheID($object->getID());
	}

	public function uncacheID($id)
	{
	    $className = $this->table->gdoClassName();
		unset($this->cache[$id]);
		self::$MEMCACHED->delete(GWF_MEMCACHE_PREFIX.$className . $id);
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
			if (!($mcached = self::$MEMCACHED->get(GWF_MEMCACHE_PREFIX.$gkey)))
			{
				$mcached = $this->dummy->setPersisted();
				self::$MEMCACHED->set(GWF_MEMCACHE_PREFIX.$gkey, $mcached, GWF_MEMCACHE_TTL);
				$this->newDummy();
			}
			$this->cache[$key] = $mcached;
		}
		return $this->cache[$key];
	}
}

# No memcached stub
if (!class_exists('Memcached', false))
{
    require 'memcached.php';
}
