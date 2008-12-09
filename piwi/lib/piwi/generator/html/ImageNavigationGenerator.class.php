<?php
/**
 * Creates a image based navigation. Makes use of javascript to choose between selected, hover
 * and normal images. Please use the nested tag:
 * <pathToImages><pathToImages> to define the path to your navigation images.
 * 
 * For example:
 * <navigationGenerator name="HTML_NAVIGATION" class="ImageNavigation" depth="0" includeParent="1">
 *	<pathToImages>custom/templates/images</pathToImages>
 * </navigationGenerator>
 */
class ImageNavigationGenerator implements NavigationGenerator {
	var $pathToImages = "";
	
	/**
	 * Constructor.
	 */
	public function __construct() {
	}
	public function generate(array $siteElements = null) {
		return $this->getNavigation($siteElements);
		
	}
	
	/**
     * Returns the recursivly built navigation.
     * @param array $navigationElements The navigation which is an array of NavigationElements representing the website structure.
     * @return string The recursivly built navigation.
     */
    private function getNavigation($navigationElements) {
    	if ($navigationElements == null) {
    		return '';
    	} else {
    		$result = '<ul>';
    		
    		foreach ($navigationElements as $element) {
				if (!$element->isHiddenInNavigation()) {
					$filepath = $element-> getId() . ".html";
					if (substr($element->getFilePath(), 0, 4) == 'http') {
						$filepath = $element->getFilePath();
					}
					
					$open = "";
					if ($element->isOpen()) {
						$open = "-selected";
					}
					
    				$result .= '<li>';
    				$result .= '<a onmouseover="document.images[\''. $element->getId() .'\'].src=\''.$this->pathToImages.'/' . $element->getId() . '-hover.jpg\';" ';
    				$result .= 'onmouseout="document.images[\''. $element->getId() .'\'].src=\''.$this->pathToImages.'/' . $element->getId() .$open.'.jpg\';" ';
					$result .= 'href="' . $filepath . '">';
    				$result .= '<img name="'. $element->getId() .'" src="'.$this->pathToImages.'/' . $element->getId() . $open.'.jpg" />';
					$result .= '</a></li>';
				}
    		}
    		
    		$result .= '<ul>';
    		return $result;
    	}
    }
    
    
	   
    /**
	 * Used to pass parameters to the Generator.
	 * @param string $key The name of the parameter.
	 * @param string $value The value of the parameter.
	 */
    public function setProperty($key, $value) {
    	if($key == "pathToImages") {    	
    		$this->pathToImages = $value;
    	}
    }
}
?>