<?php
// TODO: should differ on the input source between several Page implementations
class SiteSelector {
	/** Singleton instance of the SiteSelector. */
	private $page = null;
	
	/**
	 * Constructor.
	 */
	public function __construct() {
	}
	
	public function generateContent() {
		$this->page->generateContent();
	}

	public function setContent($content) {
		$this->page->setContent($content);
	}
	
	public function serialize() {
		$this->page->serialize();
	}
	
	/**
	 * Returns the singleton instance of the SiteSelector.
	 * @return SiteSelector The singleton instance of the SiteSelector.
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * Sets the singleton instance of the SiteSelector.
	 * @param SiteSelector $site The singleton instance of the SiteSelector.
	 */
	public function setPage(Page $page) {
		$this->page = $page;
	}
}
?>