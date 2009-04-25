<?php
/**
 * Used to retrieve Forms.
 */
class FormFactory {
	/** Map of the forms that have already been initialized. */
	private $forms = array ();

	/** Path of the file containing the xml-definition of the forms that can be used. */
	private $formsXMLPath = null;

	/** The forms definition as xml. */
	private $xml = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Returns the Form with the given id as DOMXPath.
	 * If the Form is already loaded, the loaded instance will be returned.
	 * Otherwise a the form will be loaded and stored in a map.
	 * @param string $formId The id of the Form.
	 * @return DOMXPath The Form with the given id as DOMXPath.
	 */
	public function getFormById($formId) {
		$this->_initializeForm($formId);
		
		return $this->forms[$formId];
	}
	
	/**
	 * Sets the path of the file containing the xml-definition of the forms that can be used.
	 * @param string $formsXMLPath Path of the file containing the xml-definition of the forms that can be used.
	 */
	public function setFormsXMLPath($formsXMLPath) {
		$this->formsXMLPath = $formsXMLPath;
	}
	
	/**
	 * Constructs an DOMXPath of the From with the given id.
	 * @param string $formId The id of the From.
	 */
	private function _initializeForm($formId) {
		if (isset($this->forms[$formId])) {
			return;
		}
		if ($this->xml == null) {
			if (!file_exists($this->formsXMLPath)) {
				throw new PiwiException("Could not find the forms definition file (Path: '"
						. $this->formsXMLPath . "').", 
					PiwiException :: ERR_NO_XML_DEFINITION);
			}
			$this->xml = simplexml_load_file($this->formsXMLPath);
			$this->xml->registerXPathNamespace('forms', 'http://piwi.googlecode.com/xsd/forms');
		}

		$result = $this->xml->xpath("/forms:forms/forms:language[@region='" . 
			UserSessionManager::getUserLanguage() . "']//forms:form[@id='" . $formId . "']");
		
		if ($result != null) {			
			$domXPath = new DOMXPath(DOMDocument::load((string)$result[0]->attributes()->href));
			$domXPath->registerNamespace('piwiform', 'http://piwi.googlecode.com/xsd/piwiform');
			
			$this->forms[$formId] = $domXPath;
		} else {
			throw new PiwiException("Could not find the form '" . $formId .
					"' in the forms definition file (Path: '" . $this->formsXMLPath . "').", 
				PiwiException :: ERR_NO_XML_DEFINITION);
		}
	}
}
?>