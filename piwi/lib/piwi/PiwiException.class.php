<?php

class PiwiException extends Exception {
	
	const ERR_NO_XML_DEFINITION    = 1001;
	const ERR_PARAM_NOT_EXPECTED   = 1002;
    
    public function __construct($message ="An exception occured", $code = 1) {
        parent::__construct($message, $code);
    }

/*
 * 
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
    */
}

?>