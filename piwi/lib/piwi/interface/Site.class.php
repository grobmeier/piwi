<?php
/**
 * Renders the requested page and creates the navigation.
 */
abstract class Site {
	/**Singleton instance of the Site. */
	private static $siteInstance = null;
	
	/** The id of the requested page. */
	protected $pageId = null;
	
	/** Name of the folder where the content is placed. */
	protected $contentPath = null;
	
	/** The serializer used for transformation. */
	private $serializer = null;

	/** The template of the requested page. */
	private $template = "default.php";
	
	/** The content of the requested page as DOMDocument. */
	private $content = null;
	
	/** The 'SiteMap' which is an array of NavigationElements representing the whole website structure. */
	private $siteMap = null;
	
    /**
     * Reads the xml of the requested page.
     */
    public function readContent() {
    	if ($this->pageId == null) {
			throw new PiwiException(
				"The requested page has not been specified.", 
				PiwiException :: ERR_ILLEGAL_STATE);
    	}
    	
    	$filePath = $this->contentPath . '/' . $this->getFilePath();

    	if (!file_exists($filePath)) {
			throw new PiwiException(
				"Could not find the the requested page (Path: '" . $filePath . "').", 
				PiwiException :: ERR_404);
		}
	
		$this->content = new DOMDocument;
		$this->content->load($filePath);
		
		// Set template if specified
		$template = $this->getCustomTemplate();
        if ($template != null) {
        	$this->template = $template;
        }
    }    
            
   	/** 
   	 * Transforms the page using the specified serializer.
   	 * @return string The serialized content.
   	 */
    public function transform() {
    	if ($this->content == null) {
    		throw new PiwiException(
				"The page has not been loaded yet. Invoke method 'readContent' or 'setContent' on '" . __CLASS__ . "' first.", 
				PiwiException :: ERR_ILLEGAL_STATE);
    	}
    	if ($this->serializer == null) {
    		$this->serializer = new HTMLSerializer();    	
    	}
    	return $this->serializer->serialize($this->content);
    }

	/**
	 * Returns the NavigationGenerator which should be used to generate the navigation.
	 * @return NavigationGenerator The navigation generator.
	 */
    public function getNavigationGenerator() {
		$navigationClass = $this->getCustomNavigationGenerator();
		if ($navigationClass != null) {
			$class = new ReflectionClass($navigationClass);
			$navigationGenerator = $class->newInstance();
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
    	if ($this->siteMap == null) {
	    	$this->siteMap = $this->getFullSiteMap();  		
    	}
        
        // Build menu
        // If id is specified show only this item and its subitems
        if ($id != null) {        
	        $navigationElements = array();
	        
	        // retrieve the NavigationElement with the given id
	        $element = $this->getSiteMapItemsById($this->siteMap, $id);
	        
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
        	$navigationElements = $this->siteMap;
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
     * @return NavigationElement The NavigationElement with the given in id or null if no item is found.
     */
    private function getSiteMapItemsById($siteMap, $id) {
    	$foundElement = null;
        foreach($siteMap as $element) {
        	if ($element->getId() == $id) {
        		$foundElement = $element;
        		break;
        	} else if ($element->getChildren() != null) {
        		$foundElement = $this->getSiteMapItemsById($element->getChildren(), $id);
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
    protected abstract function getCustomNavigationGenerator();
}
?>