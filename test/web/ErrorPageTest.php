<?php
require_once ('test/PiwiWebTestCase.php');

class ErrorPageTest extends PiwiWebTestCase {
	
	function testHeadersTranslatedInDataGrid() {
		$this->get(self :: $HOST . '666.html');
		
		// check if error is displayed
		$this->assertWantedText('Could not find the requested page', 'No error displayed.');
	}
}
?>