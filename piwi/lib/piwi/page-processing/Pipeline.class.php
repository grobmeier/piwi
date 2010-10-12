<?php
/**
 * The Pipeline class selects a matching inputprovider implementation based on the
 * file ending of the requested content. The actual file which is to be read is defined 
 * in the site.xml. 
 * 
 * In other words, the pipeline:
 * 
 * - chooses the input provider
 * - takes care the input provider transforms the content to piwi xml
 * - chooses the output provider
 * - takes care the output provider transforms the content to the choosen output format
 * 
 * New inputprovider implementations can be added in the context.xml with adding the
 * extension and the preferred Page implementation for it.
 */
class Pipeline {
	/** Reference to the processed page. */
	private $page = null;
	
	/** Map of extension / Page implementations */
	private $pagemap = null;
	
	/** Configuration */
	private $configuration = null;
	
	/** Constructor */
	public function __construct() {
	}
	
	/**
	 * Standard generation of content.
	 * Choose input, generate content, choose output.
	 */
	public function generate() {
		$this->input();
		$this->page->generateContent();		
		$this->output();
	}

	/**
	 * Selects a Page implementation based on the file extension.
	 * 
	 * @param string $extension the extension to use for selecting the input file, selected from the page map 
	 * @param string $pageId the page id from the site
	 * @return void
	 */
	public function input($extension = null, $pageId = null) {
		if ($pageId != null) {
			Request::setPageId($pageId);
		}
		
		if ($extension == null) {
			$path = $this->getSite()->getFilePath();
			$pos = strrpos($path, ".");
			$extension = substr($path, $pos + 1);
		}

		if (isset($this->pagemap[$extension])) {
			if (is_object($this->pagemap[$extension])) {
				$this->setPage($this->pagemap[$extension]);
			} else {
				$this->setPage(BeanFactory :: getBeanById($this->pagemap[$extension]));
			}
		} else {
			if (isset($this->pagemap['default']) && is_object($this->pagemap['default'])) {
				$this->setPage($this->pagemap['default']);
			} else {
				$this->setPage(BeanFactory :: getBeanById($this->pagemap['default']));
			}
		}
	}
	
	/**
	 * Outputs the content, based on the serializer choosen by the url extension
	 */
	public function output() {
		$extension = Request :: getExtension();

		$serializer = $this->configuration->getSerializer($extension);
		if ($serializer == null) {
			$serializer = new HTMLSerializer();
		}
		$serializer->serialize($this->page->getContent());
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
	 * Sets the Pagemap as defined in the context.xml
	 * 
	 * @param array $pagemap
	 * @return unknown_type
	 */
	public function setPagemap($pagemap) {
		$this->pagemap = $pagemap;
	}
	
	/**
	 * Sets the configuration
	 * Injected by dependency injection.
	 * @param Configuration $configuration The reference to the configuration.
	 */	
	public function setConfiguration(Configuration $configuration) {
		$this->configuration = $configuration;
	}
}
?>