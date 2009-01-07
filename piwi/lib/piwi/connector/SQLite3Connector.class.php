<?php
/**
 * Establishes connections to SQLite 3 databases.
 */
class SQLite3Connector implements Connector {
	/** The path of the database file. */
	private $file = null;
	
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
   		
   		try {
			$this->dbConnection = new PDO('sqlite:' . $this->file);			
		} catch(PDOException $exception) {
			throw new DatabaseException('Establishing database connection failed (' .
					$exception->getMessage() . ').', 
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
   		$result = $this->dbConnection->query($sql);
   		
   		if (!$result) {
   			throw new DatabaseException('Querying database failed.', 
				DatabaseException::ERR_QUERY_FAILED);
   		}
   		
   		$i = 0;
   		while ($temp = $result->fetch()) {
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
    	}
    }
}
?>