<?
class XMLPage {
	var $source;
	var $dom;
	var $simpleXML;
	
	public function XMLPage($source) {
		$this->source = $source;
		$this->dom = new DOMDocument;		$this->dom->load($this->source);
		$this->simpleXML = simplexml_import_dom($this->dom);
	}

	public function sourceString() {
		return $this->simpleXML->asXML();
	}
	
	public function transform() {
		$xsl = new DOMDocument;		$xsl->load("resources/xslt/document-v1.0.xsl");		// Configure the transformer		$proc = new XSLTProcessor;
		$proc->registerPHPFunctions();		$proc->importStyleSheet($xsl); // attach the xsl rules
		// $xml = $proc->transformToXML($this->dom);
		
		$r = new DOMDocument($xml);
		$elements = $this->dom->getElementsByTagName('content');

		for ($i = 0; $i < $elements->length; $i++) {
			$s = simplexml_import_dom($elements->item($i));
			
			$template = new DOMDocument();
			$template->loadXML($s->asXML());
			$result[$elements->item($i)->getAttribute("position")] = $proc->transformToXML($template);
		}
		return $result;
	}
	
	public static function transformPart($xslFile, $xml) {
		$xslt = new xsltProcessor;
		$xslt->registerPHPFunctions();
		$xslt->importStyleSheet(DomDocument::load($xslFile));
		return $xslt->transformToXML(DomDocument::loadXML($xml));
	}
}
?>