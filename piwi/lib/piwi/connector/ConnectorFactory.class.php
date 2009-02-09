<?php
/**
 * Used to initiate and retrieve Connectors.
 */
class ConnectorFactory {
	/** Singleton instance of the ConnectorFactory. */
	private static $instance = null;

	/** Map of the connectors that have already been initialized. */
	private $connectors = array ();

	/** Path of the file containing the xml-definition of the connectors that can be used. */
	private $connectorsXMLPath = null;

	/** The connectors definition as xml. */
	private $xml = null;

	/**
	 * Constructor.
	 * Private constructor since only used by 'initialize'.
	 * @param string $connectorsXMLPath Path of the file containing the xml-definition
	 * of the connectors that can be used.
	 */
	private function __construct($connectorsXMLPath) {
		$this->connectorsXMLPath = $connectorsXMLPath;
	}

	/**
	 * Initializes the singleton instance of this Class.
	 * @param string $connectorsXMLPath Path of the file containing the xml-definition 
	 * of the connectors that can be used.
	 */
	public static function initialize($connectorsXMLPath) {
		self :: $instance = new ConnectorFactory($connectorsXMLPath);
	}

	/**
	 * Returns the Connector with the given id.
	 * If the Connector is already instanciated, the instance will be returned.
	 * Otherwise a new instance will be created and stored in a map.
	 * @param string $connectorId The id of the Connector.
	 * @return Connector An instance of type Connector.
	 */
	public static function getConnectorById($connectorId) {
		if (self :: $instance == null) {
			throw new PiwiException("Illegal State: Invoke static method 'initialize' on '" .
					__CLASS__ . "' before accessing a Connector.", 
				PiwiException :: ERR_ILLEGAL_STATE);
		}

		self :: $instance->_initializeConnector($connectorId);
		
		return self :: $instance->connectors[$connectorId];
	}
	
	/**
	 * Constructs an instance of the Connector with the given id.
	 * Arguments from the connector xml file will be passed to the Connector.
	 * @param string $connectorId The id of the Connector.
	 */
	private function _initializeConnector($connectorId) {
		if (isset($this->connectors[$connectorId])) {
			return;
		}
		if ($this->xml == null) {
			if (!file_exists($this->connectorsXMLPath)) {
				throw new PiwiException("Could not find the connectors definition file (Path: '" .
						$this->connectorsXMLPath . "').", 
					PiwiException :: ERR_NO_XML_DEFINITION);
			}
			$this->xml = simplexml_load_file($this->connectorsXMLPath);
			$this->xml->registerXPathNamespace('connectors', 'http://piwi.googlecode.com/xsd/connectors');
		}

		$result = $this->xml->xpath("//connectors:connector[@id='" . $connectorId . "']");
		if ($result != null) {
			$class = new ReflectionClass((string)$result[0]->attributes()->class);
			$connector = $class->newInstance();

			if (!$connector instanceof Connector) {
				throw new PiwiException("The Class with id '" . $connectorId .
						"' is not an instance of Connector.", 
					PiwiException :: ERR_WRONG_TYPE);
			}
			
			foreach ($result[0]->children() as $attribute) {
				$connector->setProperty((string) $attribute->getName(), (string) $attribute);
			}
			$this->connectors[$connectorId] = $connector;
		} else {
			throw new PiwiException("Could not find the connector '" . $connectorId .
					"' in the connectors definition file (Path: '" . $this->connectorsXMLPath . "').", 
				PiwiException :: ERR_NO_XML_DEFINITION);
		}
	}
}
?>