<?php
/**
 * Used to initiate and retrieve Generators.
 */
class GeneratorFactory {
	/** Singleton instance of the GeneratorFactory. */
	private static $generatorFactoryInstance = null;

	/** Map of the generators that have already been initialized. */
	private $generators = array ();

	/** Path of the file containing the xml-definition of the generators that can be used. */
	private $generatorsXMLPath = null;

	/** The generators definition as xml. */
	private $xml = null;

	/**
	 * Constructor.
	 * Private constructor since only used by 'initialize'.
	 * @param string $generatorsXMLPath Path of the file containing the xml-definition of the generators that can be used.
	 */
	private function __construct($generatorsXMLPath) {
		$this->generatorsXMLPath = $generatorsXMLPath;
	}

	/**
	 * Initializes the singleton instance of this Class.
	 * @param string $generatorsXMLPath Path of the file containing the xml-definition of the generators that can be used.
	 */
	public static function initialize($generatorsXMLPath) {
		if (self :: $generatorFactoryInstance == null) {
			self :: $generatorFactoryInstance = new GeneratorFactory($generatorsXMLPath);
		}
	}

	/**
	 * Returns the Generator with the given id.
	 * If the Generator is already instanciated, the instance will be returned.
	 * Otherwise a new instance will be created and stored in a map.
	 * @param string $generatorId The id of the Generator.
	 * @return Generator An instance of type Generator.
	 */
	private function getGeneratorById($generatorId) {
		$this->initializeGenerator($generatorId);
		return $this->generators[$generatorId];
	}

	/**
	 * Constructs an instance of the Generator with the given id.
	 * Arguments from the generator xml file will be passed to the Generator.
	 * @param string $generatorId The id of the Generator.
	 */
	private function initializeGenerator($generatorId) {
		if (isset($this->generators[$generatorId])) {
			return;
		}
		if ($this->xml == null) {
			if (!file_exists($this->generatorsXMLPath)) {
				throw new PiwiException(
					"Could not find the generators definition file (Path: '" . $this->generatorsXMLPath . "').", 
					PiwiException :: ERR_NO_XML_DEFINITION);
			}
			$this->xml = simplexml_load_file($this->generatorsXMLPath);
		}

		$result = $this->xml->xpath("//generator[@id='" . $generatorId . "']");
		if ($result != null) {
			$class = new ReflectionClass((string)$result[0]->attributes()->class);
			$generator = $class->newInstance();

			if (!$generator instanceof Generator){
				throw new PiwiException(
					"The Class with id '" . $generatorId . "' is not an instance of Generator.", 
					PiwiException :: ERR_WRONG_TYPE);
			}

			foreach ($result[0]->children() as $attribute) {
				$generator->setProperty((string) $attribute->getName(), (string) $attribute);
			}
			$this->generators[$generatorId] = $generator;
		} else {
			throw new PiwiException(
				"Could not find the generator '" . $generatorId . "' in the generators definition file (Path: '" . $this->generatorsXMLPath . "').", 
				PiwiException :: ERR_NO_XML_DEFINITION);
		}
	}

	/**
	 * Used within the XSLT-Stylesheets to interpret the <generator /> tag. 
	 * @param string $generatorId The id of the Generator.
	 * @return Generator The Generator with the given id.
	 */
	public static function callGenerator($generatorId) {
		if (self :: $generatorFactoryInstance == null) {
			throw new PiwiException(
				"Illegal State: Invoke static method 'initialize' on '" . __CLASS__ . "' before accessing a Generator.", 
				PiwiException :: ERR_ILLEGAL_STATE);
		}

		$xml = self :: $generatorFactoryInstance->getGeneratorById($generatorId)->generate();
		$doc = new DOMDocument;
		$doc->loadXml($xml);
		return $doc;
	}
}
?>