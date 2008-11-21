<?php
require_once ('test/PiwiTestCase.php');

class ConnectorFactoryTest extends PiwiTestCase {

	function before($message) {
		ConnectorFactory :: initialize(dirname(__FILE__) . '/connectors.xml');
	}
	
	function testGetConnectorByWrongId() {
		$this->expectException(PiwiException);
		$connector = ConnectorFactory :: getConnectorById('666');
	}

	function testGetConnectorByCorrectId() {
		$connector = ConnectorFactory :: getConnectorById('testConnector');
		$this->assertTrue($connector instanceof Connector);
		
		// get it again to test caching
		$connector = ConnectorFactory :: getConnectorById('testConnector');
		$this->assertTrue($connector instanceof Connector);
	}

	function testGetConnectorByCorrectIdButWrongInterface() {
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