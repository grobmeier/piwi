<?php
/**
 * Represents a webpage.
 */
class Page {
	/** The url of the page. */
	private $url = null;
	
	/** The language to use. Set to null for the default language. */ 
	private $language = null;
	
	/** The content of the page */
	private $content = null;
	
	/** If one of these strings appears in a link it is not handled as internal. */
	private static $resourcesFormats = array(".jpg", ".png", ".gif", ".js", ".css");
	
	/**
	 * Constructor.
	 * @param string $url The url of the page.
	 * @param string $language The language to use. Set to null for the default language.
	 */
	public function __construct($url, $language = '') {
		$this->url = $url;
		$this->language = $language;
	}
	
	/**
	 * Returns the language of the page.
	 * @return string The language of the page.
	 */
	public function getURL() {
		return $this->url;
	}
	
	/**
	 * Returns an array containing all internal links appearing in the current page.
	 * @return array An array containing all internal links appearing in the current page.
	 */
	public function getInternalLinks() {
		$language = '?language=' . $this->language;
		if (strpos($this->url, "?") != false) {
			$language = '&language=' . $this->language;
		}
		$file = fopen(Crawler::$server . $this->url . $language, "r");
		
		if (!$file) {
			echo "  Failed to open '" . $this->url . "'\n";
			return array();
		}
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
	 */
	public function save() {
		if ($this->content == null) {
			return;
		}
		
		$this->rewriteLinks();		
		
		$filename = $this->url;
		// Build filename
		if (strpos($filename, '?') != false) {
			$filename = self :: createFilename($filename);
		}
		
		$fpread = fopen(Crawler :: $targetDirectory . $this->language . $filename, "w");
		if (!$fpread) {
			echo "  Failed to save '" . $this->url . "'\n";
			$this->content = null;
			return;
		}
		fwrite($fpread, $this->content);
		fclose($fpread);
		$this->content = null;
	}
	
	/**
	 * Rewrites all links occuring in the content, to ensure they match in the crawled page.
	 */
	private function rewriteLinks() {
		$this->content = preg_replace_callback("~(href|src)=\"(.+?)\"~", "Page::rewriteLink", $this->content);
	}
	
	/**
	 * Returns the rewritten link.
	 * The link will be rewritten depending on the target it has.
	 * @param $matches The array containing the link.
	 * @return string The rewritten link.
	 */
	private static function rewriteLink($matches) {
		$link = $matches[2];
		$link = str_replace(Crawler::$server, '', $link);
		if (substr($link, 0, 4) == 'http') {
			// external link			
		} else if (Page::isLinkResource($link)) {
			//resources			
			$link = Page::saveResource($link);
		} else if (strpos($link, "language=") != false) {
			// link to switch language
			preg_match("~[\?|&]language=([^&|.]+)~", $link, $lang);
			$link = str_replace($lang[0], '', $link);
			$link = Page::createFilename($link);

			$link = ($lang[1] == 'default' ? '' : $lang[1]) . $link;
		
		} else if (strpos($link, "?") != false) {
			// link with arguments
			$link = Page::createFilename($link);
		}
		
		return $matches[1] . '="' . $link . '"';
	}
	
	/**
	 * Saves the given resource to access them in the static page.
	 * @param string $url The url of the resource.
	 * @return string The local path of the resource.
	 */
	private static function saveResource($url) {
		$file = fopen(Crawler::$server . $url, "r");		
		if (!$file) {
			echo "  Failed to open resource '" . $url . "'\n";
			return $url;
		}
		while (!feof($file)) {
			$content .= fgets($file, 1024);
		}
		fclose($file);		
		
		$directory = Crawler :: $targetDirectory . "resources/";
		if (($filename = strrchr($url, "/")) != false) {
			$relativePath = str_replace($filename, "", $url) . '/';
		}

		if (!is_dir($directory . $relativePath) && !mkdir($directory . $relativePath, 0777, true)) {
			echo "  Failed to save resource '" . $url . "'\n";
			return $url;
		}
		
		$fpread = fopen(Crawler :: $targetDirectory . "resources/". $url, "w");
		if (!$fpread) {
			echo "  Failed to save resource '" . $url . "'\n";
			return $url;
		}
		fwrite($fpread, $content);
		fclose($fpread);

		// Also save resources from CSS
		if (strpos($url, ".css") != false) {		
			preg_match_all("~url\(\"*'*(.+?)'*\"*\)~", $content, $matches);

			foreach ($matches[1] as $resource) {
				Page::saveResource($relativePath . $resource);
			}
		}
		
		return "resources/". $url;
	}	
	
	/**
	 * Moves the extension at the end of the filename and replaces special characters.
	 * E.g 'gallery.html?id=123&dir=test' will result in 'gallery_id_123_dir_test.html'.
	 * @param string $filename The filename to rewrite.
	 * @return string The rewritten filename.
	 */
	private static function createFilename($filename) {
		preg_match("~(\..+)\?~", $filename, $matches);
		$extension = $matches[1];
				
		$filename = str_replace('?', '_' , $filename);
		$filename = str_replace('&', '_' , $filename);
		$filename = str_replace('=', '_' , $filename);
		$filename = str_replace($extension, '' , $filename);

		return $filename . $extension;
	}
	
	/**
	 * Determinates if the given link refers to an internal page or not.
	 * @return boolean True if link refers to an internal page, otherwise false.
	 */
	private function isLinkInternal(&$link) {
		$link = str_replace(Crawler::$server, '', $link);
		if (substr($link, 0, 4) == 'http') {
			return false;			
		} else {
			if (self :: isLinkResource($link)) {
				return false;
			}
			if (strpos($link, "language=") != false) {
				return false;
			}
			return true;
		}
	}
	
	/**
	 * Determinates whether the given link targets a resource.
	 * @param string $link The link to test.
	 * @return boolean True if the link targets a resource, otherwise false.
	 */
	private static function isLinkResource($link) {
		foreach (Page::$resourcesFormats as $value) {
   			if (strpos($link, $value) != false) {
				return true;
			}
		}
		return false;
	}
}
?>