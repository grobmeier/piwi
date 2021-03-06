<?php
/**
 * Exception that is thrown, when something goes wrong during 
 * processing the requested page.
 */
class PiwiException extends Exception {	
	/** Errorcode ERR_NO_XML_DEFINITION. */
	const ERR_NO_XML_DEFINITION		= 1001;
	
	/** Errorcode ERR_PARAM_NOT_EXPECTED. */
	const ERR_PARAM_NOT_EXPECTED	= 1002;
	
	/** Errorcode ERR_ILLEGAL_STATE. */
    const ERR_ILLEGAL_STATE			= 1003;
    
    /** Errorcode ERR_WRONG_TYPE. */
    const ERR_WRONG_TYPE			= 1004;
    
    /** Errorcode ERR_404. */
    const ERR_404					= 1005;
    
    /** Errorcode INVALID_XML_DEFINITION. */
	const INVALID_XML_DEFINITION	= 1006;

    /** Errorcode FORMS_ERROR. */
	const FORMS_ERROR				= 1007;

    /** Errorcode PERMISSION_DENIED. */
	const PERMISSION_DENIED			= 1008;

	/** Errorcode INVALID_XML_DEFINITION. */
	const XML_ID_NOT_UNIQUE			= 1009;
	
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