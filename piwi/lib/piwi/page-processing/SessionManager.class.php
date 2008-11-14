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
	 * If the password is valid the user will be redirected to the originally desired page.
	 * @param string $username The username.
	 * @param string $password The password.
	 * @param boolean $useCookies True if cookies should be used.
	 * @param integer $sessionTime Number of seconds the cookie should be valid.
	 * @return boolean True if login was successful otherwise false.
	 */
	public static function loginUser($username, $password, $useCookies = false, $sessionTime = 3600) {
		// Validate the password
		$userValid = Site::getInstance()->getRoleProvider()->isPasswordValid($username, $password);
		
		if ($userValid) {
			// Store cookie
			if ($useCookies) {
				setcookie("username", $username, time() + $sessionTime);
				setcookie("password", $password, time() + $sessionTime);
			}
			
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
		
		// Delete cookie if it exists
		if (isset($_COOKIE["username"]) || isset($_COOKIE["password"])) {
			setcookie("username", "", time() - 3600);
			setcookie("password", "", time() - 3600);
		}	
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
		if ((isset($_SESSION['authenticated']) && $_SESSION['authenticated'])) {
			// In this case the user is already logged in
			return true;
		} else if (isset($_COOKIE["username"]) && isset($_COOKIE["password"])) {
			// In this case the user has a cookie. Validate the password and login the user if it is correct.
			$userValid = Site::getInstance()->getRoleProvider()->isPasswordValid($_COOKIE["username"], $_COOKIE["password"]);
			if ($userValid) {
				// Password is valid
				$_SESSION['authenticated'] = true;
				$_SESSION['username'] = $_COOKIE["username"];
				unset($_SESSION['ReturnUrl']);
				return true;
			} else {
				// In this case the cookie is invalid
				return false;
			}
		}
	}
}
?>