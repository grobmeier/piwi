<?php
class PrefillingPreprocessorLoginForm implements Preprocessor{
	private $logger = null;
	
	public function __construct() {
		$this->logger = Logger::getLogger('PrefillingPreprocessorLoginForm.class.php');
	}
	
    public function process() {
    	$this->logger->debug("Preprocessor called");
    }
}
?>