<?php
/**
 * Creates a table containing whose data comes from a database.
 */
class DataGridGenerator implements Generator {
	/** The name of the Connector that will be used to access the database. */	
	private $connector = null;
	
	/** The connection established by the connector on demand. */
	private $connection = null;
	
	/** The headers of the table. */
	private $headers = null;
	
	/** The SQL-Query to perform. */
	private $sql = null;

	/**
	 * Constructor.
	 */
    function __construct() {
    }
   
   	/**
	 * Returns the xml output of the Generator.
	 * @return string The xml output as string.
	 */
    public function generate() {
		/**
		 * Establish connection.
		 * The Connector is retrieved from the ConnectorFactory, 
		 * which is a singleton, caching the connectors. 
		 */
		if ($this->connection == null) {
			$this->connection = BeanFactory :: getBeanById('connectorFactory')->getConnectorById($this->connector);
		}

		// execute query
		$dbresult = $this->connection->execute($this->sql);
		
		// generate xml
		$piwixml = '<table>';
		if ($this->headers != null) {
			$piwixml .= '<tr>';
			foreach ($this->headers as $value) {
       			$piwixml .= '<th><label key="' . $value . '" /></th>';
			}
			$piwixml .= '</tr>';
		}
		
		if ($dbresult != null) {
			foreach ($dbresult as $row) {
				$piwixml .= '<tr>';
				for ($index = 0, $max_count = sizeof($row) / 2; $index < $max_count; $index++) {
					$piwixml .= '<td>' . $row[$index] . '</td>';
				}
				$piwixml .= '</tr>';
			}
		}
		$piwixml .= '</table>';
		return $piwixml;
	}
	
	/**
	 * Used to pass parameters to the Generator.
	 * @param string $key The name of the parameter.
	 * @param string $value The value of the parameter.
	 */
    public function setProperty($key, $value) {
		if ($key == "connector") {
    		$this->connector = $value;
    	} else if ($key == "sql") {
    		$this->sql = $value;
    	} else if ($key == "headers") {
    		$this->headers = explode(',', str_replace(", ", ",", $value));
    	}
    }
}
?>