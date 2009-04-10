<?php
class ConnectorFactoryTest extends UnitTestCase {

	function init() {
		ConnectorFactory :: initialize(dirname(__FILE__) . '/data/connectors.xml');
	}
	
	function testGetConnectorByCorrectIdButNonInitializedConnectorFactory() {
		$this->expectException(PiwiException, 'ConnectorFactory should not be initialized.');
		$connector = ConnectorFactory :: getConnectorById('testConnector');
	}	
	
	function testGetConnectorByWrongId() {
		$this->init();
		$this->expectException(PiwiException, 'Connector should not exist.');
		$connector = ConnectorFactory :: getConnectorById('666');
	}

	function testGetConnectorByCorrectId() {
		$this->init();
		$connector = ConnectorFactory :: getConnectorById('testConnector');
		$this->assertIsA($connector, Connector, 'Connector has invalid type.');
		
		// get it again to test caching
		$connector = ConnectorFactory :: getConnectorById('testConnector');
		$this->assertIsA($connector, Connector, 'Connector has invalid type.');
	}

	function testGetConnectorByCorrectIdButWrongInterface() {
		$this->init();
		$this->expectException(PiwiException, 'Connector has correct type, but wrong type was expected.');
		$connector = ConnectorFactory :: getConnectorById('wrongInterface');
	}
	
	function testInitializeConnectorFactoryWithNonExistingFile() {
		ConnectorFactory :: initialize(dirname(__FILE__) . '/data/666.xml');
		$this->expectException(PiwiException, 'Connectors definition file should not exist.');
		$connector = ConnectorFactory :: getConnectorById('testConnector');
	}
}
?>