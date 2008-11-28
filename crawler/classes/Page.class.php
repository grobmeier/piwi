<?php
/**
 * Represents a webpage.
 */
class Page {
	/** The adress of the server. */
	private $server = null;
	
	/** The url of the page. */
	private $url = null;
	
	/** The language to use. Set to null for the default language. */ 
	private $language = null;
	
	/** The content of the page */
	private $content = null;
	
	private static $noLinkIndicators = array("language=", ".jpg", ".png", ".gif");
	
	/**
	 * Constructor.
	 * @param string $server The adress of the server.
	 * @param string $url The url of the page.
	 * @param string $language The language to use. Set to null for the default language.
	 */
	public function __construct($server, $url, $language = '') {
		$this->server = $server;
		$this->url = $url;
		$this->language = $language;
	}
	
	/**
	 * 
	 */
	public function getInternalLinks() {		
		$file = fopen($this->server . $this->url, "r");
		while (!feof($file)) {
			$this->content .= fgets($file, 1024);
		}
		fclose($file);
		
		preg_match_all("~<a .*?href=\"(.+?)\"~", $this->content, $matches);
		
		$internalLinks = array();
		foreach ($matches[1] as $link) {
       		if ($this->isLinkInternal($link)) {
       			$internalLinks[$link] = $link;
       		}
		}		
		
		return $internalLinks;
	}
	
	/**
	 * Saves the webpage to disk.
	 * @param string $targetDirectory The directory where the page webpage should be saved in.
	 */
	public function save($targetDirectory) {
		if ($this->content == null) {
			return;
		}
		$filename = str_replace('?', '_' , str_replace('&', '_' ,$this->url));
		$fpread = fopen($targetDirectory . $this->language . $filename, "w");
		fwrite($fpread, $this->content);
		fclose($fpread);
		$this->content = null;
	}
	
	/**
	 * Returns the language of the page.
	 * @return string The language of the page.
	 */
	public function getURL() {
		return $this->url;
	}
	
	private function isLinkInternal(&$link) {
		$link = str_replace($this->server, '', $link);
		if (substr($link, 0, 4) == 'http') {
			return false;			
		} else {
			foreach (self::$noLinkIndicators as $value) {
       			if (strpos($link, $value) != false) {
					return false;
				}

			}
			return true;
		}
	}
}
?>