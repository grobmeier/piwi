<?php
/**
 * Interface that navigation providers have to implement.
 */
interface Navigation {
	/**
	 * Builds the navigation.
	 * @param string $contentPath Name of the folder where the content is placed.
	 * @param array $navigation The pages of the website.
	 * @return string The navigation as HTML.
	 */
	public function generate($contentPath, $navigation);
}
?>