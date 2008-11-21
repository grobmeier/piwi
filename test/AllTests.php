<?php
require_once('simpletest/autorun.php');

DEFINE('PIWITEST', 'test/');

class AllTests extends TestSuite {
    function AllTests() {
        $this->TestSuite('All tests');
        $this->addFile(PIWITEST . 'lib/piwi/connector/ConnectorFactoryTest.php');
        $this->addFile(PIWITEST . 'lib/piwi/generator/GeneratorFactoryTest.php');
    }
}
?>
