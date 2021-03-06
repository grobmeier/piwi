<?php
require_once('ClassLoaderCache.class.php');
/**
 * ClassLoader loads classes necessary for PIWI.
 * Most of this classes have been defined within xml files
 * and are known with their classname only. This classloader
 * doesn't care about namespaces, since this is an experimental
 * and new feature.
 * The ClassLoader can lookup classes recursivly and includes
 * it with requires_once. Classnames should follow normal
 * naming procedures, plus an .class.php or .if.php extension.
 * Otherwise this class cannot be found.
 * The ClassLoader makes use of an xml cache. If an class
 * can be found here, PIWI checks if it still exists. If it does,
 * the class will be used. If it doesn't the cache will be updated.
 * If the class cannot be found within the cache, PIWI trys to lookup
 * it like mentioned above and puts it into the cache.
 */
class ClassLoader {
	/** ClassLoader Cache - will be initalized lazily. */
	private $cache = null;
	
	/** For singleton usage **/
	private static $classloader = null;
	
	/**
	 * Constructor.
	 * This class can be instantiated with an valid
	 * destination to an cachefile. If the file doesn't exist,
	 * it will be created. If it exists, it will be used.
	 * If this argument is not passed, no cache is used at all.
	 * @param string $pathtocachefile Path to the cache file.
	 */
	public function __construct($pathToCacheFile = null) {
		if ($pathToCacheFile != null) {
			$this->cache = new ClassLoaderCache($pathToCacheFile);
		}	
	}

	/**
	 * Autoload uses the ClassLoader class to recursively look up
	 * needed classes. Use one file per class/interface and name it: 
	 * 	- yourclass.class.php
	 * 	- yourinterface.if.php
	 * @param string $class Name of the of the class or interface.
	 */
	public static function autoload($class) {
		if (self::$classloader == null) {
			self::$classloader = new ClassLoader($GLOBALS['PIWI_ROOT'] . 'cache/classloader.cache.xml');
		}
	
		$directorys = array ('lib',CUSTOM_CLASSES_PATH);
	
		foreach ($directorys as $directory) {
			$result = self::$classloader->loadClass($directory, $class);
			if ($result == true) {
				return;
			}
		}
	}
	
	/**
	 * Tries to load a class from the given directory. 
	 * All subdirectories are used for lookup. 
	 * @param string $directory The path in which the class should be found.
	 * @param string $class The name of the class.
	 * @return boolean True, if the class could be found, false otherwise.
	 */
	public function loadClass($directory, $class, $cache = true) {
		// Check if this class is in the cache
		if ($this->cache != null && $cache == true) {
			$path = $this->cache->getClassById($class);
			if ($path != null) {
				if (file_exists($path . '/' . $class . '.class.php')) {
					require_once($path . '/' . $class . '.class.php');
			        return true;
				} else if (file_exists($path . '/' . $class . '.if.php')) {
					require_once($path . '/' . $class . '.if.php');
			        return true;
				} else if (file_exists($path . '/' . $class . '.php')) {
					require_once($path . '/' . $class . '.php');
			        return true;
				} else {
					$this->cache->invalidate();
				}
			}
		}
		
		// Lookup class in the filesystem
		if ($handle = opendir($directory)) {
		    while (false !== ($file = readdir($handle))) {
		    	if ($file != "." && $file != "..") {
		        	if (is_dir($directory . '/' . $file)) {
		        		if (substr($file, 0, 1) != ".") {
		        			$result = $this->loadClass($directory . '/' . $file, $class, false);
			        		if ($result == true) {
			        			return true;
			        		}
		        		} 
		        	} else {
		            	if ($file == $class . '.class.php') {
		            		require_once($directory . '/' . $class . '.class.php');
		            		if ($this->cache != null) {
		            			$this->cache->addClassToCache($class, $directory);
		            		}
		            		return true;
		            	} else if ($file == $class . '.php') {
		            		require_once($directory . '/' . $class . '.php');
		            		if ($this->cache != null) {
		            			$this->cache->addClassToCache($class, $directory);
		            		}
		            		return true;
		            	} else if ($file == $class . '.if.php') {
		            		require_once($directory . '/' . $class . '.if.php');
		            		if ($this->cache != null) {
		            			$this->cache->addClassToCache($class, $directory);
		            		}
		            		return true;
		            	}
		        	}
		        }
		    }
		    closedir($handle);
		}
		return false;
	}	
}
?>