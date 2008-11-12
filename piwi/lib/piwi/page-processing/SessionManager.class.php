<?php
/**
 * Manages all Session features.
 */
class SessionManager {
	/**
	 * Starts the Session.
	 */
	public static function startSession() {
		session_start();
	}
	
	/**
	 * -------------------------------------------------------------------------
	 * >>>>>>>>>>>>>>>>>>>>>>>>>> Language Management <<<<<<<<<<<<<<<<<<<<<<<<<<
	 * -------------------------------------------------------------------------
	 */	
	/**
	 * Returns the preferred language of the user or 'default' if none is set.
	 * @return string The preferred language of the user.
	 */
	public static function getUserLanguage() {
		// Check if user has changed its prefered language
		if (isset($_GET['language'])) {
			if (in_array($_GET['language'], Site::getInstance()->getSupportedLanguages())) {
				$_SESSION['language'] = $_GET['language'];
			}	
		}
		
		if (!isset($_SESSION['language'])) {
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				// Language has not been set by user, so get prefered language from browser
				$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
				if (in_array($language, Site::getInstance()->getSupportedLanguages())) {
					$_SESSION['language'] = $language;
				}							
			}
		}
		
		if (!isset($_SESSION['language'])) {
			// Use 'default' if no language has not been set
			$_SESSION['language'] = 'default';
		}
		return $_SESSION['language'];
	}
	
	/**
	 * -------------------------------------------------------------------------
	 * >>>>>>>>>>>>>>>>>>>>>>>>>>>> User Management <<<<<<<<<<<<<<<<<<<<<<<<<<<<
	 * -------------------------------------------------------------------------
	 */
	/**
	 * Logs in the given user if its password is valid.
	 * The password is valid the user will be redirected to the 
	 */
	public static function loginUser($username, $password, $useCookies = false) {
		// Validate the password
		$userValid = Site::getInstance()->getRoleProvider()->isPasswordValid($username, $password);
		
		if ($userValid) {
			// Redirect to the desired page
			if (isset($_SESSION['ReturnUrl'])) {
				header('Location: ' . $_SESSION['ReturnUrl']);
			}
			
			// Update session
			$_SESSION['authenticated'] = true;
			$_SESSION['username'] = $username;
			unset($_SESSION['ReturnUrl']);	
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Logs out the user.
	 */
	public static function logoutUser() {
		unset($_SESSION['authenticated']);
		unset($_SESSION['username']);		
	}
	
	/**
	 * Returns the username if the user is currently logged in, otherwise null.
	 * @return string The username if the user is currently logged in, otherwise null.
	 */
	public static function getUserName() {		
		return isset($_SESSION['username']) ? $_SESSION['username'] : null;
	}
	
	/**
	 * Returns true if the current user is authenticated.
	 * @return boolean True if the current user is authenticated.
	 */
	public static function isUserAuthenticated() {
		// Store requested URL in session
		$uri = 'http://';				
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$uri = 'https://';
		}
					
		$uri .= $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
		
		$_SESSION['ReturnUrl'] = $uri;
		
		// Check if user is authenticated
		return (isset($_SESSION['authenticated']) && $_SESSION['authenticated']);
	}
}
?>