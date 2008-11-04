<?php
/**
 * Serializes the given XML to HTML.
 */
class HTMLSerializer implements Serializer {
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
		$processor->importStyleSheet(DOMDocument::load("resources/xslt/HTMLTransformation-v1.0.xsl"));

		$elements = $domDocument->getElementsByTagName('content');

		for ($i = 0; $i < $elements->length; $i++) {
			$simplexml = simplexml_import_dom($elements->item($i));

			$template = new DOMDocument();
			$template->loadXML($simplexml->asXML());

			$CONTENT[$elements->item($i)->getAttribute("position")] = $processor->transformToXML($template);
		}
		
		// Generate navigation
		$navigationGenerator = Site::getInstance()->getNavigationGenerator();
		$siteMap = Site::getInstance()->getCustomSiteMap(null, 1);
		$HTML_NAVIGATION = $navigationGenerator->generate($siteMap);			
		
		// Generate siteMapPath
		$siteMapPathNavigation = new SiteMapPathNavigation();
		$siteMap = Site::getInstance()->getCustomSiteMap($pageId, 0);
		$SITE_MAP_PATH = $siteMapPathNavigation->generate($siteMap);
			
		// Show generated page
		include ($templatePath);
	}
}
?>