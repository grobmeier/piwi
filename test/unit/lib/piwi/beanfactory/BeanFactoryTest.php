<?php
$GLOBALS['TEST'] = "folder/";
DEFINE('CONTENT_TEST',"subfolder/");

class BeanFactoryTest extends UnitTestCase {

	function init() {
		BeanFactory :: initialize(dirname(__FILE__) . '/data/context.xml');
	}
	
	function testGetBeanByCorrectIdButNonInitializedBeanFactory() {
		$this->expectException('PiwiException', 'BeanFactory should not be initialized.');
		$object = BeanFactory :: getBeanById('testObject1');
	}	
	
	function testGetBeanByWrongId() {		
		$this->init();
		$this->expectException('PiwiException', 'Object should not exist.');
		$connector = BeanFactory :: getBeanById('666');
	}

	function testGetBeanByCorrectId() {
		$this->init();
		$object1 = BeanFactory :: getBeanById('testObject1');
		$this->assertIsA($object1, 'TestObject1', 'Object has invalid type.');
		$this->assertEqual($object1->getFasel(), 'folder/subfolder/');
		// get it again to test if a new instance has been created (no singleton)
		$object2 = BeanFactory :: getBeanById('testObject1');
		$this->assertClone($object1, $object2, 'Objects shoud not be singleton, two instances expected.');
	}
	
	function testGetBeanSingletonByCorrectId() {
		$this->init();
		$object1 = BeanFactory :: getBeanById('testObject2');
		$this->assertIsA($object1, 'TestObject2', 'Object has invalid type.');
		
		// Check parameters
		$this->assertEqual($object1->paramString, 'test_string', 'Object has invalid parameter.');
		$this->assertTrue($object1->paramBoolean, 'Object has invalid parameter.');
		$this->assertEqual($object1->paramInteger, 666, 'Object has invalid parameter.');
		$this->assertEqual($object1->paramFloat, 12.3, 'Object has invalid parameter.');
		
		// get it again to test if a new instance has been created (no singleton)
		$object2 = BeanFactory :: getBeanById('testObject2');
		$this->assertTrue($object1 === $object2, 'Objects be singleton, one instance expected.');
	}

	function testGetBeanWithObjectReferenceByCorrectId() {
		$this->init();
		$object = BeanFactory :: getBeanById('testObject3');
		$this->assertIsA($object, 'TestObject3', 'Object has invalid type.');
		
		// Check parameters
		$this->assertEqual($object->paramString, 'test_string', 'Object has invalid parameter.');
		$this->assertEqual($object->paramString2, 'test_string2', 'Object has invalid parameter.');
		
		$this->assertIsA($object->testObject2, 'TestObject2', 'Object has invalid parameter.');
		$this->assertEqual($object->testObject2->paramString, 'test_string', 'Object has invalid parameter.');
		$this->assertTrue($object->testObject2->paramBoolean, 'Object has invalid parameter.');
		$this->assertEqual($object->testObject2->paramInteger, 666, 'Object has invalid parameter.');
		$this->assertEqual($object->testObject2->paramFloat, 12.3, 'Object has invalid parameter.');
		
		$this->assertIsA($object->blub, 'TestObject2', 'Object has invalid parameter.');
		$this->assertIsA($object->blubberParam, 'TestObject1', 'Object has invalid parameter.');
		$this->assertIsA($object->blubber2Param, 'TestObject2', 'Object has invalid parameter.');
		$this->assertIsA($object->blubber2Param, 'TestObject2', 'Object has invalid parameter.');
		$this->assertIsA($object->getBla(), 'TestObject2', 'Object has invalid parameter.');
		
		$r = $object->getMyarray();
		$this->assertEqual($r['key1'], 'value1', 'Object has invalid parameter.');
		$this->assertEqual($r['key2'], 'value2', 'Object has invalid parameter.');
	}
	
	function testInitializeBeanFactoryWithNonExistingFile() {
		BeanFactory :: initialize(dirname(__FILE__) . '/data/666.xml');
		$this->expectException('PiwiException', 'Objects definition file should not exist.');
		$object = BeanFactory :: getBeanById('testObject1');
	}
	
	function testAddCustomContext() {
		$this->init();
		BeanFactory :: addContext(dirname(__FILE__) . '/data/additionalContext.xml', false);
		
		$object1 = BeanFactory :: getBeanById('testObject2');
		
		$this->assertIsA($object1, 'TestObject2', 'Object has invalid type.');
		
		// Check parameters
		$this->assertEqual($object1->paramString, 'test_string', 'Object has invalid parameter.');
		$this->assertTrue($object1->paramBoolean, 'Object has invalid parameter.');
		$this->assertEqual($object1->paramInteger, 666, 'Object has invalid parameter.');
		$this->assertEqual($object1->paramFloat, 12.3, 'Object has invalid parameter.');
		
		$object2 = BeanFactory :: getBeanById('testObject4');
		$this->assertIsA($object2, 'TestObject1', 'Object has invalid type.');
		$this->assertEqual($object2->getFasel(), 'folder/subfolder/');
	}
	
	function testAddCustomContextWithOverride() {
		$this->init();
		BeanFactory :: addContext(dirname(__FILE__) . '/data/additionalContext.xml', true);
		
		$object1 = BeanFactory :: getBeanById('testObject2');
		
		$this->assertIsA($object1, 'TestObject2', 'Object has invalid type.');
		
		// Check parameters
		$this->assertEqual($object1->paramString, 'test_string2', 'Object has invalid parameter.');
		$this->assertFalse($object1->paramBoolean, 'Object has invalid parameter.');
		$this->assertEqual($object1->paramInteger, 777, 'Object has invalid parameter.');
		$this->assertEqual($object1->paramFloat, 45.6, 'Object has invalid parameter.');
		
		$object2 = BeanFactory :: getBeanById('testObject4');
		$this->assertIsA($object2, 'TestObject1', 'Object has invalid type.');
		$this->assertEqual($object2->getFasel(), 'folder/subfolder/');
	}
}
?>