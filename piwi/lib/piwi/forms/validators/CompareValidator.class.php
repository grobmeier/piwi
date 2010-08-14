<?php
/**
 * Checks if the value of a field matches the value of another field.
 */
class CompareValidator extends Validator {
	/**
	 * Executes the Validator.
	 * @param string $fieldName The name of the field which should be validated.
	 * @return boolean Returns true if validation is successful otherwise false.
	 */
	protected function isValid($fieldName) {
		$fieldNameToCompare = BeanFactory :: getBeanById('formProcessor')->getId() . $this->validatorXML->getAttribute("fieldNameToCompare");
		
		if (!isset($_POST[$fieldName]) || !isset($_POST[$fieldNameToCompare])) {
			return false;
		} else {
			return $_POST[$fieldName] == $_POST[$fieldNameToCompare];
		}
	}
}
?>