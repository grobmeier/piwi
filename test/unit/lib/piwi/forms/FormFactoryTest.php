<?php
require_once ('test/PiwiTestCase.php');

class FormFactoryTest extends PiwiTestCase {

	function init() {
		FormFactory :: initialize(dirname(__FILE__) . '/data/forms.xml');
	}
	
	function testGetFormByCorrectIdButNonInitializedFormFactory() {
		$this->expectException(PiwiException, 'FormFactory should not be initialized.');
		$form = FormFactory :: getFormById('testForm');
	}	
	
	function testGetFormByWrongId() {
		$this->init();
		$this->expectException(PiwiException, 'Connector should not exist.');
		$form = FormFactory :: getFormById('666');
	}

	function testGetFormByCorrectId() {
		$this->init();
		$form = FormFactory :: getFormById('testForm');
		$this->assertIsA($form, DOMXPath, 'Form has invalid type.');
		
		// get it again to test caching
		$form = FormFactory :: getFormById('testForm');
		$this->assertIsA($form, DOMXPath, 'Form has invalid type.');
	}
	
	function testInitializeFormFactoryWithNonExistingFile() {
		FormFactory :: initialize(dirname(__FILE__) . '/data/666.xml');
		$this->expectException(PiwiException, 'Forms definition file should not exist.');
		$form = FormFactory :: getFormById('testForm');
	}
}
?>