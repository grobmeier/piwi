<?php
    // Generate coverage report if path to coverage report is specified
    if (isset($argv[1])) {
      require_once "test/phpcoverage.inc.php";
      require_once PHPCOVERAGE_HOME . "/CoverageRecorder.php";
      require_once PHPCOVERAGE_HOME . "/reporter/HtmlCoverageReporter.php";

      $reporter = new HtmlCoverageReporter("Code Coverage Report", "", $argv[1]);

      $includePaths = array("piwi/lib/piwi");
      $excludePaths = array("test");
      $cov = new CoverageRecorder($includePaths, $excludePaths, $reporter);

      $cov->startInstrumentation();
    }
    
    // TEST CASES   
    include('simpletest/autorun.php');

    class AllTests extends TestSuite {
        function AllTests() {
            $this->TestSuite('All tests');            
            include('TestSuite.php');
        }
    }
?>