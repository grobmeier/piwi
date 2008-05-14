<?php

class SQLiteConnector implements Connector {

	private $file = null;
	private $mode = null;
	
	private $db = null;
	private $sqliteerror = null;
	
    function SQLiteConnector() {
    }
    
    function connect() {
    	$this->db = sqlite_open($this->file, $this->mode, $this->sqliteerror);
    }
    
    function execute($sql) {
   		if($this->db == null) {
   			$this->connect();
   		}
   		
   		$result = sqlite_query($this->db,$sql);
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