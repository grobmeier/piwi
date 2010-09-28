<?php
class SQLite2ConnectorTest extends UnitTestCase {
	
	private $connector = null;
	
	function before($message) {
		$this->connector = new SQLite2Connector();
	}
	
	function testConnectUninitialized() {
		$this->expectException('DatabaseException', 'No database file set.');
		$this->connector->connect();
	}
	
	function testExecute() {
		$this->connector->setProperty('file', dirname(__FILE__) . '/data/sqlite2_sample.sqlite2');
		$this->connector->setProperty('mode', 0666);
		
		$sql = "SELECT rowid, subject, content FROM content";
		$dbresult = $this->connector->execute($sql);
		
		$this->assertEqual(sizeof($dbresult), 2, 'Result has wrong number of rows.');
		
		
		$sql = "SELECT rowid, subject, content FROM 666";
		$this->expectException('DatabaseException', 'Table should not exist.');
		$dbresult = $this->connector->execute($sql);		
	}
}
?>