<?php
/**
 * Crawls a PIWI-Webpage, to make it static.
 */
class Crawler {
	/** The server where the website, that should be crawled, is hosted. */
	private $host = null;
	
	/** The languages that, apart from 'default', should be crawled. */
	private $languages = null;
	
	/** The directory where the crawled site should be placed. */
	private $targetDirectory = null;
	
	/**
	 * Constructor.
	 * @param string $host The server where the website, that should be crawled, is hosted.
	 * @param array $languages The languages that, apart from 'default', should be crawled.
	 * @param string $targetDirectory The directory where the crawled site should be placed.
	 */
	public function __construct($host, $languages, $targetDirectory) {
		$this->host = $host;
		$this->languages = $languages;
		$this->targetDirectory = $targetDirectory;
	}
	
	public function startCrawling() {
		echo 'startCrawling';
	}	
}
?>