<?php
require_once ('test/PiwiTestCase.php');

class GeneratorFactoryTest extends PiwiTestCase {
	
	function init() {
		GeneratorFactory :: initialize(dirname(__FILE__) . '/generators.xml');
	}
	
	function testCallGeneratorByCorrectIdButButNonInitializedGeneratorFactory() {
		$this->expectException(PiwiException);
		$content = GeneratorFactory :: callGenerator('testGenerator');
	}
	
	function testCallGeneratorByCorrectId() {
		$this->init();
		$content = GeneratorFactory :: callGenerator('testGenerator');
		$this->assertIsA($content, DOMDocument);
		
		// get it again to test caching
		$content = GeneratorFactory :: callGenerator('testGenerator');
		$this->assertIsA($content, DOMDocument);		
	}
	
	function testCallGeneratorByWrongId() {
		$this->init();
		$this->expectException(PiwiException);
		$content = GeneratorFactory :: callGenerator('666');
	}

	function testCallGeneratorByCorrectIdButWrongInterface() {
		$this->init();
		$this->expectException(PiwiException);
		$content = GeneratorFactory :: callGenerator('wrongInterface');
	}
	
	function testInitializeGeneratorFactoryWithNonExistingFile() {
		GeneratorFactory :: initialize(dirname(__FILE__) . '/666.xml');
		$this->expectException(PiwiException);
		$content = GeneratorFactory :: callGenerator('testGenerator');
	}
}
?>