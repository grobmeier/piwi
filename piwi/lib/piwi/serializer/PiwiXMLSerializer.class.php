<?php
/**
 * Serializes the given XML to PiwiXML.
 */
class PiwiXMLSerializer implements Serializer {
	/**
	 * Transform the given xml to the output format.
	 * @param DOMDocument $domDocument The content as DOMDocument.
	 */
	public function serialize(DOMDocument $domDocument) {
		header("Content-type: application/xml");		
		echo $domDocument->saveXML();		
	}
}
?>