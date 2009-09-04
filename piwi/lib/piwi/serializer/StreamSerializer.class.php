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
		
		$file = null;
		$name = null;

		foreach ($elements as $item) {			
			if ($item->hasAttributes()) {
				$file = $item->getAttribute('file');
				$name = $item->getAttribute('name');
				break;
			}
		}

		header("Content-type: application/octet-stream"); 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		
		if ($name !== null) {
 			header('Content-Disposition: attachment; filename="' . $name . '"');
		} else {
 			header('Content-Disposition: attachment; filename="' . $file . '"');
		}
		
		readfile($file);
	}
}
?>