<?php
// TODO: should differ on the input source between several Page implementations
/**
 * TODO: Write comment
 */
class SiteSelector {
	/** Reference to the processed page. */
	private $page = null;
	
	/**
	 * Constructor.
	 */
	public function __construct() {
	}
	
	/**
	 * Processes the contents of the page.
	 */
	public function generateContent() {
		$this->page->generateContent();
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
}
?>