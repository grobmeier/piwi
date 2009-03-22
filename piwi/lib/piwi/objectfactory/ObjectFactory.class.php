<?php
/**
 * Used to initiate and retrieve Objects for Dependency Injection.
 */
class ObjectFactory {
	/** Singleton instance of the ObjectFactory. */
	private static $instance = null;

	/** Map of the objects that have already been initialized. */
	private $objects = array();

	/** Path of the file containing the xml-definition of the generators that can be used. */
	private $contextXMLPath = null;

	/** The DOMXPath of the 'context.xml'. */
	private $domXPath = null;

	/**
	 * Constructor.
	 * Private constructor since only used by 'initialize'.
	 * @param string $contextXMLPath Path of the file containing the xml-definition 
	 * of the objects that can be used.
	 */
	private function __construct($contextXMLPath) {
		$this->contextXMLPath = $contextXMLPath;
	}

	/**
	 * Initializes the singleton instance of this Class.
	 * @param string $contextXMLPath Path of the file containing the xml-definition 
	 * of the generators that can be used.
	 */
	public static function initialize($contextXMLPath) {
		self :: $instance = new ObjectFactory($contextXMLPath);
	}

	/**
	 * Returns the Object with the given id.
	 * @param string $objectId The id of the Object.
	 * @return stdclass The requested Object.
	 */
	public static function getObjectById($objectId) {		
		if (self :: $instance == null) {
			throw new PiwiException("Illegal State: Invoke static method 'initialize' on '"
					. __CLASS__ . "' before accessing an Object.", 
				PiwiException :: ERR_ILLEGAL_STATE);
		}
		
		// Check if object is a singleton and has been cached already
		if (isset(self :: $instance->objects[$objectId])) {
			return self :: $instance->objects[$objectId];
		}
		
		return self :: $instance->_initializeObject($objectId);
	}

	/**
	 * Constructs an instance of the Object with the given id.
	 * Arguments from the context xml file will be passed to the Object.
	 * @param string $objectId The id of the Object.
	 * @return stdclass The requested Object.
	 */
	private function _initializeObject($objectId) {
		if ($this->domXPath == null) {
    		$this->_loadContextXML();
    	}
    	
    	$domNodeList = $this->domXPath->query("/context:context/context:bean[@id='" . $objectId . "']");

        if ($domNodeList->length == 1) {
        	$class = new ReflectionClass($domNodeList->item(0)->getAttribute('class'));
        	$params = array();
        	
        	$contructorArgs = $this->domXPath->query("/context:context/context:bean[@id='" . $objectId . "']" . 
				"/context:constructor-args");
			if ($contructorArgs->length == 1) {					
	        	foreach ($contructorArgs->item(0)->childNodes as $childNode) {
	        		if ($childNode->nodeType == XML_ELEMENT_NODE) {
		        		if ($childNode->nodeName == 'bean') {        			
		        			$params[] = self :: getObjectById($childNode->getAttribute('ref'));
		        		} else {
		        			$params[] = $childNode->nodeValue;
		        		}
	        		}      			
				}
			}
			$instance = $class->newInstanceArgs($params);
			
			// If instance should be used as a singleton cache the instance
			if ($domNodeList->item(0)->getAttribute("singleton")) {
				$this->objects[$objectId] = $instance;
			}
        	return $instance;
        } else if ($domNodeList->length > 1) {
       		throw new PiwiException("The id of the requested object is not unique (Object: '" . 
       				$objectId . "').", 
				PiwiException :: INVALID_XML_DEFINITION);
        } else {
			throw new PiwiException("Could not find the requested object '" . $objectId .
					"' in the context definition file (Path: '" . $this->contextXMLPath . "').", 
				PiwiException :: ERR_NO_XML_DEFINITION);
        }
	}
	
	/**
     * Loads the 'context.xml'.
     */
    private function _loadContextXML() {
		if (!file_exists($this->contextXMLPath)) {
			throw new PiwiException("Could not find the context definition file (Path: '" 
					. $this->contextXMLPath . "').", 
				PiwiException :: ERR_NO_XML_DEFINITION);
    	}
    	
    	// Load 'context.xml'
    	$contextXml = DOMDocument::load($this->contextXMLPath);

		// Init DOMXPath which will be used for querying the context
		$this->domXPath = new DOMXPath($contextXml);
		$this->domXPath->registerNamespace('context', 'http://piwi.googlecode.com/xsd/context');
    }
}
?>