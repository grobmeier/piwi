<?
class XMLPage {
	private $dom;
	private $template = "";

	public function XMLPage($source = null) {
		$this->dom = new DOMDocument;
		if ($source != null) {			
			$this->dom->load($source);
		}
	}

	public function getTemplate() {
		return $this->template;
	}
	
	public function setTemplate($name) {
		$this->template = $name;
	}

	public function setDom($dom) {
		$this->dom->loadXML($dom);
	}

	public function transform() {
		$xsl = new DOMDocument;
		$xsl->load("resources/xslt/document-v1.0.xsl");
		// Configure the transformer
		$proc = new XSLTProcessor;
		$proc->registerPHPFunctions();
		$proc->importStyleSheet($xsl); // attach the xsl rules
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
		$xslt->importStyleSheet(DomDocument :: load($xslFile));
		return $xslt->transformToXML(DomDocument :: loadXML($xml));
	}
}
?>