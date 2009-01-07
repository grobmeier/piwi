<?php
/**
 * Establishes connections to MySQL databases.
 */
class MySQLConnector implements Connector {
	/** The url of the server. */
	private $server = null;
	
	/** The name of the database. */
	private $database = null;
	
	/** The username. */
	private $username = null;
	
	/** The password. */
	private $password = null;
	
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
    	if ($this->server == null || $this->database == null) {
   			throw new DatabaseException('No database specified (Correct your "connectors.xml").',
				DatabaseException::ERR_NO_DATABASE_SPECIFIED);
   		}
   		
		if ($this->username != null && $this->password != null) {
			$this->dbConnection = mysql_connect($this->server, $this->username, $this->password);
		} else {
			$this->dbConnection = mysql_connect($this->server);
		}

		if (!$this->dbConnection) {
			throw new DatabaseException('Establishing database connection failed (' . mysql_error() . ').', 
				DatabaseException::ERR_CONNECTION_FAILED);
		}
		
		//execute query
		$db_selected = mysql_select_db($this->database, $this->dbConnection);		
		
		if (!$db_selected) {
			throw new DatabaseException('Could not select database (' . mysql_error() . ').', 
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
   		$result = mysql_query($sql, $this->dbConnection);
   		
   		if (!$result) {
   			throw new DatabaseException('Querying database failed (' . mysql_error() . ').', 
				DatabaseException::ERR_QUERY_FAILED);
   		}
   		
   		$i = 0;
   		while ($temp = mysql_fetch_array($result, MYSQL_BOTH)) {
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
    	if ($key == "server") {
    		$this->server = $value;
    	} else if ($key == "database") {
    		$this->database = $value;
    	} else if ($key == "username") {
    		$this->username = $value;
    	} else if ($key == "password") {
    		$this->password = $value;
    	}
    }
}
?>