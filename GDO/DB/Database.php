<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\Logger;
use GDO\Core\GDOError;
use GDO\Core\GDT;
use Exception;
use GDO\Util\Strings;
use GDO\Core\Debug;

/**
 * mySQLi abstraction.
 * 
 * @TODO support postgres?
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
	 * @return self
	 */
	public static function instance() { return self::$INSTANCE; }
	public static $INSTANCE;
	
	# Connection
	private $link, $host, $user, $pass, $db, $debug;
	
	# Const
	const PRIMARY_USING = 'USING HASH'; # default index algorithm for primary keys.
	
	# Perf connection
	public $reads = 0;
	public $writes = 0;
	public $commits = 0;
	public $queries = 0;
	public $queryTime = 0;
	
	# Perf total
	public static $READS = 0;
	public static $WRITES = 0;
	public static $COMMITS = 0;
	public static $QUERIES = 0;
	public static $QUERY_TIME = 0;
	
	/**
	 * Available GDO
	 * @var GDO[]
	 */
	private static $TABLES = [];

	/**
	 * gdoColumns for all GDO
	 * @var GDT[]
	 */
	private static $COLUMNS = [];
	
	public static function init()
	{
		Cache::init();
		if (GWF_DB_ENABLED) # @TODO should always return an instance?
		{
		    return new self(
		        GWF_DB_HOST, GWF_DB_USER, GWF_DB_PASS, GWF_DB_NAME, GWF_DB_DEBUG);
		}
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
				    # This is more like a read because nothing is written to the disk.
					$this->queryRead("SET NAMES UTF8");
					$this->queryRead("SET time_zone = '+00:00'");
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
		return mysqli_connect(
		    $this->host, $this->user, $this->pass, $this->db);
	}
	
	#############
	### Query ###
	#############
	public function queryRead($query)
	{
		self::$READS++;
		$this->reads++;
		return $this->query($query);
	}
	
	public function queryWrite($query)
	{
		self::$WRITES++;
		$this->writes++;
		return $this->query($query);
	}
	
	private function query($query)
	{
		self::$QUERIES++;
		$this->queries++;
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
			throw new DBException("err_db", [$errno, $error, $query]);
		}
		$t2 = microtime(true);
		$timeTaken = $t2 - $t1;
		self::$QUERY_TIME += $timeTaken;
		$this->queryTime += $timeTaken;
		if ($this->debug)
		{
			$timeTaken = sprintf('%.04f', $timeTaken);
			Logger::log('queries', "#" . self::$QUERIES .
			    ": ({$timeTaken}s) ".$query, Logger::DEBUG);
			if ($this->debug > 1)
			{
			    Logger::log('queries', 
			        Debug::backtrace('#' . self::$QUERIES . ' Backtrace', false));
			}
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
	public static function tableS($classname, $initCache=true)
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
// 			if ($initCache && ($gdo->gdoCached() || $gdo->memCached()))
			{
			    # Always init a cache item.
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
		$columns = [];
		foreach ($gdo->gdoColumns() as $gdoType)
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
			$columns[] = "PRIMARY KEY ($primary) " . self::PRIMARY_USING;
		}

		foreach ($gdo->gdoColumnsCache() as $column)
		{
			if ($column->unique)
			{
				$columns[] = "UNIQUE({$column->identifier()})";
			}
		}
		
		$columnsCode = implode(",\n", $columns);
		
		try
		{
		    $this->disableForeignKeyCheck();
    		$query = "CREATE TABLE IF NOT EXISTS {$gdo->gdoTableIdentifier()} ".
    		  "(\n$columnsCode\n) ENGINE = {$gdo->gdoEngine()}";
    		$this->queryWrite($query);
		}
		catch (\Throwable $ex)
		{
		    throw $ex;
		}
		finally
		{
		    $this->enableForeignKeyCheck();
		}
		
// 		@TODO Implement auto alter table... very tricky!
// 		if ($reinstall)
// 		{
// 			$this->alterTable($gdo);
// 		}
		
		return true;
	}
	
	# @TODO Implement auto alter table... very tricky!
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
// // 				$query = "ALTER TABLE {$gdo->gdoTableName()} ".
// //                   "CHANGE COLUMN {$column->name} $define {$after}";
// // 				$lastCol = $column;
// // 				$this->queryWrite($query);
// // 			}
// // 		}
		
// 	}
	
	public function dropTable(GDO $gdo)
	{
	    $tableName = $gdo->gdoTableIdentifier();
		return $this->queryWrite("DROP TABLE IF EXISTS {$tableName}");
	}
	
	public function truncateTable(GDO $gdo)
	{
	    $tableName = $gdo->gdoTableIdentifier();
	    return $this->queryWrite("TRUNCATE TABLE {$tableName}");
	}
	
	###################
	### DB Creation ###
	###################
	public function createDatabase($databaseName)
	{
		return $this->queryWrite("CREATE DATABASE $databaseName");
	}
	
	public function dropDatabase($databaseName)
	{
		return $this->queryWrite("DROP DATABASE $databaseName");
	}
	
	public function useDatabase($databaseName)
	{
	    $this->queryWrite("USE $databaseName");
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
	    # Perf
		$this->commits++;
		self::$COMMITS++;
		
		# Exec and perf
		$t1 = microtime(true);
		$result = mysqli_commit($this->getLink());
		$t2 = microtime(true);
		$tt = $t2 - $t1;
		
		# Perf
		$this->queryTime += $tt;
		self::$QUERY_TIME += $tt;
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
	
	##############
	### Import ###
	##############
	public function parseSQLFile($path)
	{
	    $fh = fopen($path, 'r');
	    $command = '';
	    while ($line = fgets($fh))
	    {
	        if ( (Strings::startsWith($line, '-- ')) ||
	            (Strings::startsWith($line, '/*')) )
	        {
	            # skip comments
	            continue;
	        }
	        
	        # Append to command
	        $command .= $line;
	        
	        # Finished command
	        if (Strings::endsWith(trim($line), ';'))
	        {
	            # Most likely a write
    	        $this->queryWrite($command);
	        }
	    }
	}
	
}
