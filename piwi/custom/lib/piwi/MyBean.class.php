<?php
/**
 * Test bean
 */
class MyBean {
	private $configuration = null;
	
	/**
	 * Setter for the configuration
	 * @param unknown_type $configuration
	 * @return unknown_type
	 */
	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
	}
	
	/**
	 * Getter for the configuration
	 * @return the configuration
	 */
	public function getConfiguration() {
		return $this->configuration;
	}
}
?>