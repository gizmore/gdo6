<?php
namespace GDO\DB;
use GDO\Core\GDO;
use GDO\Core\Logger;
use GDO\Core\GDOError;
use GDO\Core\GDT;
use Symfony\Component\Routing\Tests\Fixtures\OtherAnnotatedClasses\VariadicClass;
/**
 * mySQLi abstraction.
 * 
 * @see Query
 * @see Result
 * @author gizmore
 * @version 6.05
 * @since 3.00
 */
class Database
{
	/**
	 * @return Database
	 */
	public static function instance() { return self::$INSTANCE; }
	public static $INSTANCE;
	
	# Connection
	private $link, $host, $user, $pass, $db, $debug;
	
	# Timing
	public $reads = 0;
	public $writes = 0;
	public $commits = 0;
	public $queries = 0;
	public $queryTime = 0;
	
	public static $READS = 0;
	public static $WRITES = 0;
	public static $COMMITS = 0;
	public static $QUERIES = 0;
	public static $QUERY_TIME = 0;
	
	/**
	 * @var GDO[]
	 */
	private static $TABLES = [];

	/**
	 * @var GDT[]
	 */
	private static $COLUMNS = [];
	
	public static function init()
	{
	    Cache::init();
	    return new self(GWF_DB_HOST, GWF_DB_USER, GWF_DB_PASS, GWF_DB_NAME, GWF_DB_DEBUG);
	}
	
	public function __construct($host, $user, $pass, $db, $debug=false)
	{
		self::$INSTANCE = $this;
		$this->debug = $debug;
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
	}
	
	public function getLink()
	{
		if (!$this->link)
		{
			$t1 = microtime(true);
			if ($this->link = $this->connect())
			{
    			$this->query("SET NAMES UTF8");
			}
			else
			{
			    throw new DBException('err_db_connect');
			}
			$timeTaken = microtime(true) - $t1;
			$this->queryTime += $timeTaken; self::$QUERY_TIME += $timeTaken;
		}
		return $this->link;
	}
	
	public function connect()
	{
	    return @mysqli_connect($this->host, $this->user, $this->pass, $this->db);
	}
	
	#############
	### Query ###
	#############
	public function queryRead($query)
	{
		$this->reads++; self::$READS++;
		return $this->query($query);
	}
	
	public function queryWrite($query)
	{
		$this->writes++; self::$WRITES++;
		return $this->query($query);
	}
	
	private function query($query)
	{
		$this->queries++; self::$QUERIES++;
		$t1 = microtime(true);
		if (!($result = mysqli_query($this->getLink(), $query)))
		{
		    throw new DBException("err_db", [mysqli_error($this->link), htmlspecialchars($query)]);
		}
		$t2 = microtime(true);
		$timeTaken = $t2 - $t1;
		$this->queryTime += $timeTaken; self::$QUERY_TIME += $timeTaken;
		if ($this->debug)
		{
			$timeTaken = sprintf('%.04f', $timeTaken);
			Logger::log('queries', "#" . self::$QUERIES . ": ({$timeTaken}s) ".$query, Logger::DEBUG);
		}
		return $result;
	}
	
	public function insertId()
	{
		return mysqli_insert_id($this->getLink());
	}
	
	public function affectedRows()
	{
		return mysqli_affected_rows($this->getLink());
	}
	
	###################
	### Table cache ###
	###################
	/**
	 * @param string $classname
	 * @throws GDOError
	 * @return \GDO\Core\GDO
	 */
	public static function tableS($classname)
	{
		if (!isset(self::$TABLES[$classname]))
		{
			self::$TABLES[$classname] = $gdo = new $classname();
			self::$COLUMNS[$classname] = self::hashedColumns($gdo->gdoColumns());

			/** @var $gdo \GDO\Core\GDO **/
			if ($gdo->gdoCached() || $gdo->memCached())
			{
				$gdo->initCache();
			}
		}
		return self::$TABLES[$classname];
	}
	
	/**
	 * Extract name from gdo columns for hashmap.
	 * @param GDT[] $gdoColumns
	 * @return GDT[]
	 */
	private static function hashedColumns(array $gdoColumns)
	{
		$columns = [];
		foreach ($gdoColumns as $gdoType)
		{
			$columns[$gdoType->name] = $gdoType;
		}
		return $columns;
	}
	
	/**
	 * @param string $classname
	 * @return GDT[]
	 */
	public static function columnsS($classname)
	{
		self::tableS($classname);
		return self::$COLUMNS[$classname];
	}
	
	####################
	### Table create ###
	####################
	/**
	 * Create a database table from a GDO. 
	 * @param GDO $gdo
	 * @return bool
	 */
	public function createTable(GDO $gdo)
	{
		$columns = [];
		$primary = [];
		
		foreach ($gdo->gdoColumnsCache() as $key => $column)
		{
			if ($define = $column->gdoColumnDefine())
			{
				$columns[] = $define;
			}
			if ($column->primary)
			{
				$primary[] = $column->identifier();
			}
		}
		
		if (count($primary))
		{
			$primary = implode(',', $primary);
			$columns[] = "PRIMARY KEY ($primary)";
		}

		foreach ($gdo->gdoColumnsCache() as $key => $column)
		{
			if ($column->unique)
			{
				$columns[] = "UNIQUE({$column->identifier()})";
			}
		}
		
		
		$columns = implode(",\n", $columns);
		
		$query = "CREATE TABLE IF NOT EXISTS {$gdo->gdoTableIdentifier()} (\n$columns\n) ENGINE = {$gdo->gdoEngine()}";
		
		if ($this->debug)
		{
			printf("<pre>%s</pre>\n", htmlspecialchars($query));
		}
		return $this->queryWrite($query);
	}
	
	public function dropTable(GDO $gdo)
	{
		return $this->queryWrite("DROP TABLE IF EXISTS {$gdo->gdoTableIdentifier()}");
	}
	
	public function truncateTable(GDO $gdo)
	{
		return $this->queryWrite("TRUNCATE TABLE {$gdo->gdoTableIdentifier()}");
	}
	
	###################
	### Transaction ###
	###################
	public function transactionBegin()
	{
		return mysqli_begin_transaction($this->getLink());
	}
	
	public function transactionEnd()
	{
		$this->commits++; self::$COMMITS++;
		$t1 = microtime(true);
		$result = mysqli_commit($this->getLink());
		$t2 = microtime(true);
		$tt = $t2 - $t1;
		$this->queryTime += $tt; self::$QUERY_TIME += $tt;
		return $result;
	}
	
	public function transactionRollback()
	{
		return mysqli_rollback($this->getLink());
	}
	
	############
	### Lock ###
	############
	
	###############
	### FKCheck ###
	###############
	public function enableForeignKeyCheck($check="1")
	{
		return $this->query("SET foreign_key_checks = $check");
	}

	public function disableForeignKeyCheck()
	{
		return $this->enableForeignKeyCheck("0");
	}
}
