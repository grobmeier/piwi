<?php
class TestObject3 {
	public $paramString = null;
	
	public $testObject2 = null;
	
	public $blub = null;
	
	public $blubberParam;
	public $blubber2Param;
	
	public function __construct($paramString, TestObject2 $testObject2) {
		$this->paramString = $paramString;
		$this->testObject2 = $testObject2;
	}
	
	public function blubber($p) {
		$this->blubberParam = $p;
	}
	
	public function setBlubber2($p) {
		$this->blubber2Param = $p;
	}
}
?>