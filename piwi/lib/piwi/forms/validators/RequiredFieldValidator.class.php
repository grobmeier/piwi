<?php
/**
 * Checks if the value of a field is not empty.
 */
class RequiredFieldValidator extends Validator {
	/**
	 * Executes the Validator.
	 * @param string $fieldName The name of the field which should be validated.
	 * @return boolean Returns true if validation is successful otherwise false.
	 */
	protected function isValid($fieldName) {
		if (!isset($_POST[$fieldName]) || $_POST[$fieldName] == '') {
			return false;
		} else {
			return $_POST[$fieldName] != $this->validatorXML->getAttribute("initialValue");
		}
	}
}
?>