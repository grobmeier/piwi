<?php
/**
 * Generates a complete page.
 * Which means, that, in contrast to classes implementing the Generator interface, 
 * not only sections are generated.
 */
abstract class SiteGenerator implements Generator {	   	
   	/**
   	 * Constructor.
   	 */
    public function __construct() {
    }
   
   	/**
	 * Returns the xml output of the Generator.
	 * @return string The xml output as string.
	 */
    public function generate() {
		$piwixml = '<document xmlns="http://piwi.googlecode.com/xsd/piwixml" 
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
xsi:schemaLocation="http://piwi.googlecode.com/xsd/piwixml ../../../resources/xsd/piwixml.xsd">';
		$piwixml .= "<content position='article'>";
		
		$piwixml .= $this->generateSections();

		$piwixml .= '</content>';
		$piwixml .= '</document>';
		return $piwixml;
	}
	
	/**
	 * Used to pass parameters to the Generator.
	 * @param string $key The name of the parameter.
	 * @param object $value The value of the parameter.
	 */
    public function setProperty($key, $value){    	
    }
    
	/**
	 * ---------------------------------------------------------------------
	 * >>>>>>>>>>>>>>>>>>>>>>>>>> Abstract Methods <<<<<<<<<<<<<<<<<<<<<<<<<
	 * ---------------------------------------------------------------------
	 */ 
	 
	/**
	 * Generates the sections that will be placed as content.
	 * @return string The xml output as string.
	 */
	abstract protected function generateSections();
}
?>