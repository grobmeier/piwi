<?php
/**
 * Interface that navigation generators have to implement.
 */
interface NavigationGenerator {
	/**
	 * Builds the navigation.
	 * @param array $siteElements The SiteElements of the website.
	 * @return string The navigation as HTML.
	 */
	public function generate(array $siteElements);
	
	/**
	 * Used to pass parameters to the Generator.
	 * @param string $key The name of the parameter.
	 * @param string $value The value of the parameter.
	 */
	public function setProperty($key, $value);
}
?>