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
	public function generate($navigationElements) {
		return $this->getNavigation($navigationElements) . '<div style="clear: both"> </div>';
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
    			$result .= '<li' . $cssClass . '><a href="' . $element-> getId() . '.html">' . $element->getLabel() . '</a>' . $this->getNavigation($element->getChildren()) . '</li>';
    		}
    		
    		$result .= '</ul>';
    		return $result;
    	}
    }
}
?>