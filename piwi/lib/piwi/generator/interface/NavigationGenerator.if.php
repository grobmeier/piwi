<?php
/**
 * Interface that navigation generators have to implement.
 */
interface NavigationGenerator {
	/**
	 * Builds the navigation.
	 * @param array $navigationElements The NavigationElements of the website.
	 * @return string The navigation as HTML.
	 */
	public function generate($navigationElements);
}
?>