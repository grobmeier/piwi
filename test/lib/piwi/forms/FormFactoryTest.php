<?php
require_once ('test/PiwiTestCase.php');

class FormFactoryTest extends PiwiTestCase {

	function init() {
		FormFactory :: initialize(dirname(__FILE__) . '/forms.xml');
	}
	
	function testGetFormByCorrectIdButNonInitializedFormFactory() {
		$this->expectException(PiwiException);
		$form = FormFactory :: getFormById('testForm');
	}	
	
	function testGetFormByWrongId() {
		$this->init();
		$this->expectException(PiwiException);
		$form = FormFactory :: getFormById('666');
	}

	function testGetFormByCorrectId() {
		$this->init();
		$form = FormFactory :: getFormById('testForm');
		$this->assertIsA($form, DOMXPath);
		
		// get it again to test caching
		$form = FormFactory :: getFormById('testForm');
		$this->assertIsA($form, DOMXPath);
	}
	
	function testInitializeFormFactoryWithNonExistingFile() {
		FormFactory :: initialize(dirname(__FILE__) . '/666.xml');
		$this->expectException(PiwiException);
		$form = FormFactory :: getFormById('testForm');
	}
}
?>