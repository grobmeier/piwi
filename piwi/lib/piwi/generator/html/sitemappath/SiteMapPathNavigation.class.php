<?php
/**
 * Creates a SiteMapPath which consists of the SiteElements leading to the requested page.
 */
class SiteMapPathNavigation implements NavigationGenerator {
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
		$siteMapPathHTML = "";
		if (sizeof($siteElements) > 0){
			$siteElement = $siteElements[0];
			
			do {
				if ($siteMapPathHTML != "") {
					$siteMapPathHTML = " / " . $siteMapPathHTML;
				}
				$siteMapPathHTML = '<a href="' . $siteElement-> getId() . '.html">' . $siteElement->getLabel() . '</a>' . $siteMapPathHTML;
				$siteElement = $siteElement->getParent();
			} while ($siteElement != null);
		}
		
		return $siteMapPathHTML;
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