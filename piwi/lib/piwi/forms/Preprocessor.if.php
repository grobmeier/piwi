<?php
/**
 * Interface that Preprocessors have to implement.
 */
interface Preprocessor {
	/**
	 * Processes an action before a form step will be done.
	 */
	public function process();
}
?>
