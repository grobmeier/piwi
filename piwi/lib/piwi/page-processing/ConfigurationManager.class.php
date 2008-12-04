<?php
/**
 * Manages the settings configured in 'config.xml'.
 */
class ConfigurationManager {
	/** Singleton instance of the ConfigurationManager. */
	private static $configurationManagerInstance = null;

	/** The DOMXPath of the 'config.xml'. */
    private $domXPath = null;
    
    /** Path of the file containing the configuration. */
    private $configFilePath = null;
		
	/** Instance of the RoleProvider. */
	private $roleProvider = null;
	
	/**
	 * Constructor.
	 * Private constructor since only used by 'initialize'.
	 * @param string $configFilePath Path of the file containing the configuration.
	 */
	private function __construct($configFilePath) {
		$this->configFilePath = $configFilePath;
	}

	/**
	 * Initializes the singleton instance of this Class.
	 * @param string $configFilePath Path of the file containing the configuration.
	 */
	public static function initialize($configFilePath) {
		self :: $configurationManagerInstance = new ConfigurationManager($configFilePath);
	}

	/**
	 * Returns the singleton instance of the ConfigurationManager.
	 * @return ConfigurationManager The singleton instance of the ConfigurationManager.
	 */
	public static function getInstance() {
		return self::$configurationManagerInstance;
	}
	
    /**
	 * -------------------------------------------------------------------------
	 * >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>Configuration<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	 * -------------------------------------------------------------------------
	 */   	     
    /**
     * Returns the cachetime (the time that may pass until the content of the page is regenerated).
     * @return integer The cachetime.
     */
    public function getCacheTime() {
    	if ($this->domXPath == null) {
    		$this->loadConfig();
    	}
    	
    	$result = $this->domXPath->query("/config:configuration/config:cachetime");
    	if($result->length == 1) {
    		return $result->item(0)->nodeValue;
        } else if ($result->length > 1) {
       		throw new PiwiException(
				"Your 'config.xml' is not valid (Path: '" . $this->configFilePath . "').", 
				PiwiException :: INVALID_XML_DEFINITION);
        } else {
            return 0;
        }    	
    }  
    
    /** 
     * Returns the Serializer for the given format or null if none is found.
     * @return Serializer The Serializer for the given format or null if none is found.
     */
    public function getSerializer($extension) {
    	if ($this->domXPath == null) {
    		$this->loadConfig();
    	}
    	
    	$result = $this->domXPath->query("/config:configuration/config:serializers/config:serializer[@extension='" . $extension . "']");
    	if($result->length == 1) {
    		$serializerClass = $result->item(0)->getAttribute("serializer");
    		
	    	if ($serializerClass != null) {
		    	try {
		    		$class = new ReflectionClass($serializerClass);
					$serializer = $class->newInstance();
					
					if (!$serializer instanceof Serializer) {
						$serializer = null;
						if (error_reporting() > E_ERROR) {							
							echo("The Class with name '" . $serializerClass . "' is not an instance of Serializer.");
						}
					}
				} catch (ReflectionException $exception) {		
					if (error_reporting() > E_ERROR) {
						echo("Serializer not found: " . $serializerClass);
					}
				}
	    	}
	    	return $serializer;
        } else if ($result->length > 1) {
       		throw new PiwiException(
				"Your 'config.xml' is not valid (Path: '" . $this->configFilePath . "').", 
				PiwiException :: INVALID_XML_DEFINITION);
        } else {
            return null;
        } 
    }    
    
    /**
     * Returns an array containing all Navigations.
     * The keys in the array, are the names (specified in 'config.xml') which can be placed in the templates.
     * The values are the the generated menus as XHTML.
     * @return array Array containing all Navigations.
     */
    public function getHTMLNavigations() {
    	if ($this->domXPath == null) {
    		$this->loadConfig();
    	}
    	
    	$navigations = array();
    	
    	foreach ($this->domXPath->query("/config:configuration/config:navigationGenerators/config:navigationGenerator") as $generator) {
			try {
				$class = new ReflectionClass($generator->getAttribute('class'));
			    $navigationGenerator = $class->newInstance();			    
			    
			    if (!$navigationGenerator instanceof NavigationGenerator) {
		    		if (error_reporting() > E_ERROR) {
						echo("The Class with name '" . $generator->getAttribute('class') . "' is not an instance of NavigationGenerator.");
					}
					continue;
			    }
			    
			    $pageId = $generator->getAttribute('pageId') == "" ? null : $generator->getAttribute('pageId');
			    if ($pageId == 'CURRENT_PAGE') {
			    	$pageId = Request::getPageId();
			    }	    
			    
			    $depth = $generator->getAttribute('depth') == "" ? -1 : $generator->getAttribute('depth');
			    $includeParent = $generator->getAttribute('includeParent') == "" ? true : $generator->getAttribute('includeParent');

				$customSiteMap = SiteMapHelper::getCustomSiteMap($pageId, $depth, $includeParent);
			    $navigations[$generator->getAttribute('name')] = $navigationGenerator->generate($customSiteMap);

			} catch( ReflectionException $exception ) {
				if (error_reporting() > E_ERROR) {
					echo("Custom Navigation Generator not found: " . $generator->getAttribute('class'));
				}
			}			
		}
		
		return $navigations;
    }
    
    /**
     * Returns the path of the a custom labels file or null if none is specified.
     * @return string The path of the a custom labels file or null if none is specified.
     */
    public function getCustomLabelsPath() {
    	if ($this->domXPath == null) {
    		$this->loadConfig();
    	}
    	
    	$result = $this->domXPath->query("/config:configuration/config:customLabels");
    	if ($result->length == 1) {
    		return $result->item(0)->nodeValue;
        } else {
       		return null;
        }  
    }    
    
    /**
     * Returns the path of the a custom XSLT stylesheet or null if none is specified.
     * @return string The path of the a custom XSLT stylesheet or null if none is specified.
     */
    public function getCustomXSLTStylesheetPath() {
    	if ($this->domXPath == null) {
    		$this->loadConfig();
    	}
    	
    	$result = $this->domXPath->query("/config:configuration/config:customXSLTStylesheet");
    	if ($result->length == 1) {
    		return $result->item(0)->nodeValue;
        } else {
       		return null;
        }    	
    }

	/**
	 * Returns true if authentication is enabled otherwise false.
	 * @return boolean True if authentication is enabled otherwise false.
	 */
	public function isAuthenticationEnabled() {
    	if ($this->domXPath == null) {
    		$this->loadConfig();
    	}
    	
    	$result = $this->domXPath->query("/config:configuration/config:authentication");
    	if ($result->length == 1) {    		
    		return $result->item(0)->getAttribute("enabled");
        } else if ($result->length > 1) {
       		throw new PiwiException(
				"Your 'config.xml' is not valid (Path: '" . $this->configFilePath . "').", 
				PiwiException :: INVALID_XML_DEFINITION);
        } else {
       		return false;
        }
	}
	
    /** 
     * Returns the RoleProvider which manages the authentication of users.
     * @return RoleProvider The RoleProvider which manages the authentication of users.
     */
	public function getRoleProvider() {
		if ($this->roleProvider == null) {
			if ($this->domXPath == null) {
    			$this->loadConfig();
    		}
    		
    		$className = null;
    		
    		$result = $this->domXPath->query("/config:configuration/config:authentication");
	    	if ($result->length == 1) {    		
	    		$className = $result->item(0)->getAttribute("roleProvider");
	        }
	        
		   	$class = new ReflectionClass($className);
			$roleProvider = $class->newInstance();
			
			if (!$roleProvider instanceof RoleProvider) {
				throw new PiwiException(
					"The Class with name '" . $className . "' is not an instance of RoleProvider.", 
					PiwiException :: ERR_WRONG_TYPE);
			}
			$this->roleProvider = $roleProvider;
		}
		return $this->roleProvider;		
	}
	
    /**
     * Returns the id of the login page.
     * @return string The id of the login page.
     */
    public function getLoginPageId() {
    	if ($this->domXPath == null) {
    		$this->loadConfig();
    	}
    	
    	$result = $this->domXPath->query("/config:configuration/config:authentication");
    	if ($result->length == 1) {    		
    		return $result->item(0)->getAttribute("loginPageId");
        } else {        	
            return null;
        }
    }
    
    /**
	 * -------------------------------------------------------------------------
	 * >>>>>>>>>>>>>>>>>>>>>>>>>>>>Private Helper Methods<<<<<<<<<<<<<<<<<<<<<<<
	 * -------------------------------------------------------------------------
	 */   
    /**
     * Loads the 'config.xml'.
     */
    private function loadConfig() {
       	if (!file_exists($this->configFilePath)) {
			throw new PiwiException(
				"Could not find the config file (Path: '" . $this->configFilePath . "').", 
				PiwiException :: ERR_NO_XML_DEFINITION);
    	}
    	
		// Init DOMXPath which will be used for querying the configuration
		$this->domXPath = new DOMXPath(DOMDocument::load($this->configFilePath));
		$this->domXPath->registerNamespace('config', 'http://piwi.googlecode.com/xsd/config');
    }
}
?>