<?php
/**
 * Custom Step Processors used within WizardForm.
 */
class ValidationProcessor implements StepProcessor{
	/**
	 * Performs a custom action within a form using the results of the form.
	 * @param array $values The values of the form.
	 * @param array $files The files that have been posted.
	 * @param integer $currentStep The current step within the form.
	 * @param integer $numberOfSteps The total number of steps within the form.
	 * @return string The desired result as PiwiXML
	 */
	public function process(array $values, array $files, $currentStep, $numberOfSteps) {
		$result = '<label key="GENDER" />: ' . $values['Gender'] . '<br />';
		$result .= '<label key="NAME" />: ' . $values['Name'] . '<br />';
		$result .= '<label key="EMAIL" />: ' . $values['Email'] . '<br />';
		$result .= '<label key="NUMBER" />: ' . $values['Number'] . '<br />';
		$result .= '<label key="NEWSLETTER" />: ' . ((isset($values['Newsletter'])) ? $values['Newsletter'] : '-');
		return '<div>' . $result . '</div>';
	}
}
?>