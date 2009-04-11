<?php
class TestObject1 {
	private $fasel = null;
	public function __construct() {
	}
	
	public function setFasel($_fasel) {
		$this->fasel = $_fasel;
	}
	
	public function getFasel() {
		return $this->fasel;
	}
}
?>