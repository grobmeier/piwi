<?php
/**
 * Custom Generator.
 * This is a sample of a custom Generator.
 * It generates some content using a database as datasource.
 * The database is accessed by using a Connector.
 * Which connector is used can be configured in the "generators.xml", 
 * so different databases (like MySQL or SQLite) could be accessed easily.
 */
class SQLiteContentGenerator implements Generator {
	/** The name of the Connector that will be used to access the database. */	
	private $connector = null;
	
	/** The connection established by the connector on demand. */
	private $connection = null;
	
	/**
	 * Constructor.
	 */
    function __construct() {
    }
   
   	/**
	 * Returns the xml output of the Generator.
	 * @return string The xml output as string.
	 */
    function generate() {
		/**
		 * Establish connection.
		 * The Connector is retrieved from the ConnectorFactory, 
		 * which is a singleton, caching the connectors. 
		 */
		if ($this->connection == null) {
			$this->connection = ConnectorFactory::getConnectorById($this->connector);
		}

		// execute query
		$sql = "SELECT rowid, subject, content FROM content";
		$dbresult = $this->connection->execute($sql);
		
		// generate xml
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
		
		return $piwixml;
	}
	
	/**
	 * Used to pass parameters to the Generator.
	 * @param string $key The name of the parameter.
	 * @param object $value The value of the parameter.
	 */
    function setProperty($key, $value) {
		if($key == "connector") {
    		$this->connector = $value;
    	}
    }
}
?>