<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\Logger;
use GDO\Core\GDOError;
use GDO\Core\GDT;
use GDO\Util\Strings;
use GDO\Core\Debug;

/**
 * mySQLi abstraction.
 * 
 * @TODO support postgres? This can be achieved via making module DB a separate module. Just need to move some classes to core and ifelse them in creation code?
 * @TODO support sqlite? This can be achieved by a few string tricks maybe. No foreign keys? no idea.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 3.0.0
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
	public $locks = 0;
	public $reads = 0;
	public $writes = 0;
	public $commits = 0;
	public $queries = 0;
	public $queryTime = 0;
	
	# Perf total
	public static $LOCKS = 0;
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
		if (GDO_DB_ENABLED) # @TODO should always return an instance?
		{
		    return new self(
		        GDO_DB_HOST, GDO_DB_USER, GDO_DB_PASS, GDO_DB_NAME, GDO_DB_DEBUG);
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
		return $this->link ? $this->link : $this->openLink();
	}
	
	private function openLink()
	{
		try
		{
			$t1 = microtime(true);
			if ($this->link = $this->connect())
			{
				# This is more like a read because nothing is written to the disk.
				$this->queryRead("SET NAMES UTF8");
				$this->queryRead("SET time_zone = '+00:00'");
				return $this->link;
			}
		}
		catch (\Throwable $e)
		{
			throw new DBException('err_db_connect', [$e->getMessage()]);
		}
		finally
		{
			$timeTaken = microtime(true) - $t1;
			$this->queryTime += $timeTaken;
			self::$QUERY_TIME += $timeTaken;
		}
	}
	
	public function connect()
	{
		return mysqli_connect($this->host, $this->user, $this->pass, $this->db);
	}
	
	#############
	### Query ###
	#############
	public function queryRead($query, $buffered=true)
	{
		self::$READS++;
		$this->reads++;
		return $this->query($query, $buffered);
	}
	
	public function queryWrite($query)
	{
		self::$WRITES++;
		$this->writes++;
		return $this->query($query);
	}
	
	private function query($query, $buffered=true)
	{
		$t1 = microtime(true);
		
		if ($buffered)
		{
		    $result = mysqli_query($this->getLink(), $query);
		}
		else 
		{
		    if (mysqli_real_query($this->getLink(), $query))
		    {
		        $result = mysqli_use_result($this->getLink());
		    }
		}
		
		if (!($result))
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
		$this->queries++;
		self::$QUERIES++;
		$this->queryTime += $timeTaken;
		self::$QUERY_TIME += $timeTaken;
		if ($this->debug)
		{
			$timeTaken = sprintf('%.04f', $timeTaken);
			Logger::log('queries', "#" . self::$QUERIES .
			    ": ({$timeTaken}s) ".$query);
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
	 * @return GDO
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

		    # Always init a cache item.
			$gdo->initCache();
			
			$gdo->setInited();
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
		foreach ($gdo->gdoColumns() as $gdt)
		{
			$columns[$gdt->name] = $gdt->gdtTable($gdo);
		}
		return $columns;
	}
	
	/**
	 * @param string $classname
	 * @return GDT[]
	 */
	public static function &columnsS($classname)
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
	public function createTableCode(GDO $gdo)
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
		
		$query = "CREATE TABLE IF NOT EXISTS {$gdo->gdoTableIdentifier()} ".
		         "(\n$columnsCode\n) ENGINE = {$gdo->gdoEngine()}";
		
		return $query;
	}
	
	/**
	 * Create a database table from a GDO. 
	 * @param GDO $gdo
	 * @return bool
	 */
	public function createTable(GDO $gdo)
	{
		try
		{
		    $this->disableForeignKeyCheck();
    		$query = $this->createTableCode($gdo);
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
		
		return true;
	}
	
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
	    $this->queryRead("USE $databaseName");
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
	    $this->locks++;
	    self::$LOCKS++;
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
	/**
	 * Import a large SQL file.
	 * @param string $path
	 */
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
    	        $command = '';
	        }
	    }
	}
	
}
