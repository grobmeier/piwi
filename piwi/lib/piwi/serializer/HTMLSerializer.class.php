<?php
/**
 * Interface that serializers have to implement.
 */
class HTMLSerializer implements Serializer {
	/**
	 * Transform the given xml to the output format.
	 * @param DOMDocument $domDocument The content as DOMDocument.
	 * @return string The transformed content.
	 */
	public function serialize(DOMDocument $domDocument) {
		// Load xslt file
		$xsl = new DOMDocument;
		$xsl->load("resources/xslt/document-v1.0.xsl");
		
		// Configure the transformer
		$proc = new XSLTProcessor;
		$proc->registerPHPFunctions();
		$proc->importStyleSheet($xsl); // attach the xsl rules

		$elements = $domDocument->getElementsByTagName('content');

		for ($i = 0; $i < $elements->length; $i++) {
			$simplexml = simplexml_import_dom($elements->item($i));

			$template = new DOMDocument();
			$template->loadXML($simplexml->asXML());
			$result[$elements->item($i)->getAttribute("position")] = $proc->transformToXML($template);
		}
		return $result;
	}
}
?>