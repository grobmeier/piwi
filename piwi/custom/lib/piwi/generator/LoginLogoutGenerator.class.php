<?php
/**
 * Custom Generator.
 * This is a sample of a custom Generator.
 * It displays an login or logout form depending on the user current state.
 */
class LoginLogoutGenerator implements Generator {
	/**
	 * Constructor.
	 */
    function __construct() {
    }
   
   	/**
	 * Returns the xml output of the Generator.
	 * @return string The xml output as string.
	 */
    function generate() {
		// generate xml
		$piwixml = '<piwiform id="loginform" />';
		
		if(SessionManager::isUserAuthenticated()) {
			$piwixml = '<piwiform id="logoutform" />';
		}
		
		return $piwixml;
	}
	
	/**
	 * Used to pass parameters to the Generator.
	 * @param string $key The name of the parameter.
	 * @param object $value The value of the parameter.
	 */
    function setProperty($key, $value) {
    }
}
?>