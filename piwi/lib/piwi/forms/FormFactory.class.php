<?php
/**
 * Used to retrieve Forms.
 */
class FormFactory {
	/** Singleton instance of the FormsFactory. */
	private static $formsFactoryInstance = null;

	/** Map of the forms that have already been initialized. */
	private $forms = array ();

	/** Path of the file containing the xml-definition of the forms that can be used. */
	private $formsXMLPath = null;

	/** The forms definition as xml. */
	private $xml = null;

	/**
	 * Constructor.
	 * Private constructor since only used by 'initialize'.
	 * @param string $formsXMLPath Path of the file containing the xml-definition of the forms that can be used.
	 */
	private function __construct($formsXMLPath) {
		$this->formsXMLPath = $formsXMLPath;
	}

	/**
	 * Initializes the singleton instance of this Class.
	 * @param string $formsXMLPath Path of the file containing the xml-definition of the forms that can be used.
	 */
	public static function initialize($formsXMLPath) {
		if (self :: $formsFactoryInstance == null) {
			self :: $formsFactoryInstance = new FormFactory($formsXMLPath);
		}
	}

	/**
	 * Returns the Form with the given id as DOMXPath.
	 * If the Form is already loaded, the loaded instance will be returned.
	 * Otherwise a the form will be loaded and stored in a map.
	 * @param string $formId The id of the Form.
	 * @return DOMXPath The Form with the given id as DOMXPath.
	 */
	public static function getFormById($formId) {
		if (self :: $formsFactoryInstance == null) {
			throw new PiwiException(
				"Illegal State: Invoke static method 'initialize' on '" . __CLASS__ . "' before accessing a Form.", 
				PiwiException :: ERR_ILLEGAL_STATE);
		}

		self :: $formsFactoryInstance->initializeForm($formId);
		
		return self :: $formsFactoryInstance->forms[$formId];
	}
	
	/**
	 * Constructs an DOMXPath of the From with the given id.
	 * @param string $formId The id of the From.
	 */
	private function initializeForm($formId) {
		if (isset($this->forms[$formId])) {
			return;
		}
		if ($this->xml == null) {
			if (!file_exists($this->formsXMLPath)) {
				throw new PiwiException(
					"Could not find the forms definition file (Path: '" . $this->formsXMLPath . "').", 
					PiwiException :: ERR_NO_XML_DEFINITION);
			}
			$this->xml = simplexml_load_file($this->formsXMLPath);
			$this->xml->registerXPathNamespace('forms', 'http://piwi.googlecode.com/xsd/forms');
		}

		$result = $this->xml->xpath("/forms:forms/forms:language[@region='" . SessionManager::getUserLanguage() . "']//forms:form[@id='" . $formId . "']");
		
		if ($result != null) {			
			$domXPath = new DOMXPath(DOMDocument::load((string)$result[0]->attributes()->path));
			$domXPath->registerNamespace('piwiform', 'http://piwi.googlecode.com/xsd/piwiform');
			
			$this->forms[$formId] = $domXPath;
		} else {
			throw new PiwiException(
				"Could not find the form '" . $formId . "' in the forms definition file (Path: '" . $this->formsXMLPath . "').", 
				PiwiException :: ERR_NO_XML_DEFINITION);
		}
	}
}
?>