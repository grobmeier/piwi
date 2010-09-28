<?php
date_default_timezone_set('Europe/Berlin');

require_once ('test/autoload.php');
require_once ('third-party/simpletest/autorun.php');
require_once ('third-party/simpletest/unit_tester.php');

// if error reporting has another level, test build fails due to a php bug in xmllib
//error_reporting(0);
error_reporting(E_ALL);

$GLOBALS['PIWI_ROOT'] = dirname(__FILE__) . '/../piwi/';
    // Local server 
    if (isset($argv[1])) {
      require_once "test/PiwiWebTestCase.php";
      PiwiWebTestCase::$HOST = $argv[1];
    }

    // Generate coverage report if path to coverage report is specified
    if (isset($argv[2])) {
      require_once "test/phpcoverage.inc.php";
      require_once PHPCOVERAGE_HOME . "/CoverageRecorder.php";
      require_once PHPCOVERAGE_HOME . "/reporter/HtmlCoverageReporter.php";

      $reporter = new HtmlCoverageReporter("Code Coverage Report", "", $argv[2]);

      $includePaths = array("piwi/lib/piwi");
      $excludePaths = array("test");
      $cov = new CoverageRecorder($includePaths, $excludePaths, $reporter);

      $cov->startInstrumentation();
    }
    
    // TEST CASES   
    require_once('third-party/simpletest/autorun.php');

    class AllTests extends TestSuite {
        function AllTests() {
            $this->TestSuite('All tests');            
            include('TestSuite.php');
        }
    }
?>