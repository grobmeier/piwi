<?php
/**
 * Custom Step Processors used within Login Form.
 */
class LoginProcessor implements StepProcessor{
	/**
	 * Performs a custom action within a form using the results of the form.
	 * @param array $values The values of the form.
	 * @param array $files The files that have been posted.
	 * @param integer $currentStep The current step within the form.
	 * @param integer $numberOfSteps The total number of steps within the form.
	 * @return string The desired result as PiwiXML
	 */
	public function process(array $values, array $files, $currentStep, $numberOfSteps) {
		// Login the user
		$result = '';
		if ($currentStep == $numberOfSteps) {
			if (UserSessionManager::loginUser($values['Name'], $values['Password'], isset($values['Cookies']), 3600 * 24 * 7)) {
				$result .= '<label key="LOGIN_SUCCESS" />';
			} else {
				$result .= '<label key="LOGIN_FAILED" />';
			}		
		}
		return '<div>' . $result . '</div>';
	}
}
?>