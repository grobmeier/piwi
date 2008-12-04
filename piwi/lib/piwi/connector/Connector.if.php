<?php
/**
 * Interface that database connectors have to implement.
 */
interface Connector {
	/**
	 * Establishes a connection to the database.
	 */
	public function connect();
	
	/**
	 * Executes the given query.
	 * @param string $sql The query to execute.
	 * @return array The result of the query.
	 */
	public function execute($sql);
	
	/**
	 * Used to pass parameters to the Connector.
	 * @param string $key The name of the parameter.
	 * @param object $value The value of the parameter.
	 */
	public function setProperty($key, $value);
}
?>