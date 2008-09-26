<?php
/**
 * Interface that serializers have to implement.
 */
interface Serializer {
	/**
	 * Transform the given xml to the output format.
	 * @param DOMDocument $domDocument The content as DOMDocument.
	 * @param string $pageId The id of the requested page.
	 * @param string $templatePath The path to the template which should be used.
	 */
	public function serialize(DOMDocument $domDocument, $pageId, $templatePath);
}
?>