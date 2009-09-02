<?php
/**
 * Serializes the given XML to Excel (XSL).
 */
class StreamSerializer implements Serializer {
	/**
	 * Transform the given xml to the output format.
	 * @param DOMDocument $domDocument The content as DOMDocument.
	 */
	public function serialize(DOMDocument $domDocument) {
		$elements = $domDocument->getElementsByTagName('stream');
		
		$stream = null;
		foreach ($elements as $item) {
			if($item->hasAttributes()) {
				$stream = $item->getAttribute('file');
				break;
			}
		}
		
		
		header("Content-type: application/octet-stream"); 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
 		header('Content-Disposition: attachment; filename="'.$stream.'"');
 		
		echo $stream;
	}
}
?>