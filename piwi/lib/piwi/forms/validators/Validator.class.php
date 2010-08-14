<?php
/**
 * Abstract class that validators have to inherit from.
 */
abstract class Validator {
	/** The DOMElement specifying the Validator. */
	protected $validatorXML;
	
	/**
	 * Constructor.
	 * @param DOMElement $validatorXML The DOMElement specifying the Validator.
	 */
	public function __construct(DOMElement $validatorXML) {
		$this->validatorXML = $validatorXML;
	}
	
	/**
	 * Evaluates the Validator.
	 * @return string Returns an errormessage if validation fails otherwise true.
	 */
	public function validate() {
		$fieldToValidate = BeanFactory :: getBeanById('formProcessor')->getId() . $this->validatorXML->getAttribute("fieldToValidate");
		
		if ($this->isValid($fieldToValidate)) {
			return null;
		} else {
			return $this->validatorXML->getAttribute("message");
		}
	}
	
	/**
	 * Executes the Validator.
	 * @param string $fieldName The name of the field which should be validated.
	 * @return boolean Returns true if validation is successful otherwise false.
	 */
	protected abstract function isValid($fieldName);
}
?>