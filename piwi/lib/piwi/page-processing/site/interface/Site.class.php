<?php
/**
 * Renders the requested page and creates the navigation.
 */
abstract class Site {
	/** Singleton instance of the Site. */
	private static $siteInstance = null;
	
	/** Name of the folder where the content is placed. */
	protected $contentPath = null;

	/** The template of the requested page. */
	private $template = "default.php";
	
	/** The content of the requested page as DOMDocument. */
	private $content = null;
		
	/** Instance of the RoleProvider. */
	private $roleProvider = null;
	
    /**
     * Reads the xml of the requested page and transforms the Generators to Piwi-XML.
     */
    public function generateContent() {
    	$allowedRoles = $this->getAllowedRoles();

    	// If authorization is required check if user has authorization
    	if (!in_array('?', $allowedRoles)) {
			$roleProvider = $this->getRoleProvider();
			
			// Check if user is already logged in
			if (SessionManager::isUserAuthenticated()) {
				// Check whether user has required role
				if (!in_array('*', $allowedRoles) && !$roleProvider->isUserInRole(SessionManager::getUserName(), $allowedRoles)) {
					throw new PiwiException(
						"Permission denied.", 
						PiwiException :: PERMISSION_DENIED);
				}
			} else {
				// Since user is not logged in, show login page				
				Request::setPageId($this->getLoginPageId());
			}
    	}    	
    	
    	$cachetime = $this->getCacheTime();
  
    	// Try to get contents from cache
    	$cache = new Cache($cachetime);
		$content = $cache->getPage();
		if ($content != null) {
			// Page has been found in cache
			$this->content = $content;
		} else {
			// Page has not been found in cache, so load it and save it in the cache
			$filePath = $this->contentPath . '/' . $this->getFilePath();
	
	    	if (!file_exists($filePath)) {
				throw new PiwiException(
					"Could not find the the requested page (Path: '" . $filePath . "').", 
					PiwiException :: ERR_404);
			}
		
			$this->content = new DOMDocument;
			$this->content->load($filePath);			

			// Configure the transformer
			$processor = new XSLTProcessor;
			$processor->registerPHPFunctions();
			$processor->importStyleSheet(DOMDocument::load("resources/xslt/GeneratorTransformation.xsl"));
			
			// Transform the Generators
			$this->content = $processor->transformToDoc($this->content);
			
			// Save page in cache
			$cache->cachePage($this->content);
		}

		// Set template if specified
		$template = $this->getCustomTemplate();
        if ($template != null) {
        	$this->template = $template;
        }
    }    
            
   	/** 
   	 * Returns the Serializer for the given extension.
   	 */
    public function getSerializer() {
    	$extension = Request::getExtension();
    	
    	$serializerClass = $this->getSerializerClass($extension);
    	
    	if ($serializerClass != null) {
	    	try {
	    		$class = new ReflectionClass($serializerClass);
				$serializer = $class->newInstance();
				
				if (!$serializer instanceof Serializer) {
					if (error_reporting() > E_ERROR) {
						echo("The Class with name '" . $serializerClass . "' is not an instance of Serializer.");
					}
				} else {
					return $serializer;
				}
			} catch (ReflectionException $exception) {
				if (error_reporting() > E_ERROR) {
					echo("Serializer not found: " . $serializerClass);
				}
			}
    	}    	
    	
    	if ($extension == "xml") {
    		return new PiwiXMLSerializer();
    	} else if ($extension == "pdf") {
    		return new PDFSerializer();
    	} else {
    		return new HTMLSerializer();
    	}  	
    }

    /** 
     * Returns the RoleProvider which manages the authentication of users.
     * @return RoleProvider The RoleProvider which manages the authentication of users.
     */
	public function getRoleProvider() {
		if ($this->roleProvider == null) {
		   	$class = new ReflectionClass($this->getRoleProviderClass());
			$roleProvider = $class->newInstance();
			
			if (!$roleProvider instanceof RoleProvider) {
				throw new PiwiException(
					"The Class with name '" . $this->getRoleProvider() . "' is not an instance of RoleProvider.", 
					PiwiException :: ERR_WRONG_TYPE);
			}
			$this->roleProvider = $roleProvider;
		}
		return $this->roleProvider;		
	}

	/**
	 * Returns the NavigationGenerator which should be used to generate the navigation.
	 * @return NavigationGenerator The navigation generator.
	 */
    public function getNavigationGenerator() {
		$navigationClass = $this->getCustomNavigationGeneratorClass();
		if ($navigationClass != null) {
			try {
				$class = new ReflectionClass($navigationClass);
				$navigationGenerator = $class->newInstance();
			} catch( ReflectionException $exception ) {
				if (error_reporting() > E_ERROR) {
					echo("Custom Navigation Generator not found: " . $navigationClass);
				}
				$navigationGenerator = new SimpleTextNavigation();
			}	
		} else {
			$navigationGenerator = new SimpleTextNavigation();
		}

		// Check type
    	if (!$navigationGenerator instanceof NavigationGenerator) {
    		throw new PiwiException(
				"The given Class is not an instance of NavigationGenerator.", 
				PiwiException :: ERR_WRONG_TYPE);
    	}
    	
    	return $navigationGenerator;
    }
    
	/**
	 * Returns the 'SiteMap' which is an array of NavigationElements representing the website structure.
	 * It is possible to retrieve only certain parts of the 'SiteMap'.
	 * @param string $id Returns only the NavigationElement with this id and its subitems. Set to 'null' if all NavigationElements should be returned.
	 * @param integer $depth Determinates the maximum number of subitems that will be returned. Set to '-1' for full depth, to '0' only for parent items. 
	 * @param boolean $includeParent If false only the children of the parent will be returned. This can only be used if $id is not 'null'.
	 * @return The 'SiteMap' which is an array of NavigationElements representing the website structure.
	 */
    public function getCustomSiteMap($id, $depth, $includeParent = true) {
    	$siteMap = $this->getFullSiteMap();  		

        // Build menu
        // If id is specified show only this item and its subitems
        if ($id != null) {        
	        $navigationElements = array();
	        
	        // retrieve the NavigationElement with the given id
	        $element = $this->getSiteMapItemsById($siteMap, $id);
	        
	        // if parent should be not included show only its children otherwise the parent itself
	        if ($element != null && $includeParent) {
	        	$navigationElements[0] = $element;
	        } else if ($element != null && $element->getChildren() != null) {
	        	$index = 0;
	        	foreach($element->getChildren() as $element) {
	        		$navigationElements[$index++] = $element;
	        	}
	        }
        } else {
        	$navigationElements = $siteMap;
        }
       
        // If depth is greater than -1, cut off subitems
        if ($depth >= 0) {
	    	foreach($navigationElements as $element) {
	    		$this->cutOffSiteMap(0, $depth, $element);
	        }
        }
        return $navigationElements;
    }
    
    /**
     * Returns the NavigationElement with the given in id or null if no item is found.
     * @param array $siteMap The array of NavigationElements to search in.
     * @param string $id The id of the NavigationElement.
     * @param NavigationElement $foundElement The NavigationElement with the given in id or null if no item has been found yet.
     * @return NavigationElement The NavigationElement with the given in id or null if no item is found.
     */
    private function getSiteMapItemsById($siteMap, $id, NavigationElement $foundElement = null) {
        foreach($siteMap as $element) {
        	if ($element->getId() == $id) {
        		$foundElement = $element;
        		break;
        	} else if ($element->getChildren() != null) {
        		$foundElement = $this->getSiteMapItemsById($element->getChildren(), $id, $foundElement);
        	}
        }
        
        return $foundElement;
    }
    
	/**
	 * Removes all subitems in the given NavigationElement that exceed the given depth.
	 * @param integer $currentDepth The current depth.
	 * @param integer $depth description The desired depth.
	 * @param NavigationElement $navigationElement The NavigationElement whose children should be cut off.
	 */
    private function cutOffSiteMap($currentDepth, $depth, NavigationElement $navigationElement) {
    	if ($currentDepth++ >= $depth) {    		
    		$navigationElement->setChildren(null);
    	} else {
    		if ($navigationElement->getChildren() != null) {
	    		foreach($navigationElement->getChildren() as $element) {
		    		$this->cutOffSiteMap($currentDepth, $depth, $element);
		        }
    		}
    	}
    }
    
	/**
     * Returns the template of the requested page.
     * @return string The template of the requested page.
     */
    public function getTemplate() {
    	return $this->template;
    }

	/**
	 * Returns the content of the requested page as DOMDocument.
	 * @return DOMDocument The content of the requested page as DOMDocument.
	 */
    public function getContent() {
    	if ($this->content == null) {
    		throw new PiwiException(
				"The page has not been loaded yet. Invoke method 'generateContent' or 'setContent' on '" . __CLASS__ . "' first.", 
				PiwiException :: ERR_ILLEGAL_STATE);
    	}
    	
    	return $this->content;
    }  

    /**
     * Sets the content of the page.
     * @param string $content The content as xml.
     */
    public function setContent($content) {
    	$this->content = new DOMDocument;
    	$this->content->loadXML($content);
    }    

	/**
	 * Returns the singleton instance of the Site.
	 * @return Site The singleton instance of the Site.
	 */
	public static function getInstance() {
		return Site::$siteInstance;
	}
	
	/**
	 * Sets the singleton instance of the Site.
	 * @param Site $site The singleton instance of the Site.
	 */
	public static function setInstance(Site $site) {
		Site::$siteInstance = $site;
	}
	
	/**
	 * ---------------------------------------------------------------------
	 * >>>>>>>>>>>>>>>>>>>>>>>>>> Abstract Methods <<<<<<<<<<<<<<<<<<<<<<<<<
	 * ---------------------------------------------------------------------
	 */ 
 
    /**
     * Returns the path of the xml file containing the content of the requested page.
     * @return string The path of the xml file containing the content of the requested page.
     */
    protected abstract function getFilePath();
    
    /**
     * Returns the template of the requested page or null if not specified.
     * @return string The template of the requested page.
     */
    protected abstract function getCustomTemplate();
    
    /**
     * Returns the 'SiteMap' which is an array of NavigationElements representing the whole website structure.
     * @return array Array of NavigationElements representing the whole website structure.
     */
    protected abstract function getFullSiteMap();
    
    /**
     * Returns the classname of the custom NavigationGenerator Class or null if not specified.
     * @return string The classname of the custom NavigationGenerator Class or null if not specified.
     */
    protected abstract function getCustomNavigationGeneratorClass();
    
    /**
     * Returns the cachetime (the time that may pass until the content of the page is regenerated).
     * @return integer The cachetime.
     */
    protected abstract function getCacheTime();
    
    /**
     * Returns the possible roles a user needs to access the currently requested page.
     * The possible roles are returned as array.
     * The array contains only '?' if no authentication is required.
     * The array contains only '*' if any authenticated user allowed to access the page.
     * @return array The possible roles a user needs to access the currently requested page.
     */
    protected abstract function getAllowedRoles();
    
    /** 
     * Returns the classname of the RoleProvider which manages the authentication of users or null if none is specified.
     * @return string The classname of the RoleProvider which manages the authentication of users or null if none is specified.
     */
    protected abstract function getRoleProviderClass();

    /** 
     * Returns the classname of the Serializer for the given format or null if none is found.
     * @return string The classname of the Serializer for the given format or null if none is found.
     */
    protected abstract function getSerializerClass($extension);
    
    /**
     * Returns the id of the login page.
     * @return string The id of the login page.
     */
    protected abstract function getLoginPageId();
    
    /**
     * Returns the path of the a custom XSLT stylesheet or null if none is specified.
     * @return string The path of the a custom XSLT stylesheet or null if none is specified.
     */
    public abstract function getCustomXSLTStylesheetPath();
    
    /**
     * Returns a list of supported languages.
     * @return array List of supported languages.
     */
    public abstract function getSupportedLanguages();
}
?>