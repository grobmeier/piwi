<?php
/**
 * Used to initiate and retrieve Objects for Dependency Injection.
 */
class BeanFactory {
	/** Singleton instance of the ObjectFactory. */
	private static $instance = null;

	/** Map of the objects that have already been initialized. */
	private $beans = array();

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
		self :: $instance = new BeanFactory($contextXMLPath);
	}

	/**
	 * Returns the Object with the given id.
	 * @param string $beanId The id of the Object.
	 * @return stdclass The requested Object.
	 */
	public static function getBeanById($beanId) {		
		if (self :: $instance == null) {
			throw new PiwiException("Illegal State: Invoke static method 'initialize' on '"
					. __CLASS__ . "' before accessing an Object.", 
				PiwiException :: ERR_ILLEGAL_STATE);
		}
		
		// Check if object is a singleton and has been cached already
		if (isset(self :: $instance->beans[$beanId])) {
			return self :: $instance->beans[$beanId];
		}
		
		return self :: $instance->_initializeBean($beanId);
	}

	/**
	 * Constructs an instance of the Object with the given id.
	 * Arguments from the context xml file will be passed to the Object.
	 * @param string $beanId The id of the Object.
	 * @return stdclass The requested Object.
	 */
	private function _initializeBean($beanId) {
		if ($this->domXPath == null) {
    		$this->_loadContextXML();
    	}
    	
    	$domNodeList = $this->domXPath->query("/context:context/context:bean[@id='" . $beanId . "']");

        if ($domNodeList->length == 1) {
        	$class = new ReflectionClass($domNodeList->item(0)->getAttribute('class'));
        	
        	$params = self::_initializeConstructorArgs($beanId);
        	
        	$instance = $class->newInstanceArgs($params);
			
			$properties = self::_initializeProperties($beanId, $instance);
        	
			// If instance should be used as a singleton cache the instance
			if ($domNodeList->item(0)->getAttribute("singleton")) {
				$this->beans[$beanId] = $instance;
			}
        	return $instance;
        } else if ($domNodeList->length > 1) {
       		throw new PiwiException("The id of the requested object is not unique (Object: '" . 
       				$beanId . "').", 
				PiwiException :: INVALID_XML_DEFINITION);
        } else {
			throw new PiwiException("Could not find the requested object '" . $beanId .
					"' in the context definition file (Path: '" . $this->contextXMLPath . "').", 
				PiwiException :: ERR_NO_XML_DEFINITION);
        }
	}
	
	/**
	 * Initializes the constructor arguments for the bean with the referenced id.
	 * Constructor arguments are noted as 
	 * 
	 * <code>
	 * <constructor-args>
	 * 		<arg>value</arg>
	 * 		<arg>...</arg>
	 * </constructor-args>
	 * </code>
	 * 
	 * @param beanId - the id of the bean
	 */
	private function _initializeConstructorArgs($beanId) {
		$params = array();
        	
    	$contructorArgs = $this->domXPath->query("/context:context/context:bean[@id='" . $beanId . "']" . 
			"/context:constructor-args");
		if ($contructorArgs->length == 1) {					
        	foreach ($contructorArgs->item(0)->childNodes as $childNode) {
        		if ($childNode->nodeType == XML_ELEMENT_NODE) {
	        		if ($childNode->nodeName == 'bean') {        			
	        			$params[] = self :: getBeanById($childNode->getAttribute('ref'));
	        		} else {
	        			$params[] = $childNode->nodeValue;
	        		}
        		}      			
			}
		}
		return $params;
	}
	
	private function _initializeProperties($beanId, $instance) {
		$params = array();
        	
    	$properties = $this->domXPath->query("/context:context/context:bean[@id='" . $beanId . "']" . 
			"/context:property");
			
		foreach ($properties as $childNode) {
        	$name = $childNode->getAttribute('name');
			$ref  = $childNode->getAttribute('ref');
			
			$clazz = new ReflectionClass($instance);
			
			if($clazz->hasProperty($name) && $clazz->getProperty($name)->isPublic()) {
				$prop = $clazz->getProperty($name);
				$prop->setValue($instance, self::getBeanById($ref));
			} else if($clazz->hasMethod($name)) {
				$method = $clazz->getMethod($name);
				$method->invoke($instance, self::getBeanById($ref));
			} else if($clazz->hasMethod('set'.ucfirst($name))) {
				$method = $clazz->getMethod('set'.ucfirst($name));
				$method->invoke($instance, self::getBeanById($ref));
			}
		}
		
		return $params;
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