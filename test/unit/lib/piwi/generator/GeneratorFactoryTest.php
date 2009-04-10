<?php
class GeneratorFactoryTest extends UnitTestCase {
	
	function init() {
		GeneratorFactory :: initialize(dirname(__FILE__) . '/data/generators.xml');
	}
	
	function testCallGeneratorByCorrectIdButButNonInitializedGeneratorFactory() {
		$this->expectException(PiwiException, 'GeneratorFactory should not be initialized.');
		$content = GeneratorFactory :: callGenerator('testGenerator');
	}
	
	function testCallGeneratorByCorrectId() {
		$this->init();
		$content = GeneratorFactory :: callGenerator('testGenerator');
		$this->assertIsA($content, DOMDocument, 'Generator has invalid type.');
		
		// get it again to test caching
		$content = GeneratorFactory :: callGenerator('testGenerator');
		$this->assertIsA($content, DOMDocument, 'Generator has invalid type.');		
	}
	
	function testCallGeneratorByWrongId() {
		$this->init();
		$this->expectException(PiwiException, 'Generator should not exist.');
		$content = GeneratorFactory :: callGenerator('666');
	}

	function testCallGeneratorByCorrectIdButWrongInterface() {
		$this->init();
		$this->expectException(PiwiException, 'Generator has correct type, but wrong type was expected.');
		$content = GeneratorFactory :: callGenerator('wrongInterface');
	}
	
	function testInitializeGeneratorFactoryWithNonExistingFile() {
		GeneratorFactory :: initialize(dirname(__FILE__) . '/data/666.xml');
		$this->expectException(PiwiException, 'Generators definition file should not exist.');
		$content = GeneratorFactory :: callGenerator('testGenerator');
	}
}
?>