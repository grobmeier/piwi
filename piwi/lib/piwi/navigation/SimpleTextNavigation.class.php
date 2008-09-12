<?php
/**
 * Creates a simple navigation.
 */
class SimpleTextNavigation implements Navigation {
	/**
	 * Constructor.
	 */
	public function __construct() {
	}
	
	/**
	 * Builds the navigation.
	 * @param string $contentPath Name of the folder where the content is placed.
	 * @param array $navigation The pages of the website.
	 * @return string The navigation as HTML.
	 */
	public function generate($contentPath, $navigation) {
		$topNav = "";
		$navString = "";
		
		foreach($navigation as $parent) {
			$linkid = $parent['id'];
			
			$topLink = "";
		   	if(strpos($parent['href'], "http://") !== false) {
		   		$topLink .= "<a class=\"links\" href=\"".$parent['href']."\">".$parent['label']."</a>&nbsp;&nbsp;";
			} else {
   				$topLink .= "<a class=\"links\" href=\"".$linkid.".html\">".$parent['label']."</a>&nbsp;&nbsp;";
			}
			
			if(isset($parent['childs'])) {
				foreach($parent['childs'] as $entry) {
					if(strpos($entry['href'], $contentPath) !== false) {
				    	$link = str_replace($contentPath, "", $entry['href']);
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