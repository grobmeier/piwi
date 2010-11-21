<?php
/**
 * Renders the requested page and creates the navigation based on 'site.xml'.
 */
class DocbookPage extends Page {
	/**
     * Constructor.
     * @param string $contentPath Name of the folder where the content is placed.
     * @param string $templatesPath Name of the folder where your templates are placed.
     * @param string $siteFilename The name of the file containing the site information.
     */
    public function __construct() {
    }
    
    /**
	 * Reads the xml of the requested page and transforms the Generators to Piwi-XML.
	 * TODO refactor: some stuff should go to the parent generate content method
	 * TODO refactor: this class must be easily able extend
	 */
	public function generateContent() {
		$content = null;
		$cache = null;
		
		$this->checkPermissions();
		$cache = $this->getCache();
		$content = $cache->getPage();
	
		if ($content != null) {
			// Page has been found in cache
			$this->content = $content;
		} else {
			// Page has not been found in cache, so load it and save it in the cache
			$filePath = $this->contentPath . '/' . $this->site->getFilePath();

			if (!file_exists($filePath)) {
				throw new PiwiException("Could not find the the requested page (Path: '" . $filePath . "').", 
					PiwiException :: ERR_404);
			}
			
			$document = new ezcDocumentDocbook();
			$document->loadFile($filePath);
	
			$html = new ezcDocumentXhtml();
			$html->createFromDocbook( $document );
			$this->content = $html->getDomDocument();

			// Configure the transformer
			$processor = new XSLTProcessor();
			$processor->registerPHPFunctions();
			$processor->importStyleSheet(DOMDocument :: load($GLOBALS['PIWI_ROOT'] . 
				"resources/xslt/WikiToPiwiTransformation.xsl"));

			// Transform the Generators
			$this->content = $processor->transformToDoc($this->content);
			
			// Save page in cache
			if ($cache != null) {
				$cache->cachePage($this->content);
			}			
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
}
?>