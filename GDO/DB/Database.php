<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\Logger;
use GDO\Core\GDOError;
use GDO\Core\GDT;
use Exception;

/**
 * mySQLi abstraction.
 * 
 * @author gizmore
 * @version 6.10
 * @since 3.00
 * 
 * @see Query
 * @see Result
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
	
	public function __destruct()
	{
		$this->closeLink();
	}
	
	public function closeLink()
	{
		if ($this->link)
		{
			@mysqli_close($this->link);
			$this->link = null;
		}
	}
	
	public function getLink()
	{
		if (!$this->link)
		{
			try
			{
				$t1 = microtime(true);
				if ($this->link = $this->connect())
				{
					$this->query("SET NAMES UTF8");
					$this->query("SET time_zone = '+00:00'");
				}
			}
			catch (Exception $e)
			{
				throw new DBException('err_db_connect', [$e->getMessage()]);
			}
			finally
			{
				$timeTaken = microtime(true) - $t1;
				$this->queryTime += $timeTaken; self::$QUERY_TIME += $timeTaken;
			}
		}
		return $this->link;
	}
	
	public function connect()
	{
		return mysqli_connect($this->host, $this->user, $this->pass, $this->db);
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
			if ($this->link)
			{
				$error = @mysqli_error($this->link);
				$errno = @mysqli_errno($this->link);
				$this->closeLink();
			}
			else
			{
				$error = t('err_db_no_link');
				$errno = 0;
			}
			throw new DBException("err_db", [$errno, $error, htmlspecialchars($query)]);
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
		    /** @var $gdo GDO **/
			self::$TABLES[$classname] = $gdo = new $classname();
			$gdo->isTable = true;
			
			if ($gdo->gdoAbstract())
			{
				return null;
			}
			
			self::$COLUMNS[$classname] = self::hashedColumns($gdo);

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
	private static function hashedColumns(GDO $gdo)
	{
	    $gdoColumns = $gdo->gdoColumns();
		$columns = [];
		foreach ($gdoColumns as $gdoType)
		{
			$columns[$gdoType->name] = $gdoType->gdtTable($gdo);
		}
		return $columns;
	}
	
	/**
	 * @param string $classname
	 * @return GDT[]
	 */
	public static function columnsS($classname)
	{
		if (!isset(self::$COLUMNS[$classname]))
		{
    		$gdo = self::tableS($classname);
			self::$COLUMNS[$classname] = self::hashedColumns($gdo);
		}
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
	public function createTable(GDO $gdo, $reinstall=false)
	{
		$columns = [];
		$primary = [];
		
		foreach ($gdo->gdoColumnsCache() as $column)
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

		foreach ($gdo->gdoColumnsCache() as $column)
		{
			if ($column->unique)
			{
				$columns[] = "UNIQUE({$column->identifier()})";
			}
		}
		
		$columnsCode = implode(",\n", $columns);
		
		$query = "CREATE TABLE IF NOT EXISTS {$gdo->gdoTableIdentifier()} (\n$columnsCode\n) ENGINE = {$gdo->gdoEngine()}";
		
		if ($this->debug)
		{
			printf("<pre>%s</pre>\n", htmlspecialchars($query));
		}
		
		$this->queryWrite($query);
		
		if ($reinstall)
		{
			$this->alterTable($gdo);
		}
		
		return true;
	}
	
// 	/**
// 	 * Simply alter all columns again.
// 	 * Check if key changes need to be done.
// 	 * @param GDO $gdo
// 	 */
// 	public function alterTable(GDO $gdo)
// 	{
// 		$query = "select tab.table_schema as database_schema,
//     sta.index_name as pk_name,
//     sta.seq_in_index as column_id,
//     sta.column_name,
//     tab.table_name
// from information_schema.tables as tab
// inner join information_schema.statistics as sta
//         on sta.table_schema = tab.table_schema
//         and sta.table_name = tab.table_name
//         and sta.index_name = 'primary'
// where tab.table_schema = '{$this->db}'
//     and tab.table_type = 'BASE TABLE'
//     and tab.table_name = '{$gdo->gdoTableName()}'
// order by tab.table_name,
//     column_id;";
// // 		$result = $this->queryRead($query);
// // 		var_dump(mysqli_fetch_assoc($result));
// // 		die();

// // 		$columns = [];
// // 		$lastCol = null;
// // 		foreach ($gdo->gdoColumnsCache() as $column)
// // 		{
// // 			if ($define = $column->gdoColumnDefine())
// // 			{
// // 				$after = $lastCol === null ? "FIRST" : "AFTER {$lastCol->name}";
// // 				$query = "ALTER TABLE {$gdo->gdoTableName()} CHANGE COLUMN {$column->name} $define {$after}";
// // 				$lastCol = $column;
// // 				$this->queryWrite($query);
// // 			}
// // 		}
		
// 	}
	
	public function dropTable(GDO $gdo)
	{
		return $this->queryWrite("DROP TABLE IF EXISTS {$gdo->gdoTableIdentifier()}");
	}
	
	public function truncateTable(GDO $gdo)
	{
		return $this->queryWrite("TRUNCATE TABLE {$gdo->gdoTableIdentifier()}");
	}
	
	###################
	### DB Creation ###
	###################
	public function createDatabase($databaseName)
	{
		return $this->queryWrite("CREATE DATABASE $databaseName");
	}
	
	public function useDatabase($databaseName)
	{
		$this->queryWrite("USE $databaseName");
	}
	
	public function dropDatabase($databaseName)
	{
		return $this->queryWrite("DROP DATABASE $databaseName");
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
	public function lock($lock, $timeout=30)
	{
		$query = "SELECT GET_LOCK('{$lock}', {$timeout}) as L";
		return $this->queryRead($query);
	}
	
	public function unlock($lock)
	{
		$query = "SELECT RELEASE_LOCK('{$lock}') as L";
		return $this->queryRead($query);
	}
	
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
