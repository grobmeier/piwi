<?php
/**
 * Manages the localized labels.
 */
class ResourcesManager {
	/** Array of DOMXPath Documents used to search for labels. */
	private static $resources = null;
	
	/**
	 * Looks up the localized text for the given key.
	 * If no translation is found the key itself is returned.
	 * @param string $key The key to look for.
	 * @return string The localized text or the key if no translation has been found.
	 */
	public static function getLabelText($key) {
		if (self::$resources == null) {
			self::loadResources();
		}
		
		foreach (self::$resources as $domXPath) {			
       		$result = $domXPath->query("/labels:labels/labels:language[@region='" . 
       			UserSessionManager::getUserLanguage() . "']/labels:label[@key='" . $key . "']");

       		if ($result->length >= 1) {
    			return $result->item(0)->getAttribute('value');
        	}
		}
		return $key;
	}
	
	/**
	 * Initializes the resources array.
	 */
	private static function loadResources() {
		self::$resources = array();
		
		// Load the internal labels
		$domXPath = new DOMXPath(DOMDocument::load($GLOBALS['PIWI_ROOT'] . 'resources/labels/labels.xml'));
		$domXPath->registerNamespace('labels', 'http://piwi.googlecode.com/xsd/labels');
		
		self::$resources[] = $domXPath;
		
		// Load custom labels
		$path = ConfigurationManager::getInstance()->getCustomLabelsPath();
		if (file_exists($path)) {
			$domXPath = new DOMXPath(DOMDocument::load($GLOBALS['PIWI_ROOT'] . $path));
			$domXPath->registerNamespace('labels', 'http://piwi.googlecode.com/xsd/labels');
			
			self::$resources[] = $domXPath;
		}
	}
}
?>