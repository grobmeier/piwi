<?php
class TestObject3 {
	public $paramString = null;
	
	public $testObject2 = null;
	
	public function __construct($paramString, TestObject2 $testObject2) {
		$this->paramString = $paramString;
		$this->testObject2 = $testObject2;
	}
}
?>