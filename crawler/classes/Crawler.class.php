<?php
require_once('Page.class.php');
/**
 * Crawls a PIWI-Webpage, to make it static.
 */
class Crawler {
	/** The adress of the server. */
	private $server = null;
	
	/** The url where the crawling should be started. */
	private $startURL = null;
	
	/** The languages that, apart from 'default', should be crawled. */
	private $languages = null;
	
	/** The directory where the crawled site should be placed. */
	private $targetDirectory = null;
	
	/** The pages that still have to be crawled. */
	private $pagesToBeCrawled = null;
	
	/** The pages that have already been crawled. */
	private $pagesAlreadyCrawled = null;
	
	/**
	 * Constructor.
	 * @param string $server The adress of the server.
	 * @param string $startURL The url where the crawling should be started.
	 * @param array $languages The languages that, apart from 'default', should be crawled.
	 * @param string $targetDirectory The directory where the crawled site should be placed.
	 */
	public function __construct($server, $startURL, $languages, $targetDirectory) {
		$this->server = $server;
		$this->startURL = $startURL;
		$this->languages = $languages;
		$this->targetDirectory = $targetDirectory;
	}
	
	/**
	 * Starts the crawling.
	 */
	public function startCrawling() {
		echo "Starting Crawling\n";

		// Crawl default language
		$this->crawlPageByLanguage();

		// Crawl extra languages
		foreach ($this->languages as $language) {
			$this->crawlPageByLanguage($language);
		}
		echo "Finished Crawling\n";
	}	
	
	/**
	 * Starts crawling the website using the given language.
	 * @param string $language The language to use. Set to null for the default language.
	 */
	private function crawlPageByLanguage($language = "") {
		// Reset arrays
		$this->pagesAlreadyCrawled = array();			
		$this->pagesToBeCrawled = array();
		
		// Set start url
		$this->pagesToBeCrawled[$this->startURL] = new Page($this->server, $this->startURL, $language);
		
		// Crawl until no more pages can be found
		while (sizeof($this->pagesToBeCrawled) > 0) {
			$page = array_pop($this->pagesToBeCrawled);
			
			if ($language == '') {
				echo "DEFAULT: " . $page->getURL() . "\n";
			} else {
				echo $language . ": " . $page->getURL() . "\n";
			}			
			
			foreach ($page->getInternalLinks() as $url) {
				// Only add pages that are not crawled yet
				if ($page->getURL() != $url && !isset($this->pagesToBeCrawled[$url]) && !isset($this->pagesAlreadyCrawled[$url])) {
					$this->pagesToBeCrawled[$url] = new Page($this->server, $url, $language);
				}
			}
			
			$this->pagesAlreadyCrawled[$page->getURL()] = $page;
			$page->save($this->targetDirectory);
		}
	}
}
?>