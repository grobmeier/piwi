<?/** * Renders the requested page and creates the navigation based on 'site.xml'. */class XMLSite extends Site {	/** The name of the file containing the site information. */	private $siteFilename;		/** The site definition as DOMDocument. */    private $siteXml;                /** The DOMXPath of the siteXml. */    private $domXPath = null;            /**     * Constructor.     * @param string $pageId The id of the requested page.     * @param string $contentPath Name of the folder where the content is placed.     * @param string $siteFilename The name of the file containing the site information.     */    public function __construct($pageId, $contentPath, $siteFilename = 'site.xml') {
    	$this->contentPath = $contentPath;    	$this->siteFilename = $siteFilename;    	$this->pageId = $pageId;    }        /**     * Returns the path of the xml file containing the content of the requested page.     * @return string The path of the xml file containing the content of the requested page.     */    protected function getFilePath() {    	if ($this->domXPath == null) {    		$this->loadSite();    	}    	// Lookup pageId        $result = $this->domXPath->query("//page[@id='" . $this->pageId . "']");        if($result->length > 0) {        	return $result->item(0)->getAttribute("href");        } else {            throw new PiwiException(				"Could not find the requested page (Page: '" . $this->pageId . "').", 				PiwiException :: ERR_404);        }    }        /**     * Returns the template of the requested page or null if not specified.     * @return string The template of the requested page.     */    protected function getCustomTemplate() {    	if ($this->domXPath == null) {    		$this->loadSite();    	}    	// Lookup pageId        $result = $this->domXPath->query("//page[@id='".$this->pageId."']");        if($result->length > 0) {			$template = $result->item(0)->getAttribute("template");    		if ($template == "") {    			return null;    		}			return $template;        } else {            throw new PiwiException(				"Could not find the requested page (Page: '" . $this->pageId . "').", 				PiwiException :: ERR_404);        }    }        /**     * Returns the classname of the custom NavigationGenerator Class or null if not specified.     * @return string The classname of the custom NavigationGenerator Class or null if not specified.     */    protected function getCustomNavigationGenerator() {    	if ($this->domXPath == null) {    		$this->loadSite();    	}    	    	$result = $this->domXPath->query("/site");    	if($result->length > 0) {    		$navigation = $result->item(0)->getAttribute("navigation");    		if ($navigation == "") {    			return null;    		}			return $navigation;        } else {            return null;        }    	    }    /**     * Returns the 'SiteMap' which is an array of NavigationElements representing the whole website structure.     * @return array Array of NavigationElements representing the whole website structure.     */    protected function getFullSiteMap() {    	if ($this->siteXml == null) {	    	try {	    		$this->loadSite();	    	} catch( PiwiException $exception ) {				// If this exception occurs the 'site.xml' can not be found.				return "Sitemap could not be build. Could not find the site definition file (Path: '" . $this->siteXmlPath . "').";			}    		    	}		// Determinate all nodes that lead to the current page    	$openpath = array();    	        $domNodeList = $this->domXPath->query("//page[@id='".$this->pageId."']");        $index = 0;        foreach ($domNodeList as $element) {            while($element->nodeName != "site") {                    $openpath[$index++] = $element->getAttribute("id");                    $element = $element->parentNode;            }        }                // Build array containing all sites and subsites        $xpath = "/site/page";        $nodelist = $this->domXPath->query($xpath);        $result = $this->generateSubnavigation(array(), $nodelist, $xpath, $openpath);        return $result;    }        /**     * Generates the submenus of the given nodes.     * @param array $result Array of NavigationElements.     * @param DOMNodeList $nodelist List of nodes in the current layer.     * @param string $xpath The xpathquerystring of the current layer.     * @param array $openpath The ids nodes that lead to the current pageId.     * @return array description     */    private function generateSubnavigation($result, DOMNodeList $nodelist, $xpath, $openpath) {        $index = 0;        foreach ($nodelist as $element) {        	$id = $element->getAttribute("id");        	        	$navigationElement = new NavigationElement($id, $element->getAttribute("label"));           	$navigationElement->setSelected($id == $this->pageId);           	$navigationElement->setOpen(in_array($id, $openpath));						$result[$index] = $navigationElement;	        if($element->hasChildNodes()) {            	$children = $this->domXPath->query($xpath."[@id='".$id."']/*");               	$result[$index]->setChildren($this->generateSubnavigation(array(), $children, $xpath . '/page', $openpath));            }    		$index++;      	}      	return $result;    }        /**     * Loads the 'site.xml'.     */    private function loadSite() {    	$path = $this->contentPath . '/' . $this->siteFilename;       	if (!file_exists($path)) {			throw new PiwiException(				"Could not find the site definition file (Path: '" . $path . "').", 				PiwiException :: ERR_NO_XML_DEFINITION);    	}    	    	// Load 'site.xml'    	$this->siteXml = new DOMDocument;		$this->siteXml->load($path);				$this->domXPath = new DOMXPath($this->siteXml);    }}?>