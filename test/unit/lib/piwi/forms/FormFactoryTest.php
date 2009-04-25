<?php
class FormFactoryTest extends UnitTestCase {

	private $formFactory = null;
	
	function init() {
		$this->formFactory = new FormFactory();
		$this->formFactory->setFormsXMLPath(dirname(__FILE__) . '/data/forms.xml');
	}
	
	function testGetFormByCorrectIdButNonInitializedFormFactory() {
		$this->formFactory = new FormFactory();
		$this->expectException(PiwiException, 'FormFactory should not be initialized.');
		$form = $this->formFactory->getFormById('testForm');
	}	
	
	function testGetFormByWrongId() {
		$this->init();
		$this->expectException(PiwiException, 'Connector should not exist.');
		$form = $this->formFactory->getFormById('666');
	}

	function testGetFormByCorrectId() {
		$this->init();
		$form = $this->formFactory->getFormById('testForm');
		$this->assertIsA($form, DOMXPath, 'Form has invalid type.');
		
		// get it again to test caching
		$form = $this->formFactory->getFormById('testForm');
		$this->assertIsA($form, DOMXPath, 'Form has invalid type.');
	}
	
	function testInitializeFormFactoryWithNonExistingFile() {
		$this->formFactory = new FormFactory();
		$this->formFactory->setFormsXMLPath(dirname(__FILE__) . '/data/666.xml');
		$this->expectException(PiwiException, 'Forms definition file should not exist.');
		$form = $this->formFactory->getFormById('testForm');
	}
}
?>