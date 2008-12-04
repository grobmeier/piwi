<?php
/**
 * Creates a simple navigation.
 */
class SimpleTextNavigationGenerator implements NavigationGenerator {
	/**
	 * Constructor.
	 */
	public function __construct() {
	}
	
	/**
	 * Builds the navigation.
	 * @param array $siteElements The SiteElements of the website.
	 * @return string The navigation as HTML.
	 */
	public function generate(array $siteElements = null) {
    	if ($siteElements == null) {
    		return '';
    	} else {
    		$result = '<ul>';
    		
    		foreach ($siteElements as $element) {
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
    				$result .= '<li' . $cssClass . '><a href="' . $filepath . '">' . $element->getLabel() . '</a>' . $this->generate($element->getChildren()) . '</li>';
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