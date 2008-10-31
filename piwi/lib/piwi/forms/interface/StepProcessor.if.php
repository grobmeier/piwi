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
	 * @return string The desired result as PiwiXML
	 */
	public function process(array $values, array $files);
}
?>