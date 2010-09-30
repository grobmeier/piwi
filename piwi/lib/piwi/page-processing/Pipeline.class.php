<?php
/**
 * The SiteSelector clas selects a matching inputprovider implementation based on the
 * file ending of the requested content. The actual file which is to be read is defined 
 * in the site.xml. 
 * 
 * In other words, the SiteSelector connects the Site implementation to the Page implementation.
 * 
 * New inputprovider implementations can be added in the context.xml with adding the 
 * extension and the preferred Page implementation for it. 
 */
class Pipeline {
	/** Reference to the processed page. */
	private $page = null;
	
	private $errormode = false;
	
	private $pagemap = null;
	
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

	/**
	 * Selects a Page implementation based on the file extension.
	 * @return void
	 */
	private function _choosePipeline() {
		$path = $this->getSite()->getFilePath();
		$pos = strrpos($path, ".");
		$extension = substr($path, $pos + 1);

		if(isset($this->pagemap[$extension])) {
			if(is_object($this->pagemap[$extension])) {
				$this->setPage($this->pagemap[$extension]);
			} else {
				$this->setPage(BeanFactory :: getBeanById($this->pagemap[$extension]));
			}
		} else {
			if(isset($this->pagemap['default']) && is_object($this->pagemap['default'])) {
				$this->setPage($this->pagemap['default']);
			} else {
				$this->setPage(BeanFactory :: getBeanById($this->pagemap['default']));
			}
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
	
	/**
	 * 
	 * @param array $pagemap
	 * @return unknown_type
	 */
	public function setPagemap($pagemap) {
		$this->pagemap = $pagemap;
	}
}
?>