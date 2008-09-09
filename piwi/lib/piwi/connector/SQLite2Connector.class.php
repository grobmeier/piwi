<?php

class SQLite2Connector implements Connector {

	private $file = null;
	private $mode = null;	
	private $dbConnection = null;
		
    function SQLiteConnector() {
    }
    
    function connect() {
    	if(!$this->file) {
   			throw new DatabaseException(
				'No database specified (Correct your "connectors.xml").',
				DatabaseException::ERR_NO_FILENAME_SPECIFIED);
   		}
   		   		
   		$this->dbConnection = sqlite_open($this->file, $this->mode, $sqliteerror);
   		
   		if ($sqliteerror != null) {
   			throw new DatabaseException(
				'Establishing database connection failed ('.$sqliteerror.').', 
				DatabaseException::ERR_CONNECTION_FAILED);
    	}
    }
    
    function execute($sql) {
   		if($this->dbConnection == null) {
   			$this->connect();
   		}
   		
   		$result = sqlite_query($this->dbConnection,$sql);
		
   		if ($result == false){
   			throw new DatabaseException(
				'Querying database failed.', 
				DatabaseException::ERR_QUERY_FAILED);
   		}
   		
   		$i = 0;
   		while($temp = sqlite_fetch_array($result)) {
   			$resultArray[$i] = $temp;
   			$i++;
   		}
		return $resultArray;
    }
        
    function setProperty($key,$value) {
    	if($key == "file") {
    		$this->file = $value;
    	}
    	
    	if($key == "mode") {
    		$this->mode = $value;
    	}
    }
}
?>