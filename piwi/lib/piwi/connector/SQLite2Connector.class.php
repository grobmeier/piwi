<?php
/**
 * Establishes connections to SQLite 2 databases.
 */
class SQLite2Connector implements Connector {
	/** The path of the database file. */
	private $file = null;
	
	/** The mode of the connection (default is: 0666). */
	private $mode = 0666;	
	
	/** The connection. */
	private $dbConnection = null;
		
	/**
	 * Constructor.
	 */
    public function __construct() {
    }
    
    /**
	 * Establishes a connection to the database.
	 */
    public function connect() {
    	if ($this->file == null) {
   			throw new DatabaseException('No database specified (Correct your "connectors.xml").',
				DatabaseException::ERR_NO_DATABASE_SPECIFIED);
   		}
   		$sqliteerror = null;
   		$this->dbConnection = sqlite_open($this->file, $this->mode, $sqliteerror);
   		
   		if ($sqliteerror != null) {
   			throw new DatabaseException('Establishing database connection failed (' . $sqliteerror . ').',
				DatabaseException::ERR_CONNECTION_FAILED);
    	}
    }

	/**
	 * Executes the given query.
	 * @param string $sql The query to execute.
	 * @return array The result of the query.
	 */
    public function execute($sql) {
   		if ($this->dbConnection == null) {
   			$this->connect();
   		}
   		
   		// Execute query
   		$result = sqlite_query($this->dbConnection,$sql);
		
   		if (!$result) {
   			throw new DatabaseException('Querying database failed.', 
				DatabaseException::ERR_QUERY_FAILED);
   		}
   		
   		$i = 0;
   		while ($temp = sqlite_fetch_array($result)) {
   			$resultArray[$i] = $temp;
   			$i++;
   		}
		return $resultArray;
    }
    
	/**
	 * Used to pass parameters to the Connector.
	 * @param string $key The name of the parameter.
	 * @param object $value The value of the parameter.
	 */
    public function setProperty($key, $value) {
    	if ($key == "file") {
    		$this->file = $value;
    	} else if ($key == "mode") {
    		$this->mode = $value;
    	}
    }
}
?>