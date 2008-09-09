<?php
abstract class SiteGenerator implements Generator {	
   	
    function SiteGenerator() {
    }
   
    function generate($output = "html") {
		$piwixml = "<?xml version='1.0' encoding='utf-8' standalone='yes'?>";
		$piwixml .= "<!DOCTYPE document PUBLIC \"-//PIWI//DTD Documentation V1.0//EN\" \"dtd/document-v10.dtd\">";
		$piwixml .= "<document>";
		$piwixml .= "<header>";
		$piwixml .= "<keywords>PIWI - PHP Transformation Framework</keywords>";
		$piwixml .= "<title>PIWI - PHP Transformation Framework</title>";
		$piwixml .= "</header>";

		$piwixml .= "<content position='article'>";
		
		$piwixml .= $this->generateContent();

		$piwixml .= "</content>";
		$piwixml .= "</document>";
		return $piwixml;
	}
	
	abstract protected function generateContent();
	
    function setProperty($key,$value){    	
    }
}
?>