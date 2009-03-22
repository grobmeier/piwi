<?php
class TestObject2 {
	public $paramString = null;
	
	public $paramBoolean = null;
	
	public $paramInteger = null;
	
	public $paramFloat = null;
	
	public function __construct($paramString, $paramBoolean, $paramInteger, $paramFloat) {
		$this->paramString = $paramString;
		$this->paramBoolean = $paramBoolean;
		$this->paramInteger = $paramInteger;
		$this->paramFloat = $paramFloat;
	}
}
?>