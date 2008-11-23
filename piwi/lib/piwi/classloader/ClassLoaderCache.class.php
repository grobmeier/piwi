<?php
/**
 * SimpleXML based cache implementation for the Classloader.
 */
class ClassLoaderCache {
	/** Path to the cache file. */
	private $pathToCacheFile = null;
	
	/** The cache file as XML. */
	private $cachexml = null;
	
	/** Indicates whether the cache has changed since it was loaded. */
	private $changed = false;
	
	/**
	 * Constructor.
	 * If construction this object,
	 * you need an absolut path to the destination file.
	 * All folders must exist.
	 * @param string $pathToCacheFile Path to the cache file.
	 */
	public function __construct($pathToCacheFile) {
		$this->pathToCacheFile = $pathToCacheFile;
	}
	
	/**
	 * Destructor.
	 */
	public function __destruct() {
		// Writes the cache if is has changed
		if($this->changed) {
			$fpread = fopen($this->pathToCacheFile, "w");
			fwrite($fpread, $this->cachexml->asXML());
			fclose($fpread);
			$this->changed = false;
		}
	}
	
	/**
	 * Adds a class to the cache.
	 * Forces the cache to write, when the writeCache method is
	 * called.
	 * @param string $id The name of the class and identifier.
	 * @param string $path The path to this class (without classname).
	 */
	public function addClassToCache($classname, $path) {
		if($this->cachexml == null) {
			$this->loadCache();
		}	
		$class = $this->cachexml->addChild('class');
		$class->addAttribute('id', $classname);
		$class->addAttribute('path', $path);
		
		$this->changed = true;
	}
	
	/**
	 * Resets the cache, should be called if a class 
	 * retrieved from the cache does no more exist 
	 * in the filesystem.
	 */
	public function invalidate() {
		if(file_exists($this->pathToCacheFile)) {
			$this->cachexml = null;
			unlink($this->pathToCacheFile);
			$this->changed = false;
		}
	}
	
	/**
	 * Lookup a class by the given id in the cache. 
	 * @param string $classId The id of this class (the class name).
	 * @return string The path to this class (without class name), or null, if it couldn't be found.
	 */
	public function getClassById($classId) {
		if($this->cachexml == null) {
			$this->loadCache();
		}	
		$result = $this->cachexml->xpath("//cache:class[@id='".$classId."']");
		if(empty($result)) {
			return null;
		} else {
			return $result[0]['path'];
		}
	}
	
	/**
	 * Loads the cache file. If it doesn't exist, it will be created.
	 */
	private function loadCache() {
		if (!file_exists($this->pathToCacheFile)) {
			$cachefile = '<?xml version="1.0" encoding="UTF-8"?>';
			$cachefile .= '<classloadercache xmlns="http://piwi.googlecode.com/xsd/cache" 
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
xsi:schemaLocation="http://piwi.googlecode.com/xsd/cache ../resources/xsd/cache.xsd" />';
			$fpread = fopen($this->pathToCacheFile,"a+");
			fwrite($fpread,$cachefile);
			fclose($fpread);
		} 
    	$this->cachexml = simplexml_load_file($this->pathToCacheFile);
    	$this->cachexml->registerXPathNamespace('cache', 'http://piwi.googlecode.com/xsd/cache');    	
	}
}
?>