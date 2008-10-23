<?php
/**
 * Custom Step Processors used within WizardForm.
 */
class ValidationProcessor implements StepProcessor{
	/**
	 * Performs a custom action within a form using the results of the form.
	 * @param array $values The values of the form.
	 * @return string The desired result as PiwiXML
	 */
	public function process(array $values) {
		$result = "Name: " . $values['Name'] . '<br />';
		$result .= "Email: " . $values['Email'] . '<br />';
		$result .= "Number: " . $values['Number'];
		return $result;
	}
}
?>