<?php
/**
 * Serializes the given XML to PDF.
 */
class PDFSerializer implements Serializer {
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
		$processor->importStyleSheet(DOMDocument::load("resources/xslt/PDFTransformation-v1.0.xsl"));

		$elements = $domDocument->getElementsByTagName('content');
		
		// generate HTML
		$html = '<html><body>';

		for ($i = 0; $i < $elements->length; $i++) {
			$simplexml = simplexml_import_dom($elements->item($i));

			$template = new DOMDocument();
			$template->loadXML($simplexml->asXML());

			$html .= $processor->transformToXML($template);
			$html .= "<br />";
		}
		
		$html .= "</body></html>";

		// generate PDF		
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		$html2pdf = new HTML2FPDF();		
		$html2pdf->AddPage();
		$html2pdf->WriteHTML(utf8_decode($html));
		$html2pdf->Output($pageId . '.pdf', 'I');
	}
}
?>