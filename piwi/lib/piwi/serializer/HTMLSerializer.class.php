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

		foreach ($elements as $item) {
			$position = DOMDocument::loadXML($domDocument->saveXML($item));
			$CONTENT[$item->getAttribute("position")] = $processor->transformToXML($position);
		}

		// Include header (title and keywords)
		$TITLE = '';
		$KEYWORDS = '';
		$headers = $domDocument->getElementsByTagName('head');
		if ($headers->length > 0) {
			$titles = $headers->item(0)->getElementsByTagName('title');			
			if ($titles->length > 0) {
				$TITLE = $titles->item(0)->nodeValue;
			}
			$keywords = $headers->item(0)->getElementsByTagName('keywords');			
			if ($keywords->length > 0) {
				$KEYWORDS = $keywords->item(0)->nodeValue;
			}
		}
		
		// Include navigation and siteMapPath
		foreach (ConfigurationManager::getInstance()->getHTMLNavigations() as $name => $navigation) {
			$$name = $navigation;
		}
		
		// Show generated page
		include (BeanFactory :: getBeanById('site')->getTemplate());
	}
}
?>