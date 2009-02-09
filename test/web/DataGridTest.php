<?php
require_once ('test/PiwiWebTestCase.php');

class DataGridTest extends PiwiWebTestCase {
	
	function testGetContentFromDatabase() {
		$this->get(self :: $HOST . 'datagrid.html');

		for ($i = 1; $i <= 10; $i++) {
			$this->assertWantedText('Name' . $i, 'Database access failed.');
			$this->assertWantedText('Adress' . $i, 'Database access failed.');
			$this->assertWantedText('City' . $i, 'Database access failed.');
			$this->assertWantedText('Phone' . $i, 'Database access failed.');
		}
	}
}
?>