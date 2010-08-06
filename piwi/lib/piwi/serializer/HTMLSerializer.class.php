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

		// Include header (title, keywords and description)
		$headVars = array('title', 'keywords', 'description');
		$headVars = array_combine(array_map('strtoupper', $headVars),$headVars);
		$headers  = $domDocument->getElementsByTagName('head');
		foreach($headVars as $VARNAME => $tagname) {
			$$VARNAME = '';
			if ($headers->length < 1)
				continue;
			($element = $headers->item(0)->getElementsByTagName($tagname))
				&& ($element->length > 0)
				&& ($$VARNAME = $element->item(0)->nodeValue);
		}
		
		// Include navigation and siteMapPath
		foreach (BeanFactory :: getBeanById('configurationManager')->getHTMLNavigations() as $name => $navigation) {
			$$name = $navigation;
		}
		
		// Show generated page
		include (BeanFactory :: getBeanById('site')->getTemplate());
	}
}
?>