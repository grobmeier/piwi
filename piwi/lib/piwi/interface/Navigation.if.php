<?php
/**
 * Interface that navigation providers have to implement.
 */
interface Navigation {
	/**
	 * Builds the navigation.
	 * @param array $navigation The NavigationElements of the website.
	 * @return string The navigation as HTML.
	 */
	public function generate($navigation);
}
?>