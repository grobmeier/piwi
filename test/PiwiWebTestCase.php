<?php
require_once('test/autoload.php');
require_once('third-party/simpletest/autorun.php');
require_once('third-party/simpletest/web_tester.php');

class PiwiWebTestCase extends WebTestCase {
  public static $HOST = "http://127.0.0.1:80/";
}

?>