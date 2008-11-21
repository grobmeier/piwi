<?php
require_once ('test/PiwiTestCase.php');

class ConnectorFactoryTest extends PiwiTestCase {

	function init() {
		ConnectorFactory :: initialize(dirname(__FILE__) . '/connectors.xml');
	}
	
	function testGetConnectorByCorrectIdButNonInitializedConnectorFactory() {
		$this->expectException(PiwiException);
		$connector = ConnectorFactory :: getConnectorById('testConnector');
	}	
	
	function testGetConnectorByWrongId() {
		$this->init();
		$this->expectException(PiwiException);
		$connector = ConnectorFactory :: getConnectorById('666');
	}

	function testGetConnectorByCorrectId() {
		$this->init();
		$connector = ConnectorFactory :: getConnectorById('testConnector');
		$this->assertIsA($connector, Connector);
		
		// get it again to test caching
		$connector = ConnectorFactory :: getConnectorById('testConnector');
		$this->assertIsA($connector, Connector);
	}

	function testGetConnectorByCorrectIdButWrongInterface() {
		$this->init();
		$this->expectException(PiwiException);
		$connector = ConnectorFactory :: getConnectorById('wrongInterface');
	}
	
	function testInitializeConnectorFactoryWithNonExistingFile() {
		ConnectorFactory :: initialize(dirname(__FILE__) . '/666.xml');
		$this->expectException(PiwiException);
		$connector = ConnectorFactory :: getConnectorById('testConnector');
	}
}
?>