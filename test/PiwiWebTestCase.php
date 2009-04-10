<?php
require_once('test/autoload.php');
require_once('third-party/simpletest/autorun.php');
require_once('third-party/simpletest/web_tester.php');

class PiwiWebTestCase extends WebTestCase {
	/** This is injected by ant.properties */
	public static $HOST = "";
}

?>