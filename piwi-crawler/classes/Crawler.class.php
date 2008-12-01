<?php
require_once('Page.class.php');
/**
 * Crawls a PIWI-Webpage, to make it static.
 */
class Crawler {
	/** The adress of the server. */
	public static $server = null;
	
	/** The url where the crawling should be started. */
	private $startURL = null;
	
	/** The languages that, apart from 'default', should be crawled. */
	private $languages = null;
	
	/** The directory where the crawled site should be placed. */
	public static $targetDirectory = null;
	
	private $formats = null;
	
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
	 * @param array $formats The formats that, apart from 'html', should be crawled.
	 */
	public function __construct($server, $startURL, $languages, $targetDirectory, $formats) {
		self :: $server = $server;
		$this->startURL = $startURL;
		$this->languages = $languages;
		self :: $targetDirectory = $targetDirectory;
		$this->formats = $formats;
	}
	
	/**
	 * Starts the crawling.
	 */
	public function startCrawling() {
		echo "Starting Crawling: " . self::$server . $this->startURL . "\n";

		// Crawl default language
		$this->crawlPageByLanguage();

		// Crawl extra languages
		foreach ($this->languages as $language) {
			$this->crawlPageByLanguage($language);
		}
		echo "Finished Crawling: " . self::$server . $this->startURL . "\n";
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
		$this->pagesToBeCrawled[$this->startURL] = new Page($this->startURL, $language);
		
		echo "\nLanguage: " . ($language == '' ? "default" : $language) . "\n";
		
		// Crawl until no more pages can be found
		echo " HTML:\n";
		while (sizeof($this->pagesToBeCrawled) > 0) {
			$page = array_pop($this->pagesToBeCrawled);
			
			echo ($language == '' ? "default" : $language) . ": " . $page->getURL() . "\n";			
			
			foreach ($page->getInternalLinks() as $url) {
				// Only add pages that are not crawled yet
				if ($page->getURL() != $url && !isset($this->pagesToBeCrawled[$url]) && !isset($this->pagesAlreadyCrawled[$url])) {
					$this->pagesToBeCrawled[$url] = new Page($url, $language);
				}
			}
			
			$this->pagesAlreadyCrawled[$page->getURL()] = $page;
			$page->save();
		}
		
		// Crawl other formats
		foreach ($this->formats as $format) {
			echo " " . strtoupper($format) . ":\n";
			foreach ($this->pagesAlreadyCrawled as $page) {
       			$page->setFormat($format);
       			echo ($language == '' ? "default" : $language) . ": " . $page->getURL() . "\n";	
       			$page->save();
			}
		}
	}
}
?>