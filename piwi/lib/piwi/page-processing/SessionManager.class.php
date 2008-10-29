<?php
/**
 * Manages all Session features.
 */
class SessionManager {
	/**
	 * Returns the prefered language of the user or 'default' if none is set.
	 * @return string The prefered language of the user.
	 */
	public static function getUserLanguage() {
		// Check if user has changed its prefered language
		if (isset($_GET['language'])) {
			$_SESSION['language'] = $_GET['language'];
		}
		
		if (!isset($_SESSION['language'])) {
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				// Language has not been set by user, so get prefered language from browser
				$_SESSION['language'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);					
			}  else {
				// Use 'default' if no language has not been set
				$_SESSION['language'] = 'default';
			}
		}
		return $_SESSION['language'];
	}
	
	/**
	 * Sets the user language in the Session.
	 * @param string $language The language to set.
	 */
	public static function setUserLanguage($language) {
		$_SESSION['language'] = $language;
	}
	
	/**
	 * Starts the Session.
	 */
	public static function startSession() {
		session_start();
	}
}
?>