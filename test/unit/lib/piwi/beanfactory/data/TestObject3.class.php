<?php
class TestObject3 {
	public $paramString = null;
	
	public $testObject2 = null;
	
	public $blub = null;
	
	public $blubberParam;
	
	public $blubber2Param;
	
	private $bla;
	
	private $myarray;
	
	public $paramString2 = null;
	
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
	
	public function setBla($p) {
		$this->bla = $p;
	}
	
	public function getBla() {
		return $this->bla;
	}
	
	public function setParamString2($p) {
		$this->blubber2Param = $p;
	}
	
	public function setMyarray($p) {
		$this->myarray = $p;
	}
	
	public function getMyarray() {
		return $this->myarray;
	}
}
?>