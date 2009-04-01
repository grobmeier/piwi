<?php
/**
 * Custom Step Processors used within ValidationForm.
 */
class WizardProcessor implements StepProcessor{
	/**
	 * Performs a custom action within a form using the results of the form.
	 * @param array $values The values of the form.
	 * @param array $files The files that have been posted.
	 * @param integer $currentStep The current step within the form.
	 * @param integer $numberOfSteps The total number of steps within the form.
	 * @return string The desired result as PiwiXML
	 */
	public function process(array $values, array $files, $currentStep, $numberOfSteps) {
		// Show current step
		$result = '<span class="bold">Step ' . $currentStep . ' / ' .  $numberOfSteps . '</span><br /><br />';
		
		// Show results if last step is reached
		if ($currentStep == $numberOfSteps) {
			$result .= '<label key="NAME" />: ' . $values['Name'] . '<br />';
			$result .= '<label key="BIRTHDAY" />: ' . $values['Birthday'] . '<br />';
			$result .= '<label key="COMMENT" />: ' . $values['Comment'];
		}
		return '';
	}
}
?>