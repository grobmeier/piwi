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
		$parameters = Request :: getParameters();
		$fileId = $parameters['fileId'];

		if ($fileId != null) {			
			foreach ($domDocument->getElementsByTagName('stream') as $element) {
       			if ($element->hasAttribute('id') && $element->getAttribute('id') == $fileId) {
       								$file = $element->getAttribute('file');
					$name = $fileId;
				
					if ($element->hasAttribute('name')) {
						$name =  $element->getAttribute('name');
					}
				
					$this->_streamFile($file, $name);
       			}
			}
		}
	}
	
	/**
	 * Streams the given file.
	 * @param string $filepath The path of the file.
	 * @param string $name The name of the file.
	 */
	private function _streamFile($filepath, $name) {
		header("Content-type: application/octet-stream"); 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		
 		header('Content-Disposition: attachment; filename="' . $name . '"');
 		
 		readfile($filepath);
	}
}
?>