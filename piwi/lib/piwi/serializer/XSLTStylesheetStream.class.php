<?php
class XSLTStylesheetStream {
    private $position = 0;
    private $template = '<?xml version="1.0" encoding="UTF-8"?><xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"></xsl:stylesheet>';

    public function stream_open($path, $mode, $options, &$opened_path)
    {
    	$customXSLTStylesheetPath = Site::getInstance()->getCustomXSLTStylesheetPath();
		if ($customXSLTStylesheetPath != null && file_exists($customXSLTStylesheetPath)) {
			$stylesheet = '';
			
			$file = fopen($customXSLTStylesheetPath, "r");
			while (!feof($file))
			{
				$stylesheet .= fgets($file, 1024);	
			}
			fclose($file); 
			
			$this->template = $stylesheet;
		}
        
        return true;
    }

    public function stream_read($count)
    {
        $result = substr($this->template, $this->position, $count); 
        $this->position += $count;   
        return $result;
    }

    public function stream_write($data)
    {
        return 0;
    }

    public function stream_tell()
    {
        return $this->position;
    }

    public function stream_eof()
    {
        return $this->position >= strlen($this->template);
    }
	
	public function url_stat($path, $flags) {
		return array();
	}
}
?>