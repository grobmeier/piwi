<?php
/**
 * Creates a SiteMapPath which consists of the NavigationElements leading to the requested page.
 */
class SiteMapPathNavigation implements NavigationGenerator {
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
		$siteMapPathHTML = "";
		
		if (sizeof($navigationElements) > 0){
			$navigationElement = $navigationElements[0];
			
			do {
				if ($siteMapPathHTML != "") {
					$siteMapPathHTML = " / " . $siteMapPathHTML;
				}
				$siteMapPathHTML = '<a href="' . $navigationElement-> getId() . '.html">' . $navigationElement->getLabel() . '</a>' . $siteMapPathHTML;
				$navigationElement = $navigationElement->getParent();
			} while ($navigationElement != null);
		}
		
		return $siteMapPathHTML;
	}
}
?>