<?php
/**
 * Provides information about the current request that is performed by a user.
 */
class Request {
	/** 
	 * Returns the id of the requested page.
	 * @return string The id of the requested page.
	 */
	public static function getPageId() {
		if (isset($_GET['page'])) {
			return $_GET['page'];
		} else {
			return 'default';
		}
	}	

	/** 
	 * Sets the id of the requested page.
	 * @param string $pageId The id of the requested page.
	 */
	public static function setPageId($pageId) {
		$_GET['page'] = $pageId;
	}
	
	/** 
	 * Returns the extension/format of the requested page (e.g. 'html', 'xml' or 'pdf').
	 * @return string The extension/format of the requested page.
	 */
	public static function getExtension() {
		if (isset($_GET['extension'])) {
			return $_GET['extension'];
		} else {
			return 'html';
		}
	}
	
	/** 
	 * Sets the extension/format of the requested page (e.g. 'html', 'xml' or 'pdf').
	 * @param string $extension The extension/format of the requested page.
	 */
	public static function setExtension($extension) {
		$_GET['extension'] = $extension;
	}
	
	/** 
	 * Returns all parameters that are not PIWI internal contained in $_GET.
	 * @return array Array containing the parameters.
	 */
	public static function getParameters() {
		$result = array();
		
		if (ini_get('magic_quotes_gpc')) {
			foreach ($_GET as $key => $value) {
				if ($key != 'page' && $key != 'extension' && $key != 'language') {
					if (is_array($value)) {
						$subkeys = array_keys($value); 
						foreach ($subkeys as $subkey) {
						    $result[$key][$subkey] = htmlspecialchars(stripslashes($value[$subkey]));
						}				    
					} else {
						$result[$key] = htmlspecialchars(stripslashes($value));
					}
				}
 			}
		} else {
			foreach ($_GET as $key => $value) {
				if ($key != 'page' && $key != 'extension' && $key != 'language') {
					if (is_array($value)) {
						$subkeys = array_keys($value); 
						foreach ($subkeys as $subkey) {
						    $result[$key][$subkey] = htmlspecialchars($value[$subkey]);
						}				    
					} else {
						$result[$key] = htmlspecialchars($value);
					}
				}
			}
 		}
 		
		return $result;
	}
	
	/** 
	 * Returns the parameter with the given name.
	 * @return string The parameter.
	 */
	public static function getParameter($name) {
		$parameters = self :: getParameters();
		
		foreach ($parameters as $key => $value) {
			if ($key == $name) {
				return $value;
			}
		}
		
		return null;
	}
}
?>