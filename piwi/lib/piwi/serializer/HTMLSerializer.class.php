<?php
/**
 * Serializes the given XML to HTML.
 */
class HTMLSerializer implements Serializer {
	/**
	 * Transform the given xml to the output format.
	 * @param DOMDocument $domDocument The content as DOMDocument.
	 */
	public function serialize(DOMDocument $domDocument) {
		// Register stream wrapper to include custom XSLTStylesheets
		stream_wrapper_register("xsltsss", "XSLTStylesheetStream");
		
		// Configure the transformer
		$processor = new XSLTProcessor;
		$processor->importStyleSheet(DOMDocument::load("resources/xslt/HTMLTransformation-v1.0.xsl"));

		$elements = $domDocument->getElementsByTagName('content');

		for ($i = 0; $i < $elements->length; $i++) {
			$simplexml = simplexml_import_dom($elements->item($i));

			$template = new DOMDocument();
			$template->loadXML($simplexml->asXML());

			$CONTENT[$elements->item($i)->getAttribute("position")] = $processor->transformToXML($template);
		}

		// Include navigation and siteMapPath
		foreach (ConfigurationManager::getInstance()->getHTMLNavigations() as $name => $navigation) {
			$$name = $navigation;
		}
		
		// Show generated page
		include (Site::getInstance()->getTemplate());
	}
}
?>