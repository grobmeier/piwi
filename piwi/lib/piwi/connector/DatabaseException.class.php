<?php
/**
 * Exception that is thrown, when access to a database fails.
 */
class DatabaseException extends Exception {    
    /** Errorcode ERR_CONNECTION_FAILED. */
    const ERR_CONNECTION_FAILED  = 1001;
    
    /** Errorcode ERR_NO_FILENAME_SPECIFIED. */
	const ERR_NO_DATABASE_SPECIFIED = 1002;
	
	/** Errorcode ERR_QUERY_FAILED. */
	const ERR_QUERY_FAILED = 1003;
	
	/**
	 * Constructor.
	 * @param string $message The error message.
	 * @param string $code The errorcode (optional).
	 */
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }

	/**
	 * Returns the exception as string.
	 * @return string The exception as string.
	 */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n" . $this->getTraceAsString();
    }
}
?>