<?php
/**
 * Renders the requested page and creates the navigation.
 */
abstract class Site {
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
		$template = $this->getTemplate();
        if ($template != "") {
        	$this->template = $template;
        }
    }
    
    /**
     * Generates the Navigation.
     * @return string The navigation as HTML.
     */
    public function generateNavigation() {
    	if ($this->siteMap == null) {
	    	$this->siteMap = $this->getSiteMap();  		
    	}
        
        // Initialize the Class building the menu
        $navigation = new SimpleTextNavigation();
        
        return $navigation->generate($this->siteMap);
    }
    
   	/** 
   	 * Transforms the page using the specified serializer 
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
     * Returns the template of the requested page.
     * @return string The template of the requested page.
     */
    protected abstract function getOtherTemplate();
    
    /**
     * Returns the 'SiteMap' which is an array of NavigationElements representing the whole website structure.
     * @return array Array of NavigationElements representing the whole website structure.
     */
    protected abstract function getSiteMap();
}
?>