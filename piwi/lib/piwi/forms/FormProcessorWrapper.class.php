<?php
/**
 * Wrappes the FormProcessor to make it accessible as a bean.
 * Used by the XSLT stylesheets which need static function to work
 */
class FormProcessorWrapper {
	/**
	 * Loads the form from the given file and generates the the content as PiwiXML.
	 * @param string $id The id of the form.
	 * @return DOMDocument The rendered form as PiwiXML.
	 */
	public static function process($id) {
		return BeanFactory :: getBeanById('formProcessor')->process($id);
	}

	/**
	 * Converts the given INPUT element to PiwiXML.
	 * @param array $domElement An array containing the DOMElement to convert.
	 * @return DOMDocument The PiwiXML of the given DOMElement as DOMDocument.
	 */
	public static function generateInput(array $domElement) {
		return BeanFactory :: getBeanById('formProcessor')->generateInput($domElement);
	}

	/**
	 * Converts the given SELECT element to PiwiXML.
	 * @param array $domElement An array containing the DOMElement to convert.
	 * @return DOMDocument The PiwiXML of the given DOMElement as DOMDocument.
	 */
	public static function generateSelect(array $domElement) {
		return BeanFactory :: getBeanById('formProcessor')->generateSelect($domElement);
	}
	
	/**
	 * Converts the given TEXTAREA element to PiwiXML.
	 * @param array $domElement An array containing the DOMElement to convert.
	 * @return DOMDocument The PiwiXML of the given DOMElement as DOMDocument.
	 */
	public static function generateTextArea(array $domElement) {
		return BeanFactory :: getBeanById('formProcessor')->generateTextArea($domElement);
	}

	/**
	 * Executes the given Validator.
	 * @param array $domElement An array containing the DOMElement to convert.
	 * @return DOMDocument The PiwiXML of the given DOMElement as DOMDocument.
	 */
	public static function executeValidator(array $domElement) {		
		return BeanFactory :: getBeanById('formProcessor')->executeValidator($domElement);
	}	
	
	/**
	 * Executes the given StepProcessor.
	 * @param array $domElement An array containing the DOMElement to convert.
	 * @return DOMDocument The PiwiXML of the given DOMElement as DOMDocument.
	 */
	public static function executeStepProcessor(array $domElement) {
		return BeanFactory :: getBeanById('formProcessor')->executeStepProcessor($domElement);
	}	
}
?>