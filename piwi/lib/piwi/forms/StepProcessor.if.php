<?php
/**
 * Interface that Step Processors have to implement.
 * A Step Processor can be executed within a step in a form.
 */
interface StepProcessor {
	/**
	 * Performs a custom action within a form using the results of the form.
	 * @param array $values The values of the form.
	 * @param array $files The files that have been posted.
	 * @param integer $currentStep The current step within the form.
	 * @param integer $numberOfSteps The total number of steps within the form.
	 * @return string The desired result as PiwiXML
	 */
	public function process(array $values, array $files, $currentStep, $numberOfSteps);
}
?>