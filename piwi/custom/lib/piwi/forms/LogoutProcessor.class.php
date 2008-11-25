<?php
/**
 * Custom Step Processors used within Logout Form.
 */
class LogoutProcessor  implements StepProcessor{
	/**
	 * Performs a custom action within a form using the results of the form.
	 * @param array $values The values of the form.
	 * @param array $files The files that have been posted.
	 * @return string The desired result as PiwiXML
	 */
	public function process(array $values, array $files) {
		// Logout the user
		$result = '';
		if ($values["CURRENT_STEPS"] == $values["NUMBER_OF_STEPS"]) {
			SessionManager::logoutUser();
			$result .= '<label key="LOGOUT_SUCCESS" />';	
		}
		return '<div>' . $result . '</div>';
	}
}
?>