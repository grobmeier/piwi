<?php
/**
 * Manages all Session features.
 */
class SessionManager {
	/** The users preferred language */
	private static $language = null;

	/**
	 * Returns the prefered language of the user or 'default' if none is set.
	 * @return string The prefered language of the user.
	 */
	public static function getUserLanguage() {
		if (self::$language == null) {
			// Check if user has changed its prefered language
			$parameters = Request::getParameters();
			if (isset($parameters['user-language'])) {
				self::$language = $parameters['user-language'];
			} else {				
				if (isset($_SESSION['user-language'])) {
					// If Session contains language use this as prefered language
					self::$language = $_SESSION['user-language'];					
				} else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
					// Language has not been set by user, so get prefered language from browser
					self::$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);					
				}  else {
					// Use 'default' if no language has not been set
					self::$language = 'default';
				}
			}
			
			// Store language in Session
			$_SESSION['user-language'] = self::$language;
		}
		return self::$language;
	}
	
	/**
	 * Starts the Session.
	 */
	public static function startSession() {
		session_start();
	}
}
?>