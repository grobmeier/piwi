<?php
/**
 * Serializes the given XML to Word (DOC).
 */
class WordSerializer implements Serializer {
	/**
	 * Transform the given xml to the output format.
	 * @param DOMDocument $domDocument The content as DOMDocument.
	 */
	public function serialize(DOMDocument $domDocument) {
		// Register stream wrapper to include custom XSLTStylesheets
		stream_wrapper_register("xsltsss", "XSLTStylesheetStream");
		
		// Configure the transformer
		$processor = new XSLTProcessor;
		$processor->importStyleSheet(DOMDocument::load("resources/xslt/OfficeTransformation-v1.0.xsl"));

		$elements = $domDocument->getElementsByTagName('content');
		
		// Generate HTML
		$html = '<html><body>';

		for ($i = 0; $i < $elements->length; $i++) {
			$simplexml = simplexml_import_dom($elements->item($i));

			$template = new DOMDocument();
			$template->loadXML($simplexml->asXML());

			$html .= $processor->transformToXML($template);
		}
		
		$html .= "</body></html>";
		
		// Generate Word
		header("Content-type: application/vnd-ms-word");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Disposition: attachment; filename=" . $pageId . ".doc");
		
		echo utf8_decode($html);
	}
}
?>