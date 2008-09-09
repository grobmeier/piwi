<?php
class ExceptionPageGenerator extends SiteGenerator {
	
   	private $exception;
   	
    function ExceptionPageGenerator($exception) {
    	$this->exception = $exception;
    }
   
    protected function generateContent($output = "html") {
		$piwixml .= "<header>Error</header>";
		$piwixml .= "<section>";
		$piwixml .= "<p>";
		$piwixml .= $this->exception->getMessage();
		$piwixml .= "</p>";
		$piwixml .= "</section>";
		return $piwixml;
	}
	
    function setProperty($key,$value) {
    }
}
?>