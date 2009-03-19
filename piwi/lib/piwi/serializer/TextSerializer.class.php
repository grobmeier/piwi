<?php
/**
 * Serializes the given XML to Json (json).
 */
class TextSerializer implements Serializer {
	/**
	 * Transform the given xml to the output format.
	 * @param DOMDocument $domDocument The content as DOMDocument.
	 */
	public function serialize(DOMDocument $domDocument) {
		// Register stream wrapper to include custom XSLTStylesheets
		stream_wrapper_register("xsltsss", "XSLTStylesheetStream");
		
		// Configure the transformer
		$processor = new XSLTProcessor;
		$processor->importStyleSheet(DOMDocument::load("resources/xslt/TextTransformation-v1.0.xsl"));

		$elements = $domDocument->getElementsByTagName('content');
		
		foreach ($elements as $item) {
			$position = DOMDocument::loadXML($domDocument->saveXML($item));
			$html .=  $processor->transformToXML($position);
		}
		
		echo utf8_decode($html);
	}
}
?>