<?php
/**
 * Class for checking url parameters
 */
class ParameterValidator {
	
	const TYPE_AZ_STRING 	= 1;
	const TYPE_AZ09_STRING 	= 2;
	const TYPE_FILENAME 	= 3;
	
	/**
	 * Checks if a parameter matches the expected type 
	 * @param unknown_type $param the parameter to check
	 * @param unknown_type $type the expected type of the parameter 
	 * @return boolean false if the parameter does not match the expected type, true otherwise.
	 */
	public function check($param, $type) {
		switch($type) {
			case self::TYPE_AZ_STRING: 
				if ( preg_match('/[^A-Za-z]/', $param) == 1 ) {
					return false;
				} else {
					return true;
				}
				break;
			case self::TYPE_AZ09_STRING: 
				if ( preg_match('/[^0-9A-Za-z]/', $param) == 1 ) {
					return false;
				} else {
					return true;
				}
				break;
			case self::TYPE_FILENAME:
				if ( preg_match('/[^0-9A-Za-z\_]/', $param) == 1 ) {
					return false;
				} else {
					return true;
				}
				break;
				
		}
		return false;
	}
}
?>