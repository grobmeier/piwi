<?php
/**
 * Generates test section.
 */
class TestGenerator implements SectionGenerator {
   	/**
   	 * Constructor.
   	 */
    public function __construct() {
    }
   
	/**
	 * Generates the sections that will be placed as content.
	 * @return string The xml output as string.
	 */
    public function generate() {
		return '<section>Test</section>';
	}
	
	/** Used to pass parameters to the Generator.
	 * @param string $key The name of the parameter.
	 * @param object $value The value of the parameter.
	 */
    public function setProperty($key, $value) {

    }
}
?>