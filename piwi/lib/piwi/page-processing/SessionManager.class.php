<?php
/**
 * Manages all Session features.
 */
class SessionManager {
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
	 * Starts the Session.
	 */
	public static function startSession() {
		session_start();
	}
}
?>