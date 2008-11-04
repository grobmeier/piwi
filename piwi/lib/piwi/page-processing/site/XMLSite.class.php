<?/** * Renders the requested page and creates the navigation based on 'site.xml'. */class XMLSite extends Site {	/** The name of the file containing the site information. */	private $siteFilename = null;	            /** The DOMXPath of the siteXml. */    private $domXPath = null;            /**     * Constructor.     * @param string $contentPath Name of the folder where the content is placed.     * @param string $siteFilename The name of the file containing the site information.     */    public function __construct($contentPath, $siteFilename = 'site.xml') {
    	$this->contentPath = $contentPath;    	$this->siteFilename = $siteFilename;    }        /**     * Returns the path of the xml file containing the content of the requested page.     * @return string The path of the xml file containing the content of the requested page.     */    protected function getFilePath() {    	if ($this->domXPath == null) {    		$this->loadSite();    	}    	// Lookup pageId        $result = $this->domXPath->query("/site:site/site:language[@region='" . SessionManager::getUserLanguage() . "']//site:page[@id='" . Request::getPageId() . "']");        if($result->length == 1) {        	return $result->item(0)->getAttribute("href");        } else if ($result->length > 1) {       		throw new PiwiException(				"The id of the requested page is not unique (Page: '" . Request::getPageId() . "').", 				PiwiException :: ERR_404);        } else {            throw new PiwiException(				"Could not find the requested page (Page: '" . Request::getPageId() . "').", 				PiwiException :: ERR_404);        }    }        /**     * Returns the template of the requested page or null if not specified.     * @return string The template of the requested page.     */    protected function getCustomTemplate() {    	if ($this->domXPath == null) {    		$this->loadSite();    	}    	// Lookup pageId       $result = $this->domXPath->query("/site:site/site:language[@region='" . SessionManager::getUserLanguage() . "']//site:page[@id='" . Request::getPageId() . "']");		        if ($result->length == 1) {			$template = $result->item(0)->getAttribute("template");    		if ($template == "") {    			return null;    		}			return $template;        } else if ($result->length > 1) {       		throw new PiwiException(				"The id of the requested page is not unique (Page: '" . Request::getPageId() . "').", 				PiwiException :: ERR_404);        } else {            throw new PiwiException(				"Could not find the requested page (Page: '" . Request::getPageId() . "').", 				PiwiException :: ERR_404);        }    }        /**     * Returns the classname of the custom NavigationGenerator Class or null if not specified.     * @return string The classname of the custom NavigationGenerator Class or null if not specified.     */    protected function getCustomNavigationGenerator() {    	if ($this->domXPath == null) {    		$this->loadSite();    	}    	    	$result = $this->domXPath->query("/site:site/site:configuration/site:navigation");    	if($result->length == 1) {    		$navigation = $result->item(0)->nodeValue;    		if ($navigation == "") {    			return null;    		}			return $navigation;        } else if ($result->length > 1) {       		throw new PiwiException(				"Your 'site.xml' is not valid (Path: '" . $this->siteFilename . "').", 				PiwiException :: INVALID_XML_DEFINITION);        } else {            return null;        }    	    }    /**     * Returns the cachetime (the time that may pass until the content of the page is regenerated).     * @return integer The cachetime.     */    protected function getCacheTime() {    	if ($this->domXPath == null) {    		$this->loadSite();    	}    	    	$result = $this->domXPath->query("/site:site/site:configuration/site:cachetime");    	if($result->length == 1) {    		$cachetime = $result->item(0)->nodeValue;    		if ($cachetime == "") {    			return 0;    		}			return $cachetime;        } else if ($result->length > 1) {       		throw new PiwiException(				"Your 'site.xml' is not valid (Path: '" . $this->siteFilename . "').", 				PiwiException :: INVALID_XML_DEFINITION);        } else {            return 0;        }    	    }        /**     * Returns the path of the a custom XSLT stylesheet or null if none is specified.     * @return string The path of the a custom XSLT stylesheet or null if none is specified.     */    protected function getCustomXSLTStylesheetPath() {    	if ($this->domXPath == null) {    		$this->loadSite();    	}    	    	$result = $this->domXPath->query("/site:site/site:configuration/site:customXSLTStylesheet");    	if ($result->length == 1) {    		return $result->item(0)->nodeValue;        } else {       		return null;        }    	    }        /**     * Returns the 'SiteMap' which is an array of NavigationElements representing the whole website structure.     * @return array Array of NavigationElements representing the whole website structure.     */    protected function getFullSiteMap() {    	if ($this->domXPath == null) {	    	$this->loadSite();  		    	}		// Determinate all nodes that lead to the current page    	$openpath = array();    	        $domNodeList = $this->domXPath->query("/site:site/site:language[@region='" . SessionManager::getUserLanguage() . "']//site:page[@id='". Request::getPageId() ."']");		        $index = 0;        foreach ($domNodeList as $element) {            while($element->nodeName != "language") {                    $openpath[$index++] = $element->getAttribute("id");                    $element = $element->parentNode;            }        }                // Build array containing all sites and subsites        $xpath = "/site:site/site:language[@region='" . SessionManager::getUserLanguage() . "']/site:page";        $nodelist = $this->domXPath->query($xpath);        $result = $this->generateSubnavigation(array(), $nodelist, $xpath, $openpath);        return $result;    }    /**     * Returns a list of supported languages.     * @return array List of supported languages.     */    public function getSupportedLanguages() {    	if ($this->domXPath == null) {    		$this->loadSite();    	}    	    	$xpath = "/site:site/site:language/@region";    	$languages = $this->domXPath->query($xpath);    	    	$result = array();    	$count = 0;    	foreach ($languages as $value) {       		$result[$count++] = $value->value;
		}				return $result;    }        /**     * Generates the submenus of the given nodes.     * @param array $result Array of NavigationElements.     * @param DOMNodeList $nodelist List of nodes in the current layer.     * @param string $xpath The xpathquerystring of the current layer.     * @param array $openpath The ids nodes that lead to the current pageId.     * @return array Array containing the submenus of the given nodes.     */    private function generateSubnavigation($result, DOMNodeList $nodelist, $xpath, array $openpath, NavigationElement $parentNavigationElement = null) {        $index = 0;        foreach ($nodelist as $element) {        	$id = $element->getAttribute("id");        	        	$navigationElement = new NavigationElement($id, $element->getAttribute("label"));           	$navigationElement->setSelected($id == Request::getPageId());           	$navigationElement->setOpen(in_array($id, $openpath));           	           	if ($parentNavigationElement != null) {           		$navigationElement->setParent($parentNavigationElement);           	}						$result[$index] = $navigationElement;	        if($element->hasChildNodes()) {            	$children = $this->domXPath->query($xpath."[@id='".$id."']/*");               	$result[$index]->setChildren($this->generateSubnavigation(array(), $children, $xpath . '/site:page', $openpath, $navigationElement));            }    		$index++;      	}      	return $result;    }        /**     * Loads the 'site.xml'.     */    private function loadSite() {    	$path = $this->contentPath . '/' . $this->siteFilename;       	if (!file_exists($path)) {			throw new PiwiException(				"Could not find the site definition file (Path: '" . $path . "').", 				PiwiException :: ERR_NO_XML_DEFINITION);    	}    	    	// Load 'site.xml'    	$siteXml = DOMDocument::load($path);		// validate xml only if error level is high enough to display warnings		if (error_reporting() > E_ERROR) {			$siteXml->schemaValidate("resources/xsd/site.xsd");		}				// Init DOMXPath which will be used for querying the site		$this->domXPath = new DOMXPath($siteXml);		$this->domXPath->registerNamespace('site', 'http://piwi.googlecode.com/xsd/site');    }}?>