<?php
require_once ('test/PiwiWebTestCase.php');

class FormsTest extends PiwiWebTestCase {
	function testGetFormByCorrectIdButNonInitializedFormFactory() {
		$this->get(self :: $HOST . 'forms.html');
		$this->clickSubmit("Send Data");

		// check if all validators fail
		$this->assertWantedText('You must select a gender.', 'Validator failed.');
		$this->assertWantedText('You must enter a name.', 'Validator failed.');
		$this->assertWantedText('You must enter a valid email.', 'Validator failed.');
		$this->assertNoText('Email must be the same.', 'Validator failed.');
		$this->assertWantedText('You must enter a number in the range 0-10.', 'Validator failed.');
		$this->assertWantedText('Please select at least one interest.', 'Validator failed.');

		// check if validator fails
		$this->setFieldByName('2Email', 'test@test.de');
		$this->clickSubmit("Send Data");
		$this->assertWantedText('Email must be the same.', 'Validator failed.');
	}
}
?>