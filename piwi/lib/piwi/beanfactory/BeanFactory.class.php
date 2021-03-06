<?php
/**
 * Used to initiate and retrieve Objects for Dependency Injection.
 * 
 * Several scopes can be set:
 * <ul>
 * <li>request: an object is available for the whole request (singleton in PHP manner)</li>
 * <li>session: an object is available for the whole session</li>
 * <li>prototype: an object is always newly created by each context request</li>
 * </ul>
 * 
 * Spring Framework supports the singleton scope, too. This means a bean is available for the
 * the whole container for the whole runtime. This is not (easily) possible for PHP applications
 * and should only be used under special circumstances. The bean must be serialized to harddisk
 * for an interchange between sessions and request. However, this could be done with a BeanSerializer,
 * which is in the scope for a latter Piwi release.
 */
class BeanFactory {
	/** Singleton instance of the ObjectFactory. */
	private static $instance = null;

	/** Map of the objects that have already been initialized. */
	private $beans = array();

	/** Path of the file containing the xml-definition of the generators that can be used. */
	private $contextXMLPath = null;

	/** The DOMDocument of the 'context.xml'. May contain user elements later */
	private $contextXml; 
	
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
		if ($contextXMLPath == null) {
			throw new PiwiException('"Context must not be null.', PiwiException::ERR_NO_XML_DEFINITION);
		}

		self :: $instance = new BeanFactory($contextXMLPath);
	}

	/**
	 * Adds a custom context to the BeanFactory.
	 * @param string $contextXMLPath Path of the file containing the xml-definition 
	 * of the generators that can be used.
	 * @param boolean $overwrite Determines whether the existing beans are overridden.
	 */
	public static function addContext($contextXMLPath, $overwrite = false) {
		if (self :: $instance->domXPath == null) {
    		self :: $instance->_loadContextXML();
    	}
    	
    	$userContext = DOMDocument::load($contextXMLPath);
		$userXPath = new DOMXPath($userContext);
		$userXPath->registerNamespace('context', 'http://piwi.googlecode.com/xsd/context');
		$xpathQuery = $userXPath->query('/*/*');
		
		$elements = self :: $instance->contextXml->getElementsByTagName('bean');
		
		$ignore = false;
		for ($i = 0; $i < $xpathQuery->length; $i++) {
				$id = $xpathQuery->item($i)->getAttribute('id');
				
				foreach ($elements as $element) {
					if ($element->getAttribute('id') == $id) {
						if ($overwrite) {
							$parent = $element->parentNode;
							$parent->removeChild($element);
						} else {
							$ignore = true;
						}
						break;
					} 
				}
				if (!$ignore) {
					$node = self :: $instance->contextXml->importNode($xpathQuery->item($i), true);
					self :: $instance->contextXml->documentElement->appendChild($node);
				}
				$ignore = false;
		}
		self :: $instance->_loadContextXML();
	}
	
	/**
	 * Removes the singleton
	 */
	public static function clean() {
		self :: $instance = null;
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
			
			self::_initializeProperties($beanId, $instance);
        	self::_initializePropertyArray($beanId, $instance);
        	
			// If instance should be used as a request singleton cache the instance
			if ($domNodeList->item(0)->getAttribute("scope") == "request") {
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
	 * 		<bean ref="beanID" />
	 * </constructor-args>
	 * </code>
	 * 
	 * @param string beanId The id of the bean.
	 * @return array The arguments as array.
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
	
	/**
	 * Initializes the properties for the bean with the referenced id.
	 * Properties are noted as 
	 * 
	 * <code>
	 * <bean...>
	 * 		<property name="bla" ref="beanID" />
	 * 		<property name="paramString2" value="..." />
	 * </bean>
	 * </code>
	 * @param string $beanId The id of the bean.
	 * @param stdclass $instance The bean whose properties should be initialized.
	 */
	private function _initializeProperties($beanId, $instance) {
    	$properties = $this->domXPath->query("/context:context/context:bean[@id='" . $beanId . "']" . 
			"/context:property");
			
		foreach ($properties as $childNode) {
        	$name = $childNode->getAttribute('name');
			$ref  = $childNode->getAttribute('ref');
			
			if ($childNode->hasAttribute('ref')) {
				$parameter = self :: getBeanById($childNode->getAttribute('ref'));
			} else if ($childNode->hasAttribute('php')) {
				eval('$parameter = ' . $childNode->getAttribute('php') . ';');
			} else {
				$parameter = $childNode->getAttribute('value');
			}
			
			$clazz = new ReflectionClass($instance);
			
			if ($clazz->hasProperty($name) && $clazz->getProperty($name)->isPublic()) {
				$prop = $clazz->getProperty($name);
				$prop->setValue($instance, $parameter);
			} else if ($clazz->hasMethod($name)) {
				$method = $clazz->getMethod($name);
				$method->invoke($instance, $parameter);
			} else if ($clazz->hasMethod('set' . ucfirst($name))) {
				$method = $clazz->getMethod('set' . ucfirst($name));
				$method->invoke($instance, $parameter);
			}
		}
	}
	
	/**
	 * Initializes an array out of a properties list.
	 * Properties are noted as 
	 * 
	 * <code>
	 * <bean...>
	 * 	<properties name="myarray">
	 * 		<property name="key1" value="value1" />
	 * 		<property name="key2" value="value2" />
	 *  </properties>
	 * </bean>
	 * </code>
	 * 
	 * This will result an array of that kind:
	 * 
	 * <code>
	 * $myarray['key1'] = 'value1';
	 * $myarray['key2'] = 'value2';
	 * </code>
	 * 
	 * A setter method will be called on the target bean: setMyarray($myarray)
	 * 
	 * @param string $beanId The id of the bean.
	 * @param stdclass $instance The bean whose properties should be initialized.
	 */
	private function _initializePropertyArray($beanId, $instance) {
		$properties = $this->domXPath->query("/context:context/context:bean[@id='" . $beanId . "']" . 
			"/context:properties");
			
		foreach ($properties as $childNode) {
			$name = $childNode->getAttribute('name');
			$propertyList = $childNode->getElementsByTagname('property');
			$parameter = array();
			foreach ($propertyList as $property) {
				if ($property->hasAttribute('ref')) {
					$o = self :: getBeanById($property->getAttribute('ref'));
					$parameter[$property->getAttribute('name')] = $o;
				} else if ($property->hasAttribute('php')) {
					eval('$t = ' . $property->getAttribute('php') . ';');
					$parameter[$property->getAttribute('name')] = $t;
				} else {
					$parameter[$property->getAttribute('name')] = $property->getAttribute('value');
				}
			}
			
			$clazz = new ReflectionClass($instance);
			
			if ($clazz->hasProperty($name) && $clazz->getProperty($name)->isPublic()) {
				$prop = $clazz->getProperty($name);
				$prop->setValue($instance, $parameter);
			} else if ($clazz->hasMethod($name)) {
				$method = $clazz->getMethod($name);
				$method->invoke($instance, $parameter);
			} else if ($clazz->hasMethod('set' . ucfirst($name))) {
				$method = $clazz->getMethod('set' . ucfirst($name));
				$method->invoke($instance, $parameter);
			}
		}
	}
	
	/**
     * Loads the 'context.xml'.
     */
    private function _loadContextXML() {
    	if ($this->contextXml == null) {
			if (!file_exists($this->contextXMLPath)) {
				throw new PiwiException("Could not find the context definition file (Path: '" 
						. $this->contextXMLPath . "').", 
					PiwiException :: ERR_NO_XML_DEFINITION);
	    	}
	    	
	    	// Load 'context.xml'
	    	$this->contextXml = DOMDocument::load($this->contextXMLPath);
    	} 
    	
		$this->contextXml->formatOutput = true;
    	// Init DOMXPath which will be used for querying the context
		$this->domXPath = new DOMXPath($this->contextXml);
		$this->domXPath->registerNamespace('context', 'http://piwi.googlecode.com/xsd/context');
    }
}
?>