<?php
/**
 * Used to initiate and retrieve Generators.
 */
class GeneratorFactory {
	/** Singleton instance of the GeneratorFactory. */
	private static $instance = null;

	/** Map of the generators that have already been initialized. */
	private $generators = array();

	/** Path of the file containing the xml-definition of the generators that can be used. */
	private static $generatorsXMLPath = null;

	/** The generators definition as xml. */
	private $xml = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
	}
	
	/**
	 * Set the path of the file containing the xml-definition 
	 * @param string $generatorsXMLPath Path of the file containing the xml-definition 
	 */
	public function setGeneratorsXMLPath($generatorsXMLPath) {
		self :: $generatorsXMLPath = $generatorsXMLPath;
	}

	/**
	 * Returns the Generator with the given id.
	 * If the Generator is already instanciated, the instance will be returned.
	 * Otherwise a new instance will be created and stored in a map.
	 * @param string $generatorId The id of the Generator.
	 * @return Generator An instance of type Generator.
	 */
	private function _getGeneratorById($generatorId) {
		$this->_initializeGenerator($generatorId);
		return $this->generators[$generatorId];
	}

	/**
	 * Constructs an instance of the Generator with the given id.
	 * Arguments from the generator xml file will be passed to the Generator.
	 * @param string $generatorId The id of the Generator.
	 */
	private function _initializeGenerator($generatorId) {
		if (isset($this->generators[$generatorId])) {
			return;
		}
		if ($this->xml == null) {
			if (!file_exists(self :: $generatorsXMLPath)) {
				throw new PiwiException("Could not find the generators definition file (Path: '" . 
						self :: $generatorsXMLPath . "').", 
					PiwiException :: ERR_NO_XML_DEFINITION);
			}
			$this->xml = simplexml_load_file(self :: $generatorsXMLPath);
			$this->xml->registerXPathNamespace('generators', 'http://piwi.googlecode.com/xsd/generators');
		}

		$result = $this->xml->xpath("//generators:generator[@id='" . $generatorId . "']");
		if ($result != null) {
			$class = new ReflectionClass((string)$result[0]->attributes()->class);
			$generator = $class->newInstance();

			if (!$generator instanceof Generator) {
				throw new PiwiException("The Class with id '" . $generatorId .
						"' is not an instance of Generator.", 
					PiwiException :: ERR_WRONG_TYPE);
			}

			foreach ($result[0]->children() as $attribute) {
				$generator->setProperty((string) $attribute->getName(), (string) $attribute);
			}
			$this->generators[$generatorId] = $generator;
		} else {
			throw new PiwiException("Could not find the generator '" . $generatorId .
					"' in the generators definition file (Path: '" . $this->generatorsXMLPath . "').", 
				PiwiException :: ERR_NO_XML_DEFINITION);
		}
	}

	/**
	 * Used within the XSLT-Stylesheets to interpret the <generator /> tag. 
	 * @param string $generatorId The id of the Generator.
	 * @return Generator The Generator with the given id.
	 */
	public static function callGenerator($generatorId) {
		if (self :: $instance == null) {
			self :: $instance = BeanFactory :: getBeanById('generatorFactory');
		}

		$xml = self :: $instance->_getGeneratorById($generatorId)->generate();
		$doc = new DOMDocument();
		$doc->loadXml('<section xmlns="http://piwi.googlecode.com/xsd/piwixml">' . $xml . '</section>');
		return $doc;
	}
}
?>