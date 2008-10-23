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
	
	/**
	 * Loads the form from the given file and generates the the content as PiwiXML.
	 * @param $string $path The path to the file containing the form.
	 * @return DOMDocument The rendered form as PiwiXML.
	 */
	public static function process($path) {
		// Increase id of form to give every form an unique id
		FormProcessor::$formId++;
		
		// Reset $validationFailed
		FormProcessor::$validationFailed = false;
		
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
		
		// Validate formdata of last step
		if (FormProcessor::$currentStep > 0) {
			$lastStepXML = $domXPath->query('/piwiform:form/piwiform:step[' . FormProcessor::$currentStep . ']');
			
			foreach ($lastStepXML->item(0)->getElementsByTagName('*') as $domElement) {
				$stepXML .= FormProcessor::generateFormControl($domElement, true);       
			}
		}
		
		// If validation was successful show next step
		if (!FormProcessor::$validationFailed) {
			FormProcessor::$currentStep++;
			$stepXML = '';
			$currentStepXML = $domXPath->query('/piwiform:form/piwiform:step[' . FormProcessor::$currentStep . ']');
			
			foreach ($currentStepXML->item(0)->getElementsByTagName('*') as $domElement) {
				$stepXML .= FormProcessor::generateFormControl($domElement, false);	       
			}			
		}

		// Build xml
		$piwixml = '<form action="' . Request::getPageId() . '.' . Request::getExtension() . '" method="post">';
		$piwixml .= '<input name="' . FormProcessor::$formId . 'currentstep" type="hidden" value="' . FormProcessor::$currentStep . '" />';

		foreach ($_POST as $key => $value) {
			if ($key != FormProcessor::$formId . 'currentstep') {
				$piwixml .= '<input name="' . $key . '" type="hidden" value="' . $value . '" />';
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
	 * Converts the given form element (like <input />, <textarea />) to PiwiXML.
	 * @param DOMElement $domElement The DOMElement to convert.
	 * @param boolean $validate Set to true if validators should be evaluated otherwise false.
	 * @return string The PiwiXML of the given DOMElement.
	 */
	private static function generateFormControl(DOMElement $domElement, $validate) {
		// Get value
		$value = $domElement->getAttribute("value");

		if (isset($_POST[FormProcessor::$formId . $domElement->getAttribute("name")])) {
			$value = $_POST[FormProcessor::$formId . $domElement->getAttribute("name")];
		} 
		
		if ($domElement->nodeName == "input") {
			return ' <input name="' . FormProcessor::$formId . $domElement->getAttribute("name") . '"'
					. ($domElement->hasAttribute("type") ? ' type="' . $domElement->getAttribute("type") . '" ' : 'type="text" ')
					. ($domElement->hasAttribute("maxlength") ? ' maxlength="' . $domElement->getAttribute("maxlength") . ' " ' : '')
					. ($domElement->hasAttribute("size") ? ' size="' . $domElement->getAttribute("size") . '" ' : '')
					. 'value="' . $value . '"'
					. ' />';
		} else if ($domElement->nodeName == "textarea") {
			return ' <textarea name="' . FormProcessor::$formId . $domElement->getAttribute("name") . '"'
					. ($domElement->hasAttribute("cols") ? ' cols="' . $domElement->getAttribute("cols") . '" ' : '')
					. ($domElement->hasAttribute("rows") ? ' rows="' . $domElement->getAttribute("rows") . '" ' : '')
					. '>'
					. ($value == "" ? ' ' : $value)
					. '</textarea>';
		} else if ($domElement->nodeName == "validator" && $validate) {
			// Create instance of Validator
			$class = new ReflectionClass($domElement->getAttribute("class"));
			$validator = $class->newInstance($domElement);

			if (!$validator instanceof Validator){
				throw new PiwiException(
					"The Class with id '" . $domElement->getAttribute("class") . "' is not an instance of Validator.", 
					PiwiException :: ERR_WRONG_TYPE);
			}
			
			$errorMessage = $validator->validate();
			if ($errorMessage != null) {
				$errorMessage = '<span class="error"> ' . $errorMessage . '</span>';
				FormProcessor::$validationFailed = true;				
			}
			
			return $errorMessage;
		} else if ($domElement->nodeName == "stepprocessor") {
			// Create instance of StepProcessor
			$class = new ReflectionClass($domElement->getAttribute("class"));
			$stepProcessor = $class->newInstance();

			if (!$stepProcessor instanceof StepProcessor){
				throw new PiwiException(
					"The Class with id '" . $domElement->getAttribute("class") . "' is not an instance of StepProcessor.", 
					PiwiException :: ERR_WRONG_TYPE);
			}
			
			return $stepProcessor->process(FormProcessor::getResults());
			
		} else if ($domElement->nodeName != "validator") {
			$xml = $domElement->ownerDocument->saveXML($domElement);
			return $xml;
		}
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