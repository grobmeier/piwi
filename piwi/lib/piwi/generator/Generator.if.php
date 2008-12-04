<?php
/**
 * Interface that Generators have to implement.
 */
interface Generator {
	/**
	 * Returns the xml output of the Generator.
	 * @return string The xml output as string.
	 */
	public function generate();
	
	/**
	 * Used to pass parameters to the Generator.
	 * @param string $key The name of the parameter.
	 * @param string $value The value of the parameter.
	 */
	public function setProperty($key, $value);
}
?>