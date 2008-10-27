<?
/**
 * Processes forms specified as PiwiForms.
 * Forms can contain multiple steps (wizards).
 * The values entered by a user can be verified by using Validators.
 * It is also possible to handle multiple forms on one page.
 */
class FormProcessor {
	/** The id of the currently processed form. */
	private static $formId = 0;
	
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
	
	/**
	 * Loads the form from the given file and generates the the content as PiwiXML.
	 * @param $string $path The path to the file containing the form.
	 * @return DOMDocument The rendered form as PiwiXML.
	 */
	public static function process($path) {
		// Increase id of form to give every form an unique id
		FormProcessor::$formId++;
		
		// Reset variables
		FormProcessor::$validationFailed = false;
		FormProcessor::$validate = true;
		FormProcessor::$ignoredFields = array();
		
		// Create DOMXPath to query the forms xml
		if (!file_exists($path)) {
			throw new PiwiException(
				"Could not find the forms definition file (Path: '" . $path . "').", 
				PiwiException :: ERR_NO_XML_DEFINITION);
		}
		$domXPath = new DOMXPath(DOMDocument::load($path));
		$domXPath->registerNamespace('piwiform', 'http://piwi.googlecode.com/xsd/piwiform');
	
		// Determinate the current step in the formular
		// if request is a postback increase number of steps otherwise begin with step 1
		FormProcessor::$currentStep = 0;
		
		if (isset($_POST[FormProcessor::$formId . 'currentstep'])) {
			FormProcessor::$currentStep = $_POST[FormProcessor::$formId . 'currentstep'];
		}
		
		// Determinate number of total steps in form
		FormProcessor::$numberOfSteps = $domXPath->evaluate('count(/piwiform:form/piwiform:step)');
		
		if (FormProcessor::$currentStep > FormProcessor::$numberOfSteps || FormProcessor::$currentStep < 0) {
			throw new PiwiException(
				"This form only has " . $numberOfSteps . " steps, you requested number " . FormProcessor::$currentStep . ".", 
				PiwiException :: FORMS_ERROR);
		}
		
		$stepXML = '';
		
		// Configure the transformer
		$processor = new XSLTProcessor;
		$processor->registerPHPFunctions();
		$processor->importStyleSheet(DOMDocument::load("resources/xslt/FormTransformation.xsl"));		
		
		// Validate formdata of last step
		if (FormProcessor::$currentStep > 0) {
			$lastStepXML = simplexml_import_dom($domXPath->query('/piwiform:form/piwiform:step[' . FormProcessor::$currentStep . ']')->item(0));
			
			$template = new DOMDocument();
			$template->loadXML($lastStepXML->asXML());
			
			$stepXML = substr($processor->transformToXML($template), 39);
		}
		
		// If validation was successful show next step
		if (!FormProcessor::$validationFailed) {
			FormProcessor::$currentStep++;
			FormProcessor::$validate = false;
			
			$stepXML = '';
			$currentStepXML = simplexml_import_dom($domXPath->query('/piwiform:form/piwiform:step[' . FormProcessor::$currentStep . ']')->item(0));
			
			$template = new DOMDocument();
			$template->loadXML($currentStepXML->asXML());
			
			$stepXML = substr($processor->transformToXML($template), 39);
		}

		// Build xml
		$piwixml = '<form action="' . Request::getPageId() . '.' . Request::getExtension() . '" method="post">';
		$piwixml .= '<input name="' . FormProcessor::$formId . 'currentstep" type="hidden" value="' . FormProcessor::$currentStep . '" />';

		// Add current values as hidden field (but no checkboxes), so save their state
		foreach ($_POST as $key => $value) {
			if ($key != FormProcessor::$formId . 'currentstep' && !(isset(FormProcessor::$ignoredFields[$key]))) {
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
		$piwixml .= $stepXML;
		
		// Show buttons	if not in last step
		if (FormProcessor::$currentStep < FormProcessor::$numberOfSteps) {
			// Send button
			$configuration = $domXPath->query('/piwiform:form/piwiform:configuration')->item(0);
			$piwixml .= '<br /><br /><input type="submit" value="' . $configuration->getAttribute("submitText") . '" />';
			
			// Reset button
			if ($configuration->hasAttribute("resetText")) {
				$piwixml .= ' <input type="reset" value="' . $configuration->getAttribute("resetText") . '" />';
			}
		}		
		$piwixml .= '</form>';

		$doc = new DOMDocument;
		$doc->loadXml($piwixml);
		return $doc;
	}
	
	/**
	 * Converts the given INPUT element to PiwiXML.
	 * @param array $domElement An array containing the DOMElement to convert.
	 * @return DOMDocument The PiwiXML of the given DOMElement as DOMDocument.
	 */
	public static function generateInput($domElement) {
		// Remove '[]' from field name, to handle arrays correctly
		$name = str_replace("[]", "", FormProcessor::$formId . $domElement[0]->getAttribute("name"));
		
		$value = $domElement[0]->getAttribute("value");
		$checked = $domElement[0]->getAttribute("checked");
		
		$checkable = $domElement[0]->getAttribute("type") == 'radio' || $domElement[0]->getAttribute("type") == 'checkbox';
		
		// if INPUT is a CheckBox or RadioButton and if request is a postback, then determinate it's checked state to set the same status again
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
			FormProcessor::$ignoredFields[$name] = $domElement[0]->getAttribute("name");
		}		
		
		// if INPUT is a normal TextField and if request is a postback, then set the value to the entered one
		if (!$checkable && isset($_POST[$name])) {
			$value = $_POST[$name];
		} 

		$xml = ' <input name="' . FormProcessor::$formId . $domElement[0]->getAttribute("name") . '"'
		. ($domElement[0]->hasAttribute("type") ? ' type="' . $domElement[0]->getAttribute("type") . '" ' : 'type="text" ')
		. ($domElement[0]->hasAttribute("maxlength") ? ' maxlength="' . $domElement[0]->getAttribute("maxlength") . '" ' : '')
		. ($domElement[0]->hasAttribute("size") ? ' size="' . $domElement[0]->getAttribute("size") . '" ' : '')
		. ($domElement[0]->hasAttribute("readonly") ? ' readonly="' . $domElement[0]->getAttribute("readonly") . '" ' : '')
		. (($checked != '') ? ' checked="' . $checked . '" ' : '')
		. 'value="' . $value . '"'		
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
	public static function generateSelect($domElement) {
		// Remove '[]' from field name, to handle arrays correctly
		$name = str_replace("[]", "", FormProcessor::$formId . $domElement[0]->getAttribute("name"));
		
		$xml = ' <select name="' . FormProcessor::$formId . $domElement[0]->getAttribute("name") . '"'
			. ($domElement[0]->hasAttribute("size") ? ' size="' . $domElement[0]->getAttribute("size") . '" ' : '')
			. ($domElement[0]->hasAttribute("multiple") ? ' multiple="' . $domElement[0]->getAttribute("multiple") . '" ' : '')
			. '>';

		FormProcessor::$ignoredFields[$name] = $domElement[0]->getAttribute("name");

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
       			. ($option->hasAttribute("value") ? ' value="' . $option->getAttribute("value") . '" ' : '')
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
	public static function generateTextArea($domElement) {
		$value = $domElement[0]->textContent;

		if (isset($_POST[FormProcessor::$formId . $domElement[0]->getAttribute("name")])) {
			$value = $_POST[FormProcessor::$formId . $domElement[0]->getAttribute("name")];
		} 

		$xml = ' <textarea name="' . FormProcessor::$formId . $domElement[0]->getAttribute("name") . '"'
					. ($domElement[0]->hasAttribute("cols") ? ' cols="' . $domElement[0]->getAttribute("cols") . '" ' : '')
					. ($domElement[0]->hasAttribute("rows") ? ' rows="' . $domElement[0]->getAttribute("rows") . '" ' : '')
					. ($domElement[0]->hasAttribute("readonly") ? ' readonly="' . $domElement[0]->getAttribute("readonly") . '" ' : '')
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
	public static function executeValidator($domElement) {		
		if (!FormProcessor::$validate) {
			return new DOMDocument();
		}
		
		// Create instance of Validator
		$class = new ReflectionClass($domElement[0]->getAttribute("class"));
		$validator = $class->newInstance($domElement[0]);

		if (!$validator instanceof Validator){
			throw new PiwiException(
				"The Class with id '" . $domElement[0]->getAttribute("class") . "' is not an instance of Validator.", 
				PiwiException :: ERR_WRONG_TYPE);
		}
		
		$errorMessage = $validator->validate();
		
		if ($errorMessage == null) {
			return new DOMDocument();
		} else {
			$errorMessage = '<span class="error"> ' . $errorMessage . '</span>';
			FormProcessor::$validationFailed = true;				
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
	public static function executeStepProcessor($domElement) {
		// Create instance of StepProcessor
		$class = new ReflectionClass($domElement[0]->getAttribute("class"));
		$stepProcessor = $class->newInstance();

		if (!$stepProcessor instanceof StepProcessor){
			throw new PiwiException(
				"The Class with id '" . $domElement[0]->getAttribute("class") . "' is not an instance of StepProcessor.", 
				PiwiException :: ERR_WRONG_TYPE);
		}
		
		$xml = $stepProcessor->process(FormProcessor::getResults());
		
		$doc = new DOMDocument;
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
			if ($key{0} == FormProcessor::$formId && $key != FormProcessor::$formId . 'currentstep') {				
				$results[substr($key, 1)] = $value;
			}
		}
		$results["CURRENT_STEPS"] = FormProcessor::$currentStep;
		$results["NUMBER_OF_STEPS"] = FormProcessor::$numberOfSteps;
		
		return $results;
	}
	
	/** Returns the id of the currently processed form. 
	 * @return integer The id of the currently processed form.
	 */
	public static function getId() {
		return FormProcessor::$formId;
	}
}
?>