<?php
require_once ('test/PiwiWebTestCase.php');

class DatabaseTest extends PiwiWebTestCase {
	
	function testGetContentFromDatabase() {
		$this->get(self :: $HOST . 'database.html');

		// check if text comes from database
		$this->assertWantedText('First SQLite2 Content', 'Database access failed.');
		$this->assertWantedText('Second SQLite2 Content', 'Database access failed.');
		$this->assertWantedText('This text comes from a SQLite2 database.', 'Database access failed.');
		$this->assertWantedText('This text comes from a SQLite2 database, too.', 'Database access failed.');
		
		$this->assertWantedText('First SQLite3 Content', 'Database access failed.');
		$this->assertWantedText('Second SQLite3 Content', 'Database access failed.');
		$this->assertWantedText('This text comes from a SQLite3 database.', 'Database access failed.');
		$this->assertWantedText('This text comes from a SQLite3 database, too.', 'Database access failed.');
	}
}
?>