<?php
class ConnectorFactoryTest extends UnitTestCase {

	private $connectorFactory;
	
	function init() {
		$this->connectorFactory = new ConnectorFactory();
		$this->connectorFactory->setConnectorsXMLPath(dirname(__FILE__) . '/data/connectors.xml');
	}
	
	function testGetConnectorByCorrectIdButNonInitializedConnectorFactory() {
		$this->connectorFactory = new ConnectorFactory();
		$this->expectException('PiwiException', 'ConnectorFactory should not be initialized.');
		$connector = $this->connectorFactory->getConnectorById('testConnector');
	}	
	
	function testGetConnectorByWrongId() {
		$this->init();
		$this->expectException('PiwiException', 'Connector should not exist.');
		$connector = $this->connectorFactory->getConnectorById('666');
	}

	function testGetConnectorByCorrectId() {
		$this->init();
		$connector = $this->connectorFactory->getConnectorById('testConnector');
		$this->assertIsA($connector, 'Connector', 'Connector has invalid type.');
		
		// get it again to test caching
		$connector = $this->connectorFactory->getConnectorById('testConnector');
		$this->assertIsA($connector, 'Connector', 'Connector has invalid type.');
	}

	function testGetConnectorByCorrectIdButWrongInterface() {
		$this->init();
		$this->expectException('PiwiException', 'Connector has correct type, but wrong type was expected.');
		$connector = $this->connectorFactory->getConnectorById('wrongInterface');
	}
	
	function testInitializeConnectorFactoryWithNonExistingFile() {
		$this->connectorFactory = new ConnectorFactory();
		$this->connectorFactory->setConnectorsXMLPath(dirname(__FILE__) . '/data/666.xml');
		$this->expectException('PiwiException', 'Connectors definition file should not exist.');
		$connector = $this->connectorFactory->getConnectorById('testConnector');
	}
}
?>