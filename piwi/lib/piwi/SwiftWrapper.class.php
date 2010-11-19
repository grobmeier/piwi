<?php 

class SwiftWrapper {
	private static $dependency = false;
	public static function autoload($class) {
		if(!self::$dependency) {
			require_once 'lib/swiftmailer/swift_init.php';
			self::$dependency = true;
		}
		Swift::autoload($class);
	}
}
?>