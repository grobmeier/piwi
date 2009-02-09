<?php
/**
 * Interface that serializers have to implement.
 */
interface Serializer {
	/**
	 * Transform the given xml to the output format.
	 * @param DOMDocument $domDocument The content as DOMDocument.
	 */
	public function serialize(DOMDocument $domDocument);
}
?>