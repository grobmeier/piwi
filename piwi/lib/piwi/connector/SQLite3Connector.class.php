<?php

class SQLite3Connector implements Connector {

	private $url = null;
	
	private $db = null;
	private $sqliteerror = null;
	
    function SQLiteConnector() {
    }
    
    function connect() {
    	$this->db = new PDO($this->url);
    	
    }
    
    function execute($sql) {
   		if($this->db == null) {
   			$this->connect();
   		}
   		$result = $this->db->query($sql);
   		
   		$i = 0;
   		while($temp = $result->fetch()) {
   			$resultArray[$i] = $temp;
   			$i++;
   		}
		return $resultArray;
    }
        
    function setProperty($key,$value) {
    	if($key == "url") {
    		$this->url = $value;
    	}
    }
}
?>