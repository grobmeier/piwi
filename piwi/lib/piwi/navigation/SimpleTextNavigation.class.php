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
		$navigationHTML = "";
		
		foreach($navigationElements as $navigationElement) {
			// If the page is selected set another css class to highlight the item
			$cssClass = "";
			if ($navigationElement->isSelected()) {
				$cssClass = ' class="selected"';
			}
			$navigationHTML .= '<a href="' . $navigationElement-> getId() . '.html"' . $cssClass . '>' . $navigationElement->getLabel() . '</a>';
		}
		
		return $navigationHTML;
	}
}
?>