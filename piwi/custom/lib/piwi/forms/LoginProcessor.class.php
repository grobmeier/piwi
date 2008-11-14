<?php
/**
 * Custom Step Processors used within ValidationForm.
 */
class LoginProcessor  implements StepProcessor{
	/**
	 * Performs a custom action within a form using the results of the form.
	 * @param array $values The values of the form.
	 * @param array $files The files that have been posted.
	 * @return string The desired result as PiwiXML
	 */
	public function process(array $values, array $files) {
		// Login the user
		$result = '';
		if ($values["CURRENT_STEPS"] == $values["NUMBER_OF_STEPS"]) {
			if (SessionManager::loginUser($values['Name'], $values['Password'], isset($values['Cookies']), 3600 * 24 * 7)) {
				$result .= '<label key="LOGIN_SUCCESS" />';
			} else {
				$result .= '<label key="LOGIN_FAILED" />';
			}		
		}
		return '<div>' . $result . '</div>';
	}
}
?>