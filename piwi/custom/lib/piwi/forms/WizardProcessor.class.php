<?php
/**
 * Custom Step Processors used within ValidationForm.
 */
class WizardProcessor implements StepProcessor{
	/**
	 * Performs a custom action within a form using the results of the form.
	 * @param array $values The values of the form.
	 * @param array $files The files that have been posted.
	 * @return string The desired result as PiwiXML
	 */
	public function process(array $values, array $files) {
		// Show current step
		$result = '<span class="bold">Step ' . $values["CURRENT_STEPS"] . ' / ' .  $values["NUMBER_OF_STEPS"] . '</span><br /><br />';
		
		// Show results if last step is reached
		if ($values["CURRENT_STEPS"] == $values["NUMBER_OF_STEPS"]) {
			$result .= '<label key="NAME" />: ' . $values['Name'] . '<br />';
			$result .= '<label key="BIRTHDAY" />: ' . $values['Birthday'] . '<br />';
			$result .= '<label key="COMMENT" />: ' . $values['Comment'];
		}
		return '<div>' . $result . '</div>';
	}
}
?>