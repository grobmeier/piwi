<?php
require_once ('test/PiwiTestCase.php');

class ObjectFactoryTest extends PiwiTestCase {

	function init() {
		ObjectFactory :: initialize(dirname(__FILE__) . '/data/context.xml');
	}
	
	function testGetObjectByCorrectIdButNonInitializedObjectFactory() {
		$this->expectException(PiwiException, 'ObjectFactory should not be initialized.');
		$object = ObjectFactory :: getObjectById('testObject1');
	}	
	
	function testGetObjectByWrongId() {		
		$this->init();
		$this->expectException(PiwiException, 'Object should not exist.');
		$connector = ObjectFactory :: getObjectById('666');
	}

	function testGetObjectByCorrectId() {
		$this->init();
		$object1 = ObjectFactory :: getObjectById('testObject1');
		$this->assertIsA($object1, TestObject1, 'Object has invalid type.');
		
		// get it again to test if a new instance has been created (no singleton)
		$object2 = ObjectFactory :: getObjectById('testObject1');
		$this->assertClone($object1, $object2, 'Objects shoud not be singleton, two instances expected.');
	}
	
	function testGetObjectSingletonByCorrectId() {
		$this->init();
		$object1 = ObjectFactory :: getObjectById('testObject2');
		$this->assertIsA($object1, TestObject2, 'Object has invalid type.');
		
		// Check parameters
		$this->assertEqual($object1->paramString, 'test_string', 'Object has invalid parameter.');
		$this->assertTrue($object1->paramBoolean, 'Object has invalid parameter.');
		$this->assertEqual($object1->paramInteger, 666, 'Object has invalid parameter.');
		$this->assertEqual($object1->paramFloat, 12.3, 'Object has invalid parameter.');
		
		// get it again to test if a new instance has been created (no singleton)
		$object2 = ObjectFactory :: getObjectById('testObject2');
		$this->assertTrue($object1 === $object2, 'Objects be singleton, one instance expected.');
	}

	function testGetObjectWithObjectReferenceByCorrectId() {
		$this->init();
		$object = ObjectFactory :: getObjectById('testObject3');
		$this->assertIsA($object, TestObject3, 'Object has invalid type.');
		
		// Check parameters
		$this->assertIsA($object->testObject2, TestObject2, 'Object has invalid parameter.');
		$this->assertEqual($object->testObject2->paramString, 'test_string', 'Object has invalid parameter.');
		$this->assertTrue($object->testObject2->paramBoolean, 'Object has invalid parameter.');
		$this->assertEqual($object->testObject2->paramInteger, 666, 'Object has invalid parameter.');
		$this->assertEqual($object->testObject2->paramFloat, 12.3, 'Object has invalid parameter.');
	}
	
	function testInitializeObjectFactoryWithNonExistingFile() {
		ObjectFactory :: initialize(dirname(__FILE__) . '/data/666.xml');
		$this->expectException(PiwiException, 'Objects definition file should not exist.');
		$object = ObjectFactory :: getObjectById('testObject1');
	}
}
?>