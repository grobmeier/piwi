<?php
/**
 * Generates an errorpage.
 * Can be used to display a well formated message when an error occurs.
 */
final class ExceptionPageGenerator extends SiteGenerator {
	/** The exception that should be displayed. */
   	private $exception = null;
   	
   	/**
   	 * Constructor.
   	 * @param exception $exception The exception that should be displayed.
   	 */
    public function __construct(Exception $exception) {
    	$this->exception = $exception;
    }
   
	/**
	 * Generates the sections that will be placed as content.
	 * @return string The xml output as string.
	 */
    protected function generateSections() {
		$piwixml = "<header>Error</header>";
		$piwixml .= "<section>";
		$piwixml .= $this->exception->getMessage();
		$piwixml .= "</section>";
		return $piwixml;
	}
	
	/** Used to pass parameters to the Generator.
	 * @param string $key The name of the parameter.
	 * @param string $value The value of the parameter.
	 */
    public function setProperty($key, $value) {
    }
}
?>