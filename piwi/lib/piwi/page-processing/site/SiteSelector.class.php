<?php
// TODO: should differ on the input source between several Page implementations
/**
 * TODO: Write comment
 */
class SiteSelector {
	/** Reference to the processed page. */
	private $page = null;
	
	private $errormode = false;
	/**
	 * Constructor.
	 */
	public function __construct() {
	}
	
	/**
	 * Processes the contents of the page.
	 */
	public function generateContent() {
		$this->_choosePipeline();
		$this->page->generateContent();
		
	}

	private function _choosePipeline() {
		$path = $this->getSite()->getFilePath();
		$pos = strrpos($path, ".");
		$extension = substr($path, $pos + 1);
		
		// TODO: Pipelines should be configurable
		if ($extension == 'stream') {
			// Streaming input definition file
			$this->setPage(BeanFactory :: getBeanById('streamingPage'));
		} else {
			// Standard pipeline
			$this->setPage(BeanFactory :: getBeanById('xmlPage'));
		}
	}
	
	/**
	 * Sets the content of the page.
	 * @param string $content The content as xml.
	 */
	public function setContent($content) {
		$this->page->setContent($content);
	}
	
	/**
	 * Excecutes the Serializer.
	 */
	public function serialize() {
		$this->page->serialize();
	}
	
	/**
	 * Returns the reference to the processed page.
	 * @return Page The reference to the processed page.
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * Sets the reference to the processed page.
	 * @param Page $page The reference to the processed page.
	 */
	public function setPage(Page $page) {
		$this->page = $page;
	}
	
	/**
	 * Returns the reference to the Site.
	 * @return Site The reference to the Site.
	 */
	public function getSite() {
		return $this->site;
	}

	/**
	 * Sets the reference to the Site.
	 * @param Site $site The reference to the Site.
	 */
	public function setSite(Site $site) {
		$this->site = $site;
	}
}
?>