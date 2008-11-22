<?php
require_once('test/autoload.php');
require_once('test/simpletest/autorun.php');
require_once('simpletest/web_tester.php');

if (isset($argv[1])) {
	PiwiWebTestCase::$HOST = $argv[1];
}

class PiwiWebTestCase extends WebTestCase {
  public static $HOST = "http://127.0.0.1:80/";
}
?>