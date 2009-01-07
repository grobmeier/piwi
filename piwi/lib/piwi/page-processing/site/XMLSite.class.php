<?php
/**
 * Renders the requested page and creates the navigation based on 'site.xml'.
 */
class XMLSite extends Site {
	/** The name of the file containing the site information. */
	private $siteFilename = null;
	        
    /** The DOMXPath of the 'site.xml'. */
    private $domXPath = null;
        
    /**
     * Constructor.
     * @param string $contentPath Name of the folder where the content is placed.
     * @param string $templatesPath Name of the folder where your templates are placed.
     * @param string $siteFilename The name of the file containing the site information.
     */
    public function __construct($contentPath, $templatesPath, $siteFilename = 'site.xml') {
    	parent :: __construct($contentPath, $templatesPath);
    	$this->siteFilename = $siteFilename;
    }
    
    /**
	 * -------------------------------------------------------------------------
	 * >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>Page attributes<<<<<<<<<<<<<<<<<<<<<<<<<<<
	 * -------------------------------------------------------------------------
	 */
    /**
     * Returns the path of the xml file containing the content of the requested page.
     * @return string The path of the xml file containing the content of the requested page.
     */
    protected function getFilePath() {
    	if ($this->domXPath == null) {
    		$this->_loadSite();
    	}

		return $this->_getCurrentPageDOMNode()->getAttribute("href");
    }
    
    /**
     * Returns the template of the requested page or null if not specified.
     * @return string The template of the requested page.
     */
    protected function getHTMLTemplatePath() {
    	if ($this->domXPath == null) {
    		$this->_loadSite();
    	}

		$template = $this->_getCurrentPageDOMNode()->getAttribute("template");
 
 		return $template == "" ? null : $template;
    }

    /**
     * Returns the possible roles a user needs to access the currently requested page.
     * The possible roles are returned as array.
     * The array contains only '?' if no authentication is required.
     * The array contains only '*' if any authenticated user allowed to access the page.
     * @return array The possible roles a user needs to access the currently requested page.
     */
    protected function getAllowedRoles(){
    	if ($this->domXPath == null) {
    		$this->_loadSite();
    	}

		$result = $this->_getCurrentPageDOMNode()->getAttribute("roles");
		if ($result == "") {
    		return array('?');
    	} else {
    		return explode(',', str_replace(' ', '', $result));
    	}
    }
       
    /**
     * Returns a list of supported languages.
     * @return array List of supported languages.
     */
    public function getSupportedLanguages() {
    	if ($this->domXPath == null) {
    		$this->_loadSite();
    	}
    	
    	$xpath = "/site:site/site:language/@region";
    	$languages = $this->domXPath->query($xpath);
    	
    	$result = array();
    	$count = 0;
    	foreach ($languages as $value) {
       		$result[$count++] = $value->value;
		}
		
		return $result;
    }   
    
	/**
	 * Returns the page specific cachetime (the time that may pass until the content 
	 * of the page is regenerated) or null if none is specified.
     * @return integer The page specific cachetime or null if none is specified.
	 */
	protected function getCacheTime() {
    	if ($this->domXPath == null) {
    		$this->_loadSite();
    	}

		$result = $this->_getCurrentPageDOMNode()->getAttribute("cachetime");
		if ($result == "") {
    		return null;
    	} else {
    		return $result;
    	}
	}
      
    /**
	 * -------------------------------------------------------------------------
	 * >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>SiteMap<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	 * -------------------------------------------------------------------------
	 */  
    /**
     * Returns the 'SiteMap' which is an array of SiteElements representing the whole website structure.
     * @return array Array of SiteElements representing the whole website structure.
     */
    public function getFullSiteMap() {
    	if ($this->domXPath == null) {
	    	$this->_loadSite();  		
    	}

		// Determinate all nodes that lead to the current page
    	$openpath = array();
    	
        $domNodeList = $this->domXPath->query("/site:site/site:language[@region='" . 
        	UserSessionManager::getUserLanguage() . "']//site:page[@id='" . Request::getPageId() . "']");
		
        foreach ($domNodeList as $element) {
            while ($element->nodeName != "language") {
                    $openpath[] = $element->getAttribute("id");
                    $element = $element->parentNode;
            }
        }
        
        // Build array containing all sites and subsites
        $xpath = "/site:site/site:language[@region='" . UserSessionManager::getUserLanguage() . 
			"']/site:page";
        $nodelist = $this->domXPath->query($xpath);
        $result = $this->_generateSubnavigation(array(), $nodelist, $xpath, $openpath);

        return $result;
    }
    
    /**
     * Generates the submenus of the given nodes.
     * @param array $result Array of SiteElements.
     * @param DOMNodeList $nodelist List of nodes in the current layer.
     * @param string $xpath The xpathquerystring of the current layer.
     * @param array $openpath The ids nodes that lead to the current pageId.
     * @param SiteElement $parentSiteElement The parent SiteElement
     * @return array Array containing the submenus of the given nodes.
     */
    private function _generateSubnavigation($result, DOMNodeList $nodelist, $xpath, array $openpath, 
    	SiteElement $parentSiteElement = null) {
        foreach ($nodelist as $element) {
        	$id = $element->getAttribute("id");
        	
        	$siteElement = new SiteElement($id, $element->getAttribute('label'), 
        		$element->getAttribute('href'));
           	$siteElement->setSelected($id == Request::getPageId());
           	$siteElement->setOpen(in_array($id, $openpath));
           	if ($element->getAttribute("hideInNavigation")) {
           		$siteElement->setHiddenInNavigation(true);
           	}
           	if ($element->getAttribute("hideInSiteMap")) {
           		$siteElement->setHiddenInSiteMap(true);
           	}
           	if ($parentSiteElement != null) {
           		$siteElement->setParent($parentSiteElement);
           	}
           	
	        if ($element->hasChildNodes()) {
            	$children = $this->domXPath->query($xpath . "[@id='" . $id . "']/*");
               	$siteElement->setChildren($this->_generateSubnavigation(array(), $children, 
               		$xpath . '/site:page', $openpath, $siteElement));
            }
            			
			$result[] = $siteElement;
            
      	}
      	return $result;
    } 
	
    /**
	 * -------------------------------------------------------------------------
	 * >>>>>>>>>>>>>>>>>>>>>>>>>>>>Private Helper Methods<<<<<<<<<<<<<<<<<<<<<<<
	 * -------------------------------------------------------------------------
	 */   
	/**
	 * Returns the DOMNode representing the current page.
	 * @return DOMNode The DOMNode representing the current page.
	 */	 
    private function _getCurrentPageDOMNode() {
    	if ($this->domXPath == null) {
    		$this->_loadSite();
    	}
    	
    	// Lookup pageId
        $result = $this->domXPath->query("/site:site/site:language[@region='" . 
        	UserSessionManager::getUserLanguage() . "']//site:page[@id='" . Request::getPageId() . "']");

        if ($result->length == 1) {
        	return $result->item(0);
        } else if ($result->length > 1) {
       		throw new PiwiException("The id of the requested page is not unique (Page: '" . 
       				Request::getPageId() . "').", 
				PiwiException :: ERR_404);
        } else {
            throw new PiwiException("Could not find the requested page (Page: '" . 
            		Request::getPageId() . "').", 
				PiwiException :: ERR_404);
        }  	
    }
    
    /**
     * Loads the 'site.xml'.
     */
    private function _loadSite() {
    	$path = $this->contentPath . '/' . $this->siteFilename;
       	if (!file_exists($path)) {
			throw new PiwiException("Could not find the site definition file (Path: '" . $path . "').", 
				PiwiException :: ERR_NO_XML_DEFINITION);
    	}
    	
    	// Load 'site.xml'
    	$siteXml = DOMDocument::load($path);
		
		// validate xml only if error level is high enough to display warnings
		if (error_reporting() > E_ERROR) {
			$siteXml->schemaValidate($GLOBALS['PIWI_ROOT'] . "resources/xsd/site.xsd");
		}
		
		// Init DOMXPath which will be used for querying the site
		$this->domXPath = new DOMXPath($siteXml);
		$this->domXPath->registerNamespace('site', 'http://piwi.googlecode.com/xsd/site');
    }
}
?>