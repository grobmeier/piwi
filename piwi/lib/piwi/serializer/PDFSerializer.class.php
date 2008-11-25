<?php
/**
 * Serializes the given XML to PDF.
 */
class PDFSerializer implements Serializer {
	/**
	 * Transform the given xml to the output format.
	 * @param DOMDocument $domDocument The content as DOMDocument.
	 */
	public function serialize(DOMDocument $domDocument) {
		// Register stream wrapper to include custom XSLTStylesheets
		stream_wrapper_register("xsltsss", "XSLTStylesheetStream");
		
		// Configure the transformer
		$processor = new XSLTProcessor;
		$processor->importStyleSheet(DOMDocument::load("resources/xslt/PDFTransformation-v1.0.xsl"));

		$elements = $domDocument->getElementsByTagName('content');
		
		// generate HTML
		$html = '<html><body>';

		foreach ($elements as $item) {
			$position = DOMDocument::loadXML($domDocument->saveXML($item));
			$html .=  $processor->transformToXML($position);
			$html .= "<br>";
		}
	
		$html .= "</body></html>";

		// generate PDF		
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		$html2pdf = new HTML2FPDF();		
		$html2pdf->AddPage();
		$html2pdf->WriteHTML(utf8_decode($html));
		$html2pdf->Output(Request::getPageId() . '.pdf', 'I');
	}
}
?>