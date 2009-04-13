<?php
// TODO: should differ on the input source between several Page implementations
class SiteSelector {
	/** Singleton instance of the Site. */
	private $page = null;
	
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
	 * Returns the singleton instance of the Site.
	 * @return Site The singleton instance of the Site.
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * Sets the singleton instance of the Site.
	 * @param Site $site The singleton instance of the Site.
	 */
	public function setPage(Page $page) {
		$this->page = $page;
	}
}
?>
