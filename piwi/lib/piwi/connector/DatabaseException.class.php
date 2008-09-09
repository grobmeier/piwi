<?php

class DatabaseException extends Exception {
    
    const ERR_CONNECTION_FAILED  = 1001;
	const ERR_NO_FILENAME_SPECIFIED = 1002;
	const ERR_QUERY_FAILED = 1003;
	
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n" . $this->getTraceAsString();
    }
}
?>