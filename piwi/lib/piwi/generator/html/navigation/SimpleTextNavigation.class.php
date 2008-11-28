<?php
/**
 * Creates a simple navigation.
 */
class SimpleTextNavigation implements NavigationGenerator {
	/**
	 * Constructor.
	 */
	public function __construct() {
	}
	
	/**
	 * Builds the navigation.
	 * @param array $navigationElements The NavigationElements of the website.
	 * @return string The navigation as HTML.
	 */
	public function generate(array $navigationElements) {
		return $this->getNavigation($navigationElements);
	}
	
	/**
     * Returns the recursivly built navigation.
     * @param array $navigationElements The navigation which is an array of NavigationElements representing the website structure.
     * @return string The recursivly built navigation.
     */
    private function getNavigation($navigationElements) {
    	if ($navigationElements == null) {
    		return '';
    	} else {
    		$result = '<ul>';
    		
    		foreach ($navigationElements as $element) {
    			// If the page is selected set another css class to highlight the item
				$cssClass = "";
				if ($element->isOpen()) {
					$cssClass = ' class="selected"';
				}
				if (!$element->isHiddenInNavigation()) {
					$filepath = $element-> getId() . ".html";
					if (substr($element->getFilePath(), 0, 4) == 'http') {
						$filepath = $element->getFilePath();
					}
    				$result .= '<li' . $cssClass . '><a href="' . $filepath . '">' . $element->getLabel() . '</a>' . $this->getNavigation($element->getChildren()) . '</li>';
				}
    		}
    		
    		$result .= '</ul>';
    		return $result;
    	}
    }
    
    /**
	 * Used to pass parameters to the Generator.
	 * @param string $key The name of the parameter.
	 * @param string $value The value of the parameter.
	 */
    public function setProperty($key, $value) {    	
    }
}
?>