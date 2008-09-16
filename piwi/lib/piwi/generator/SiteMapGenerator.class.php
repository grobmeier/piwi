<?php
/**
 * Generates a sitemap of the website.
 */
final class SiteMapGenerator implements Generator {
	/** Shows only the NavigationElement with this id and its subitems. Set to 'null' if all NavigationElements should be shown. */
	private $pageId = null;
	
	/** Determinates the maximum number of subitems that will be returned. Set to '-1' for full depth, to '0' only for parent items. */
	private $depth = -1;
	
	/** If false only the children of the parent will be shown. This can only be used if $pageId is not 'null'. */
	private $includeParent = true;

	/** The header of the section. */
	private $header = "";
	
   	/**
   	 * Constructor.
   	 */
    public function __construct() {
    }
   
	/**
	 * Generates the sections that will be placed as content.
	 * @return string The xml output as string.
	 */
    public function generate() {
		$piwixml = '<?xml version="1.0"?>';
		$piwixml .= '<!DOCTYPE document PUBLIC "-//PIWI//DTD Documentation V1.0//EN" "dtd/document-v10.dtd">';
		$piwixml .= '<document>';
		$piwixml .= '<section>';
		$piwixml .= '<title>';
		$piwixml .= $this->header;
		$piwixml .= '</title>';
		$piwixml .= '<p>';
		$piwixml .= $this->getSiteMapAsXml(Site::getInstance()->getCustomSiteMap($this->pageId, $this->depth, $this->includeParent));
		$piwixml .= '</p>';
		$piwixml .= '</section>';
		$piwixml .= '</document>';
		
		return $piwixml;
	}
	
	/** Used to pass parameters to the Generator.
	 * @param string $key The name of the parameter.
	 * @param object $value The value of the parameter.
	 */
    public function setProperty($key, $value) {
    	if($key == "pageId") {
    		$this->pageId = $value;
    	} else if($key == "depth") {
    		$this->depth = $value;
    	} else if($key == "includeParent") {
    		$this->includeParent = $value;
    	} else if($key == "header") {
    		$this->header = $value;
    	} 
    }
    
    /**
     * Returns the given 'SiteMap' as xml.
     * @param array $siteMap The 'SiteMap' which is an array of NavigationElements representing the website structure.
     * @return string The given 'SiteMap' as xml.
     */
    private function getSiteMapAsXml($siteMap) {
    	if ($siteMap == null) {
    		return '';
    	} else {
    		$result = '<ul>';
    		
    		foreach ($siteMap as $element) {
    			$result .= '<li><a href="' . $element-> getId() . '.html">' . $element->getLabel() . '</a>' . $this->getSiteMapAsXml($element->getChildren()) . '</li>';
    		}
    		
    		$result .= '</ul>';
    		return $result;
    	}
    }
}
?>