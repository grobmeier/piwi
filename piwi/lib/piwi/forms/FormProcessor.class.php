<?php
/**
 * Processes forms specified as PiwiForms.
 * Forms can contain multiple steps (wizards).
 * The values entered by a user can be verified by using Validators.
 * It is also possible to handle multiple forms on one page.
 */
class FormProcessor {
	private static $logger = null;
	
	/** The id of the currently processed form. */
	private static $formId = null;
	
	/** Indicates that a validator of the current form failed. */
	private static $validationFailed = false;
	
	/** The current step of the currently processed form. */
	private static $currentStep = 0;
	
	/** The total number of steps of the currently processed form. */
	private static $numberOfSteps = 0;
	
	/** True if validation has to be performed, otherwise false. */
	private static $validate = true;
	
	/** Array of the names of all fields in the current form that should be rejected from hidden fields. */
	private static $ignoredFields;
	
	/** The XSLTProcessor. */
	private static $processor = null;
	
	/**
	 * Since we are in a static context and PHP5 can only use literals and strings
	 * for intializing static members, we need to do some lazy initilizing here.
	 */
	private function _getLogger() {
		if (self::$logger == null) {
			self::$logger = & LoggerManager::getLogger('FormProcessor.class.php');
		}
		return self::$logger;
	}
	
	/**
	 * Loads the form from the given file and generates the the content as PiwiXML.
	 * @param string $id The id of the form.
	 * @return DOMDocument The rendered form as PiwiXML.
	 */
	public static function process($id) {
		self::_getLogger()->debug('Processing form with ID: ' . $id);
		// Increase id of form to give every form an unique id
		self::$formId = $id;
		
		// Reset variables
		self::$validationFailed = false;
		self::$validate = true;
		self::$ignoredFields = array();
		
		$domXPath = FormFactory::getFormById($id);
	
		// Determinate the current step in the formular
		// if request is a postback increase number of steps otherwise begin with step 1
		self::$currentStep = 0;
		
		self::initRequestParams();
		
		if (isset($_POST[self::$formId . '_currentstep'])) {
			self::$currentStep = $_POST[self::$formId . '_currentstep'];
		}
		
		// Determinate number of total steps in form
		self::$numberOfSteps = $domXPath->evaluate('count(/piwiform:form/piwiform:step)');
		
		if (self::$currentStep > self::$numberOfSteps || self::$currentStep < 0) {
			throw new PiwiException("This form only has " . $numberOfSteps .
					" steps, you requested number " . self::$currentStep . ".", 
				PiwiException :: FORMS_ERROR);
		}
		
		// Validate formdata of last step
		if (self::$currentStep > 0) {
			$stepXML = self::getStepXML($domXPath);
		}
		
		self::_getLogger()->debug('Calling Preprocessors ' . $id);
		self::callPreProcessor($domXPath);
		
		$postbackNode = $domXPath->evaluate('//piwiform:form/piwiform:step/@postback');

		if ($postbackNode != null && $postbackNode->item(0) != null) {
			$temp = $postbackNode->item(0)->nodeValue;
			if ($temp == null) {
             	$postback = false;
			} else {
				self::_getLogger()->debug('Form with ID: ' . $id . ' is a postback form.');
				$postback = true;
			}
		} else {
			$postback = false;
		}
		
		// If validation was successful show next step
		if (!self::$validationFailed) {
			if (!$postback) {
            	self::$currentStep++;   
            } else {
            	self::$currentStep = 1;
            }
			self::$validate = false;

			if (!$postback || self::$currentStep == 1) {
				$stepXML = self::getStepXML($domXPath);	
			}
		}

		// Build xml
		$piwixml = '<form xmlns="http://piwi.googlecode.com/xsd/piwixml" action="' . 
			Request::getPageId() . '.' . Request::getExtension() .
			'" method="post" enctype="multipart/form-data">';
		$piwixml .= '<input name="' . self::$formId . '_currentstep" type="hidden" value="' .
			self::$currentStep . '" />';
		
		if (self::$currentStep < self::$numberOfSteps) {
			// Add current values as hidden field (but no checkboxes), to save their state
			foreach ($_POST as $key => $value) {
				if ($key != self::$formId . '_' . 'currentstep' && !(isset(self::$ignoredFields[$key]))) {
					if (is_array($value)) {
						foreach ($value as $var) {
	       					$piwixml .= '<input name="' . $key . '[]" type="hidden" value="' . $var . '" />';
						}					
					} else { 
						$piwixml .= '<input name="' . $key . '" type="hidden" value="' . $value . '" />';
					}
				}
			}
		}
		
		// add step
		$piwixml .= $stepXML . '</form>';

		$doc = new DOMDocument;
		$doc->loadXml($piwixml);
		self::_getLogger()->debug('Form with ID: ' . $id . ' has sucessfully processed.');
		return $doc;
	}
	
	/**
	 * Initalizes the request parameters.
	 * 
	 * There is an issue with htmlspecialchars.
	 * Not each serializer does expect htmlspecialchars.
	 */
	private static function initRequestParams() {
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
	 * Initalizes the XSLTProcessor, if this hasn't happened before'
	 */
	private static function initXSLTProcessor() {
		if (self::$processor == null) {
			self::$processor = new XSLTProcessor;
			self::$processor->registerPHPFunctions();
			self::$processor->importStyleSheet(DOMDocument::load("resources/xslt/FormTransformation.xsl"));
		}
	}
	
	/**
	 * Processes the current step of the form and returns it as XML.
	 * @param DOMXPath $domXPath The form where the step should be retrieved from.
	 * @return string The current Step as XML.
	 */
	private static function getStepXML(DOMXPath $domXPath) {
		self::_getLogger()->debug('Processing step and returning result as XML');
		self::initXSLTProcessor();
		
		$stepXML = $domXPath->query('/piwiform:form/piwiform:step[' . self::$currentStep . ']')->item(0);
		$template = DOMDocument::loadXML($stepXML->ownerDocument->saveXML($stepXML));
			
		return self::$processor->transformToXML($template);
	}
	
	/**
	 * Calls a preprocessor defined as an attribute in the step tag
	 */
	private static function callPreProcessor(DOMXPath $domXPath) {
		self::initXSLTProcessor();
		// XPath isn't 0 based, first node is node number 1.
		$formnumber = self::$currentStep + 1;
		$form = $domXPath->query('/piwiform:form/piwiform:step[' . $formnumber . ']');
		$result = $form->item(0)->getAttribute('preprocessor');
		
		self::_getLogger()->debug("Found Preprocessor: " . $result);
		
		if ($result != null) {
			$class = new ReflectionClass((string)$result);
			$preprocessor = $class->newInstance();
			
			if (!$preprocessor instanceof Preprocessor) {
				throw new PiwiException("The Class with id '" . $result .
						"' is not an instance of Preprocessor.", 
					PiwiException :: ERR_WRONG_TYPE);
			}
			$preprocessor->process();
			self::_getLogger()->debug("Preprocessor: " . $result . " has been called.");
			
		}
		self::_getLogger()->debug("Preprocessor actions finished.");
	}
	
	/**
	 * Converts the given INPUT element to PiwiXML.
	 * @param array $domElement An array containing the DOMElement to convert.
	 * @return DOMDocument The PiwiXML of the given DOMElement as DOMDocument.
	 */
	public static function generateInput(array $domElement) {
		// Remove '[]' from field name, to handle arrays correctly
		$name = str_replace("[]", "", self::$formId . '_' . $domElement[0]->getAttribute("name"));
		
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
			self::$ignoredFields[$name] = $domElement[0]->getAttribute("name");
		}		
		
		// if INPUT is a normal TextField and if request is a postback, then set the value to the entered one
		if (!$checkable && isset($_POST[$name])) {
			$value = $_POST[$name];
		} 

		$xml = ' <input name="' . self::$formId . '_' . 
			$domElement[0]->getAttribute("name") . '"' . 
			($domElement[0]->hasAttribute("type") ? ' type="' . $domElement[0]->getAttribute("type") 
				. '" ' : 'type="text" ') 
			. self::getFilteredAttributesAsString($domElement[0], array ('name', 'type', 'checked', 'value'))
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
	public static function generateSelect(array $domElement) {
		// Remove '[]' from field name, to handle arrays correctly
		$name = str_replace("[]", "", self::$formId . '_' . $domElement[0]->getAttribute("name"));
		
		$xml = ' <select name="' . self::$formId . '_' . $domElement[0]->getAttribute("name") . '"'
			. self::getFilteredAttributesAsString($domElement[0], array ('name'))
			. '>';

		self::$ignoredFields[$name] = $domElement[0]->getAttribute("name");

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
					. self::getFilteredAttributesAsString($option, array ('selected'))
	       			. '>'
	       			. $option->textContent
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
	public static function generateTextArea(array $domElement) {
		$value = $domElement[0]->textContent;

		if (isset($_POST[self::$formId . '_' . $domElement[0]->getAttribute("name")])) {
			$value = $_POST[self::$formId . '_' . $domElement[0]->getAttribute("name")];
		} 

		$xml = ' <textarea name="' . self::$formId . '_' . $domElement[0]->getAttribute("name") . '"'
					. self::getFilteredAttributesAsString($domElement[0], array ('name', 'value'))
					. '>'
					. ($value == "" ? ' ' : $value)
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
	public static function executeValidator(array $domElement) {		
		if (!self::$validate) {
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
			$errorMessage = '<span class="error"> ' . $errorMessage . '</span>';
			self::$validationFailed = true;				
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
	public static function executeStepProcessor(array $domElement) {
		// Create instance of StepProcessor
		$class = new ReflectionClass($domElement[0]->getAttribute("class"));
		$stepProcessor = $class->newInstance();

		if (!$stepProcessor instanceof StepProcessor) {
			throw new PiwiException("The Class with id '" . $domElement[0]->getAttribute("class") .
					"' is not an instance of StepProcessor.", 
				PiwiException :: ERR_WRONG_TYPE);
		}
		self::_getLogger()->debug('Executing StepProcessor: ' . $domElement[0]->getAttribute("class"));
		$xml = $stepProcessor->process(self::getResults(), self::getFiles(), 
			self::$currentStep, self::$numberOfSteps);
		
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
	private static function getResults() {
		$results = array();		
		
		foreach ($_POST as $key => $value) {
			if (substr($key, 0, strlen(self::$formId)) == self::$formId &&
				$key != self::$formId . '_currentstep') {
				$results[substr($key, strlen(self::$formId) + 1)] = $value;
			}
		}
		
		return $results;
	}

	/**
	 * Returns an array containing all files of the currently processed form.
	 * These values are used in StepProcessors to handle posted files.
	 * @return array Array containing all files of the currently processed form.
	 */	
	private static function getFiles() {
		$files = array();		
		
		foreach ($_FILES as $key => $value) {
			if (substr($key, 0, strlen(self::$formId)) == self::$formId && $value['name'] != "") {				
				$files[substr($key, strlen(self::$formId) + 1)] = $value;
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
	private static function getFilteredAttributesAsString(DOMElement $domElement, array $filterAttributes) {
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
	 * 			false if everything has been processed succesfully.
	 */
	public static function isValidationFailed() {
		return self::$validationFailed;
	}
	
	/** Returns the id of the currently processed form. 
	 * @return integer The id of the currently processed form.
	 */
	public static function getId() {
		return self::$formId . '_';
	}
}
?>