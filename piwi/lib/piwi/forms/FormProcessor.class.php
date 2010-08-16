<?php
/**
 * Processes forms specified as PiwiForms.
 * Forms can contain multiple steps (wizards).
 * The values entered by a user can be verified by using Validators.
 * It is also possible to handle multiple forms on one page.
 */
class FormProcessor {
	private $logger = null;
	
	/** The id of the currently processed form. */
	private $formId = null;
	
	/** Indicates that a validator of the current form failed. */
	private $validationFailed = false;
	
	/** The current step of the currently processed form. */
	private $currentStep = 0;
	
	/** The total number of steps of the currently processed form. */
	private $numberOfSteps = 0;
	
	/** True if validation has to be performed, otherwise false. */
	private $validate = true;
	
	/** Array of the names of all fields in the current form that should be rejected from hidden fields. */
	private $ignoredFields;
	
	/** The XSLTProcessor. */
	private $processor = null;
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->logger = Logger::getLogger('FormProcessor.class.php');
	}
	
	/**
	 * Loads the form from the given file and generates the the content as PiwiXML.
	 * @param string $id The id of the form.
	 * @return DOMDocument The rendered form as PiwiXML.
	 */
	public function process($id) {
		$this->logger->debug('Processing form with ID: ' . $id);
		// Give every form an unique id
		$this->formId = $id;
		
		// Reset variables
		$this->validationFailed = false;
		$this->validate = true;
		$this->ignoredFields = array();
		
		$domXPath = BeanFactory :: getBeanById('formFactory')->getFormById($id);
	
		// Determine the current step in the formular
		// if request is a postback increase number of steps otherwise begin with step 1
		$this->currentStep = 0;
		
		$this->_initRequestParams();
		
		if (isset($_POST[$this->formId . '_currentstep'])) {
			$this->currentStep = $_POST[$this->formId . '_currentstep'];
		}
		
		// Check if last step was already a postbackstep
		$isPostbackStep = $domXPath->query('/piwiform:form/piwiform:step[' . $this->currentStep . ']' .
					'[@postback="1"]')->length == 1;

		if ($isPostbackStep) {
			$this->logger->debug('Form with ID: ' . $id . ' is a postbackform.');
			
			$this->logger->debug('Calling Preprocessor ' . $id);
			$this->_callPreProcessor($domXPath);
		}
			
		// Determinate number of total steps in form
		$this->numberOfSteps = $domXPath->evaluate('count(/piwiform:form/piwiform:step)');
		
		if ($this->currentStep > $this->numberOfSteps || $this->currentStep < 0) {
			throw new PiwiException("This form only has " . $numberOfSteps .
					" steps, you requested number " . $this->currentStep . ".", 
				PiwiException :: FORMS_ERROR);
		}
		
		// Validate formdata of last step
		if ($this->currentStep > 0) {
			$stepXML = $this->_getStepXML($domXPath);
		}
		
		// If validation was successful and step is not a postback show next step
		if (!$this->validationFailed && !$isPostbackStep) {
			// evaluate next step if it is not a postbackstep
			$this->currentStep++;
			
			$this->logger->debug('Calling Preprocessor ' . $id);
			$this->_callPreProcessor($domXPath);
			
			$this->validate = false;
			$stepXML = $this->_getStepXML($domXPath);
			
			// Check if current step is a postbackstep
			$isPostbackStep = $domXPath->query('/piwiform:form/piwiform:step[' . $this->currentStep . ']' .
					'[@postback="1"]')->length == 1;
		}

		// Build xml
		$piwixml = '<form xmlns="http://piwi.googlecode.com/xsd/piwixml" action="' . 
			Request::getPageId() . '.' . Request::getExtension() .
			'" method="post" enctype="multipart/form-data">';
		$piwixml .= '<input name="' . $this->formId . '_currentstep" type="hidden" value="' .
			$this->currentStep . '" />';
		
		foreach ($_POST as $key => $value) {
			if ($this->_isKeyAllowedAsHiddenField($key)) {
				if (is_array($value)) {
					foreach ($value as $var) {
       					$piwixml .= '<input name="' . $key . '[]" type="hidden" value="' . $var . '" />';
					}					
				} else { 
					$piwixml .= '<input name="' . $key . '" type="hidden" value="' . $value . '" />';
				}
			}
		}
		
		// add step
		$piwixml .= $stepXML . '</form>';

		$doc = new DOMDocument;
		$doc->loadXml($piwixml);
		$this->logger->debug('Form with ID: ' . $id . ' has sucessfully processed.');
		return $doc;
	}
	
	/**
	 * Determines whether the given POST parameter should be included as a hidden field.
	 * @param string $key The key of the POST parameter.
	 * @return boolean True if key should be added as hidden field otherwise false.
	 */
	private function _isKeyAllowedAsHiddenField($key) {
		if ($key == $this->formId . '_' . 'currentstep') {
			return false;
		}
		
		if (!isset($this->ignoredFields[$key])) {
			return true;
		} else {
			return !$this->validationFailed;
		}
	}
	
	/**
	 * Initializes the request parameters.
	 * 
	 * There is an issue with htmlspecialchars.
	 * Not each serializer does expect htmlspecialchars.
	 */
	private function _initRequestParams() {
		// Replace \ within the $_POST if the magic_qutes_gpc is set
		if (ini_get('magic_quotes_gpc')) {
		    foreach ($_POST as $key => $value) {
		 		if (!is_array($value)) {
					$_POST[$key] = stripslashes($value);
				}
		 	}
		}

		// Replace special characters within the $_POST
		foreach ($_POST as $key => $value) {
			if (!is_array($value)) {
				$_POST[$key] = htmlspecialchars($value);
			}
		}
	}
	
	/**
	 * Initializes the XSLTProcessor, if this hasn't happened before'
	 */
	private function _initXSLTProcessor() {
		if ($this->processor == null) {
			$this->processor = new XSLTProcessor;
			$this->processor->registerPHPFunctions();
			$this->processor->importStyleSheet(DOMDocument::load("resources/xslt/FormTransformation.xsl"));
		}
	}
	
	/**
	 * Processes the current step of the form and returns it as XML.
	 * @param DOMXPath $domXPath The form where the step should be retrieved from.
	 * @return string The current Step as XML.
	 */
	private function _getStepXML(DOMXPath $domXPath) {
		$this->logger->debug('Processing step and returning result as XML');
		$this->_initXSLTProcessor();
		
		$stepXML = $domXPath->query('/piwiform:form/piwiform:step[' . $this->currentStep . ']')->item(0);
		$template = DOMDocument::loadXML($stepXML->ownerDocument->saveXML($stepXML));
			
		return $this->processor->transformToXML($template);
	}
	
	/**
	 * Calls a preprocessor defined as an attribute in the step tag.
	 * 
	 */
	private function _callPreProcessor(DOMXPath $domXPath) {
		$this->_initXSLTProcessor();
		$form = $domXPath->query('/piwiform:form/piwiform:step[' . $this->currentStep . ']');
		$result = $form->item(0)->getAttribute('preprocessor');
		
		$this->logger->debug("Found Preprocessor: " . $result);
		
		if ($result != null) {
			$class = new ReflectionClass((string)$result);
			$preprocessor = $class->newInstance();
			
			if (!$preprocessor instanceof Preprocessor) {
				throw new PiwiException("The Class with id '" . $result .
						"' is not an instance of Preprocessor.", 
					PiwiException :: ERR_WRONG_TYPE);
			}
			$preprocessor->process();
			$this->logger->debug("Preprocessor: " . $result . " has been called.");
			
		}
		$this->logger->debug("Preprocessor actions finished.");
	}
	
	/**
	 * Converts the given INPUT element to PiwiXML.
	 * @param array $domElement An array containing the DOMElement to convert.
	 * @return DOMDocument The PiwiXML of the given DOMElement as DOMDocument.
	 */
	public function generateInput(array $domElement) {
		// Remove '[]' from field name, to handle arrays correctly
		$name = str_replace("[]", "", $this->formId . '_' . $domElement[0]->getAttribute("name"));
		
		$value = $domElement[0]->getAttribute("value");
		$checked = $domElement[0]->getAttribute("checked");
		
		$checkable = $domElement[0]->getAttribute("type") == 'radio' || 
			$domElement[0]->getAttribute("type") == 'checkbox';
		
		/* if INPUT is a CheckBox or RadioButton and if request is a postback, 
		 * then determinate it's checked state to set the same status again */
		if ($checkable && sizeof($_POST) > 0) {
			if (isset($_POST[$name])) {
				if (is_array($_POST[$name])) {
					$checked = '';
					foreach ($_POST[$name] as $var) {
						if ($value == $var) {
							$checked = 'checked';
							break;
						}
					}			
				} else if ($value == $_POST[$name]) {
					$checked = 'checked';
				}
			} else {
				$checked = '';
			}			
		}
		
		// if INPUT is a CheckBox add it to the array to reject it from the hidden field list
		if ($domElement[0]->getAttribute("type") == 'checkbox') {
			$this->ignoredFields[$name] = $domElement[0]->getAttribute("name");
		}		
		
		// if INPUT is a normal TextField and if request is a postback, then set the value to the entered one
		if (!$checkable && isset($_POST[$name])) {
			$value = $_POST[$name];
		} 

		$xml = ' <input name="' . $this->formId . '_' . 
			$domElement[0]->getAttribute("name") . '"' . 
			($domElement[0]->hasAttribute("type") ? ' type="' . $domElement[0]->getAttribute("type") 
				. '" ' : 'type="text" ') 
			. $this->_getFilteredAttributesAsString($domElement[0], array ('name', 'type', 'checked', 'value'))
			. (($checked != '') ? ' checked="' . $checked . '" ' : '')
			. ' value="' . $value . '"'		
			. ' />';
					
		$doc = new DOMDocument;
		$doc->loadXml($xml);
		return $doc;
	}

	/**
	 * Converts the given SELECT element to PiwiXML.
	 * @param array $domElement An array containing the DOMElement to convert.
	 * @return DOMDocument The PiwiXML of the given DOMElement as DOMDocument.
	 */
	public function generateSelect(array $domElement) {
		// Remove '[]' from field name, to handle arrays correctly
		$name = str_replace("[]", "", $this->formId . '_' . $domElement[0]->getAttribute("name"));
		
		$xml = ' <select name="' . $this->formId . '_' . $domElement[0]->getAttribute("name") . '"'
			. $this->_getFilteredAttributesAsString($domElement[0], array ('name'))
			. '>';

		$this->ignoredFields[$name] = $domElement[0]->getAttribute("name");

		foreach ($domElement[0]->childNodes as $option) {
       		if ($option->nodeName == 'option') {
       			$selected = $option->getAttribute("selected");
   				$value = $option->getAttribute("value");


				if ($value == '') {
					$value = $option->textContent;
				}

				//If request is a postback, then determinate it's selected state to set the same status again
				if (sizeof($_POST) > 0) {
					if (isset($_POST[$name])) {
						if (is_array($_POST[$name])) {
							$selected = '';
							foreach ($_POST[$name] as $var) {
								if ($value == $var) {
									$selected = 'selected';
									break;
								}
							}			
						} else if ($value == $_POST[$name]) {
							$selected = 'selected';
						}
					} else {
						$selected = '';
					}			
				}
				
       			$xml .= "<option"
	       			. (($selected != '') ? ' selected="' . $selected . '" ' : '')
					. $this->_getFilteredAttributesAsString($option, array ('selected'))
	       			. '>'
	       			. htmlspecialchars($option->textContent)
					. '</option>';
       		}
		}
	
		$xml .= '</select>';
					
		$doc = new DOMDocument;
		$doc->loadXml($xml);
		return $doc;
	}
	
	/**
	 * Converts the given TEXTAREA element to PiwiXML.
	 * @param array $domElement An array containing the DOMElement to convert.
	 * @return DOMDocument The PiwiXML of the given DOMElement as DOMDocument.
	 */
	public function generateTextArea(array $domElement) {
		$value = $domElement[0]->textContent;

		if (isset($_POST[$this->formId . '_' . $domElement[0]->getAttribute("name")])) {
			$value = $_POST[$this->formId . '_' . $domElement[0]->getAttribute("name")];
		} 

		$xml = ' <textarea name="' . $this->formId . '_' . $domElement[0]->getAttribute("name") . '"'
					. $this->_getFilteredAttributesAsString($domElement[0], array ('name', 'value'))
					. '>'
					. ($value == "" ? ' ' : htmlspecialchars($value))
					. '</textarea>';
					
		$doc = new DOMDocument;
		$doc->loadXml($xml);
		return $doc;
	}

	/**
	 * Executes the given Validator.
	 * @param array $domElement An array containing the DOMElement to convert.
	 * @return DOMDocument The PiwiXML of the given DOMElement as DOMDocument.
	 */
	public function executeValidator(array $domElement) {		
		if (!$this->validate) {
			return new DOMDocument();
		}
		
		// Create instance of Validator
		$class = new ReflectionClass($domElement[0]->getAttribute("class"));
		$validator = $class->newInstance($domElement[0]);

		if (!$validator instanceof Validator) {
			throw new PiwiException("The Class with id '" . $domElement[0]->getAttribute("class") .
					"' is not an instance of Validator.", 
				PiwiException :: ERR_WRONG_TYPE);
		}
		
		$errorMessage = $validator->validate();
		
		if ($errorMessage == null) {
			return new DOMDocument();
		} else {
			$errorMessage = '<span class="error"> ' . htmlspecialchars($errorMessage) . '</span>';
			$this->validationFailed = true;				
			$doc = new DOMDocument;
			$doc->loadXml($errorMessage);
			return $doc;
		}
	}	
	
	/**
	 * Executes the given StepProcessor.
	 * @param array $domElement An array containing the DOMElement to convert.
	 * @return DOMDocument The PiwiXML of the given DOMElement as DOMDocument.
	 */
	public function executeStepProcessor(array $domElement) {
		// Create instance of StepProcessor
		$class = new ReflectionClass($domElement[0]->getAttribute("class"));
		$stepProcessor = $class->newInstance();

		if (!$stepProcessor instanceof StepProcessor) {
			throw new PiwiException("The Class with id '" . $domElement[0]->getAttribute("class") .
					"' is not an instance of StepProcessor.", 
				PiwiException :: ERR_WRONG_TYPE);
		}
		$this->logger->debug('Executing StepProcessor: ' . $domElement[0]->getAttribute("class"));
		$xml = $stepProcessor->process($this->_getResults(), $this->_getFiles(), 
			$this->currentStep, $this->numberOfSteps);
		
		$doc = new DOMDocument;
		if ($xml == null || $xml == '') {
			return $doc;
		}
		$doc->loadXml($xml);
		return $doc;
	}
	
	/**
	 * Returns an array containing all results of the currently processed form.
	 * These values are used in StepProcessors to display the results.
	 * The array also contains the current step and the total number of steps in the form.
	 * @return array Array containing all results of the currently processed form.
	 */
	private function _getResults() {
		$results = array();		
		
		foreach ($_POST as $key => $value) {
			if (substr($key, 0, strlen($this->formId)) == $this->formId &&
				$key != $this->formId . '_currentstep') {
				$results[substr($key, strlen($this->formId) + 1)] = $value;
			}
		}
		
		return $results;
	}

	/**
	 * Returns an array containing all files of the currently processed form.
	 * These values are used in StepProcessors to handle posted files.
	 * @return array Array containing all files of the currently processed form.
	 */	
	private function _getFiles() {
		$files = array();		
		
		foreach ($_FILES as $key => $value) {
			if (substr($key, 0, strlen($this->formId)) == $this->formId && $value['name'] != "") {				
				$files[substr($key, strlen($this->formId) + 1)] = $value;
			}
		}
		
		return $files;
	}
	
	/**
	 * Returns all attribute/value-pairs of a DOMElement as a string. Attributes can be filtered.
	 * @param string $domElement The DOMElement whose attributes should be processed.
	 * @param array $filterAttributes The names of the attributes which should be filtered.
	 * @return string All attribute/value-pairs of a DOMElement as a string.
	 */
	private function _getFilteredAttributesAsString(DOMElement $domElement, array $filterAttributes) {
		$result = '';
		
		foreach ($domElement->attributes as $attribute) {
			if (!in_array($attribute->name, $filterAttributes)) {
				$result .= ' ' . $attribute->name . '="' . $attribute->value . '"';
			}
		}
		return $result;
	}	
		
	/**
	 * Returns if a validation has failed while processing.
	 * @return boolean True, if a validation has failed, 
	 * 			false if everything has been processed successfully.
	 */
	public function isValidationFailed() {
		return $this->validationFailed;
	}
	
	/** Returns the id of the currently processed form. 
	 * @return integer The id of the currently processed form.
	 */
	public function getId() {
		return $this->formId . '_';
	}
}
?>