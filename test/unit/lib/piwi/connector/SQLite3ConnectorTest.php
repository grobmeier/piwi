<?php
require_once ('test/PiwiTestCase.php');

class SQLite3ConnectorTest extends PiwiTestCase {
	
	private $connector = null;
	
	function before($message) {
		$this->connector = new SQLite3Connector();
	}
	
	function testConnectUninitialized() {
		$this->expectException(DatabaseException, 'No database file set.');
		$this->connector->connect();
	}
	
	function testExecute() {
		$this->connector->setProperty('file', dirname(__FILE__) . '/data/sqlite3_sample.sqlite3');
		$this->connector->setProperty('mode', 0666);
		
		$sql = "SELECT rowid, subject, content FROM content";
		$dbresult = $this->connector->execute($sql);
		
		$this->assertEqual(sizeof($dbresult), 2, 'Result has wrong number of rows.');
		
		
		$sql = "SELECT rowid, subject, content FROM 666";
		$this->expectException(DatabaseException, 'Table should not exist.');
		$dbresult = $this->connector->execute($sql);		
	}
}
?>