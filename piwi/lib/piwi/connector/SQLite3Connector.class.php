<?php

class SQLite3Connector implements Connector {

	private $file = null;	
	private $dbConnection = null;
	
    function SQLiteConnector() {
    }
    
    function connect() {
    	if($this->file == null) {
   			throw new DatabaseException(
				'No database specified (Correct your "connectors.xml").',
				DatabaseException::ERR_NO_FILENAME_SPECIFIED);
   		}
   		
   		try {
			$this->dbConnection = new PDO('sqlite:'.$this->file);			
		} catch( PDOException $exception ) {
			throw new DatabaseException(
				'Establishing database connection failed ('.$exception->getMessage().').', 
				DatabaseException::ERR_CONNECTION_FAILED);
		}
    }
    
    function execute($sql) {
   		if($this->dbConnection == null) {
   			$this->connect();
   		}
   		$result = $this->dbConnection->query($sql);
   		
   		if ($result == false){
   			throw new DatabaseException(
				'Querying database failed.', 
				DatabaseException::ERR_QUERY_FAILED);
   		}
   		
   		$i = 0;
   		while($temp = $result->fetch()) {
   			$resultArray[$i] = $temp;
   			$i++;
   		}
		return $resultArray;
    }
        
    function setProperty($key,$value) {
    	if($key == "file") {
    		$this->file = $value;
    	}
    }
}
?>