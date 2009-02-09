<?php
/**
 * Checks if he value of a field matches the specified regular expression.
 */
class RegularExpressionValidator extends Validator {
	/**
	 * Executes the Validator.
	 * @param string $fieldName The name of the field which should be validated.
	 * @return boolean Returns true if validation is successful otherwise false.
	 */
	protected function isValid($fieldName) {
		if (!isset($_POST[$fieldName])) {
			return false;
		} else {
			return preg_match($this->validatorXML->getAttribute("pattern"), $_POST[$fieldName]);
		}
	}
}
?>