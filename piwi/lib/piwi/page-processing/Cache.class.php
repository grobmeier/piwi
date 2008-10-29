<?php
/**
 * Caches pages and loads them from cache again.
 */
class Cache {
	/** The cachetime (the time that may pass until the content of the page is regenerated). */
	private $cachetime = null;
	
	/** The name of the cached file, containing the requested page. */
	private $filePath = null;
	
    /**
     * Constructor.
     * @param integer $cachetime The cachetime (the time that may pass until the content of the page is regenerated).
     */
    public function __construct($cachetime) {
    	$this->cachetime = $cachetime;
    }
    
    public function getPage() {   
    	// Check if cache is enabled and if post data is send
    	if ($this->cachetime == 0 || sizeof($_POST) > 0) {
    		return null;
    	}
    	
    	// Generate unique pagename
    	$filePath = "";
    	
		foreach(Request::getParameters() as $key => $value) {				
			$filePath .=  '_' . $key . '_' . $value;
		}
		
		// Hash the parameters, to avoid long filenames
		$filePath = 'cache/' . Request::getPageId() . '_' . SessionManager::getUserLanguage() . '_' . Request::getExtension() . ($filePath != '' ? sha1($filePath) : '') . '.xml';

		// Set filePath in instance so a file can later be created with this filename		
		$this->filePath = $filePath;
		
		// Check if file exists or if has expired
		if (!file_exists($filePath) || time() > (filectime($filePath) + $this->cachetime)) {
			return null;
		} else {
			$cachedcontent = new DOMDocument;
			$cachedcontent->load($filePath);
			
			return $cachedcontent;
		}
    }
    
    /**
     * Caches the given page so that it can be reused within further requests.
     * @param datatype $page The page to cache.
     */
    public function cachePage(DOMDocument $page) {
    	// Check if cache is enabled and if post data is send
    	if ($this->cachetime == 0 || sizeof($_POST) > 0) {
    		return;
    	}
    	
    	if ($this->filePath == null) {
			throw new PiwiException(
				"Illegal State: Invoke method 'getPage' on '" . __CLASS__ . "' before.", 
				PiwiException :: ERR_ILLEGAL_STATE);
    	}
    	
    	// Save only if content has not been loaded from cached file
    	$page->save($this->filePath);
    }
}
?>