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
			return "default";
		}
	}	
	
	/** 
	 * Returns the extension/format of the requested page (e.g. 'html', 'xml' or 'pdf').
	 * @return string The extension/format of the requested page.
	 */
	public static function getExtension() {
		if (isset($_GET['extension'])) {
			return $_GET['extension'];
		} else {
			return "html";
		}
	}
	
	public static function getParameters() {
		$result = array();
		
		foreach($_GET as $key => $value) {
			if ($key != "page" && $key != "extension" && $key != "language"){
				$result[$key] = $value;
			}
		}
		return $result;
	}
}
?>