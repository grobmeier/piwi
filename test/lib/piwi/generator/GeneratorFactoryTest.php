<?php
require_once ('test/PiwiTestCase.php');

class GeneratorFactoryTest extends PiwiTestCase {
	
	function before($message) {
		GeneratorFactory :: initialize(dirname(__FILE__) . '/generators.xml');
	}
	
	function testCallGeneratorByWrongId() {
		$this->expectException(PiwiException);
		$content = GeneratorFactory :: callGenerator('666');
	}

	function testCallGeneratorByCorrectIdButWrongInterface() {
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