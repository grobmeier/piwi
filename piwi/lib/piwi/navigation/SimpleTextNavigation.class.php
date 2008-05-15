<?php
class SimpleTextNavigation implements Navigation {
	
	var $pathToContent = "";
	
	function SimpleTextNavigation($pathToContent) {
		$this->$pathToContent = $pathToContent;	
	}
	
	function build($nav) {
		$topNav = "";
		$navString = "";
		
		foreach($nav as $parent) {
			$linkid = $parent['id'];
			
			$topLink = "";
		   	$topLink .= "<a class=\"links\" href=\"".$linkid.".html\">".$parent['label']."</a>&nbsp;&nbsp;";
		   
			if($parent['childs'] != null) {
				foreach($parent['childs'] as $entry) {
					if(strpos($entry['href'], $this->$pathToContent) !== false) {
				    	$link = str_replace($this->$pathToContent, "", $entry['href']);
				    }
		    		        	
				    $link = str_replace(".xml", ".html", $link);
				    $navString .= "<a href=\"".$entry['id'].".html\">".$entry['label']."</a><br>";
				    $target = "";
				}
			}
			$topNav .= $topLink;
			$topNav .= "\n";
		}
		return $topNav;
	}
}

?>