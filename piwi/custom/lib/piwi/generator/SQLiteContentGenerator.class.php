<?php

class SQLiteContentGenerator implements Generator {
	private $connection = null;
	private $connName = null;

    function SQLiteContentGenerator() {
    }
   
    function generate($output = "html") {
    	// TODO - inject
    	global $connectors;
    	
    	if($this->connection == null) {
			$this->connection = $connectors->getInstance($this->connName);
		} 
		
		// $this->connection->execute('CREATE TABLE content ( subject VARCHAR ( 100 ) NOT NULL , content LONGTEXT NOT NULL ) ;  ');
  		// $this->connection->execute("INSERT INTO content ( subject , content ) VALUES ( 'My seconde content' , 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.' ) "); 
		// $sql = "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;";

		$sql= "SELECT rowid, subject, content FROM content";
		$dbresult = $this->connection->execute($sql);
		
		$piwixml = "<?xml version='1.0'?>";
		$piwixml .= "<!DOCTYPE document PUBLIC \"-//PIWI//DTD Documentation V1.0//EN\" \"dtd/document-v10.dtd\">";
		$piwixml .= "<document>";
		if ($dbresult != null) {
			foreach($dbresult as $row) {
				$piwixml .= "<section>";
				$piwixml .= "<title>";
				$piwixml .= $row['subject'];
				$piwixml .= "</title>";
				$piwixml .= "<p>";
				$piwixml .= $row['content'];
				$piwixml .= "</p>";		
				$piwixml .= "</section>";
			}
		}
		$piwixml .= "</document>";
		
		if($output == "html") {
			return XMLPage::transformPart(
						"resources/xslt/document-v1.0.xsl", 
						$piwixml);
		} else if ($output == "xml") {
			return $piwixml;
		}
	}
	
    function setProperty($key,$value) {
		if($key == "connection") {
    		$this->connName = $value;
    	}
    }
}
?>