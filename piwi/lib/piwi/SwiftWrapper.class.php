<?php 
/**
 * Wraps the original Swift autoloader to prevent swift init when not necessary.
 * The code could have been written in to Swift itself, but 
 */
class SwiftWrapper {
	private static $dependency = false;
	public static function autoload($class) {
		if(!self::$dependency) {
			self::$dependency = true;
			require_once ('lib/swiftmailer/swift_init.php');
		}
		Swift::autoload($class);
	}
}
?>