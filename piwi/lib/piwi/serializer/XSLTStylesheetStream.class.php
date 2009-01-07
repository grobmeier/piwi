<?php
/**
 * A Stream used to embed custom XSLT-Transformations into PIWI-XML.
 */
class XSLTStylesheetStream {
	/** The current position in the streamed document. */
	private $position = 0;

	/** The document to stream. */
	private $template = '<?xml version="1.0" encoding="UTF-8"?><xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"></xsl:stylesheet>';

	/**
	 * Opens the stream.
	 * @param string $path The path to open.
	 * @param string $mode The mode in which the stream should be opened.
	 * @param integer $options The options.
	 * @param string &$opened_path Reference to the path that is actually open.
	 */
	public function stream_open($path, $mode, $options, & $opened_path) {
		// Check if a custom stylesheet is specified.
		// If yes, then stream this one, otherwise stream an empty stylesheet.
		$customXSLTStylesheetPath = ConfigurationManager::getInstance()->getCustomXSLTStylesheetPath();
		if ($customXSLTStylesheetPath != null && file_exists($customXSLTStylesheetPath)) {
			$stylesheet = '';

			$file = fopen($customXSLTStylesheetPath, "r");
			while (!feof($file)) {
				$stylesheet .= fgets($file, 1024);
			}
			fclose($file);

			$this->template = $stylesheet;
		}
		return true;
	}

	/**
	 * Returns the given number of characters starting at the current position.
	 * @param integer $count The number of characters to return.
	 * @return string The desired characters.
	 */
	public function stream_read($count) {
		$result = substr($this->template, $this->position, $count);
		$this->position += $count;
		return $result;
	}

	/**
	 * Should store the given data, but does nothing, since this functionality is not required here.
	 * @param string $data The data to store.
	 */
	public function stream_write($data) {
		return 0;
	}

	/**
	 * Returns the current position in the stream.
	 * @return integer The current position in the stream.
	 */
	public function stream_tell() {
		return $this->position;
	}

	/**
	 * Returns true if the end of the streamed file is reached.
	 * @return boolean True if the end of the streamed file is reached.
	 */
	public function stream_eof() {
		return $this->position >= strlen($this->template);
	}

	/**
	 * Returns an array containing as many elements in common with the system function as possible.
	 * @param string $path The path to open.
	 * @param integer $flags Holds additional flags set by the streams API.
	 * @return array description An array containing as many elements in common with 
	 * the system function as possible
	 */
	public function url_stat($path, $flags) {
		return array ();
	}
}
?>