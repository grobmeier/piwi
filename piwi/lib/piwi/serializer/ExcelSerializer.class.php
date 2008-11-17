<?php
/**
 * Serializes the given XML to Excel (XSL).
 */
class ExcelSerializer implements Serializer {
	/**
	 * Transform the given xml to the output format.
	 * @param DOMDocument $domDocument The content as DOMDocument.
	 * @param string $pageId The id of the requested page.
	 * @param string $templatePath The path to the template which should be used.
	 */
	public function serialize(DOMDocument $domDocument, $pageId, $templatePath) {
		// Register stream wrapper to include custom XSLTStylesheets
		stream_wrapper_register("xsltsss", "XSLTStylesheetStream");
		
		// Configure the transformer
		$processor = new XSLTProcessor;
		$processor->importStyleSheet(DOMDocument::load("resources/xslt/ExcelTransformation-v1.0.xsl"));

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

		// Generate Excel
		header("Content-type: application/vnd-ms-excel"); 
 		header("Content-Disposition: attachment; filename=" . $pageId . ".xls");
		
		echo utf8_decode($html);
	}
}
?>