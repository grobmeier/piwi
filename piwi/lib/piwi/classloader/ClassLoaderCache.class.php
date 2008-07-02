<?php
/**
 * SimpleXML based cache implementation for the classloader.
 */
class ClassLoaderCache {
	private $pathtocachefile = null;
	private $cachexml = null;
	private $changed = false;
	
	/**
	 * Constructor. If construction this object,
	 * you need an absolut path to the destination file.
	 * All folders must exist.
	 */
	function ClassLoaderCache($pathtocachefile) {
		$this->pathtocachefile = $pathtocachefile;
	}
	
	/**
	 * Adds a class to the cache. The ID is the classname.
	 * Forces the cache to write, when the writeCache method is
	 * called.
	 * @param String $id the classname and identifier
	 * @param String $path the path to this class (without classname)
	 */
	public function addClassToCache($id, $path) {
		if($this->cachexml == null) {
			$this->loadCache();
		}	
		$class = $this->cachexml->addChild('class');
		$class->addAttribute('id', $id);
		$class->addAttribute('path', $path);
		$this->changed = true;
	}
	
	public function invalidate($id) {
		if(file_exists($this->pathtocachefile)) {
			$this->cachexml = null;
			unlink($this->pathtocachefile);
			$this->changed = false;
		}
	}

function dnl2array($domnodelist) {
    $return = array();
    for ($i = 0; $i < $domnodelist->length; ++$i) {
    	echo ".";
        $return[] = $domnodelist->item($i);
    }
    return $return;
}
	
	/**
	 * Lookup a class by the given id in the cache. 
	 * @param String $id The id of this class (the class name)
	 * @return String $path The path to this class (without class name), or null, if it couldn't be found'
	 */
	public function getClassById($id) {
		if($this->cachexml == null) {
			$this->loadCache();
		}	
		$result = $this->cachexml->xpath("//class[@id='".$id."']");
		if(empty($result)) {
			return null;
		} else {
			return $result[0]['path'];
		}
	}
	
	/**
	 * Writes the cache. Writes only, if the cache has changed.
	 */
	public function writeCache() {
		if($this->changed != true) {
			return;
		}
		
		$fpread = fopen($this->pathtocachefile,"w");
		fwrite($fpread,$this->cachexml->asXML());
		fclose($fpread);
		$this->changed = false;
	}
	
	/**
	 * Loads the cache file. If it doesn't exist, it will be created.
	 */
	private function loadCache() {
		if (!file_exists($this->pathtocachefile)) {
			$cachefile .= "<?xml version='1.0' standalone='yes'?>\n";
			$cachefile .= "<!DOCTYPE classloadercache SYSTEM \"dtd/classloadercache.dtd\">\n";
			$cachefile .= "<classloadercache />\n";
			$fpread = fopen($this->pathtocachefile,"a+");
			fwrite($fpread,$cachefile);
			fclose($fpread);
		} 
    	$this->cachexml = simplexml_load_file($this->pathtocachefile);
	}
}
?>