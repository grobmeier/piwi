<?php
/**
 * Provides helper functions to generate SiteMaps.
 */
class SiteMapHelper {
	/**
	 * Returns the 'SiteMap' which is an array of SiteElements representing the website structure.
	 * It is possible to retrieve only certain parts of the 'SiteMap'.
	 * @param string $id Returns only the SiteElement with this id and its subitems. 
	 * Set to 'null' if all SiteElements should be returned.
	 * @param integer $depth Determinates the maximum number of subitems that will be returned. 
	 * Set to '-1' for full depth, to '0' only for parent items. 
	 * @param boolean $includeParent If false only the children of the parent will be returned. 
	 * This can only be used if $id is not 'null'.
	 * @return The 'SiteMap' which is an array of SiteElements representing the website structure.
	 */
	public static function getCustomSiteMap($id, $depth, $includeParent = true) {
		$siteMap = Site :: getInstance()->getFullSiteMap();

		// Build menu
		// If id is specified show only this item and its subitems
		if ($id != null) {
			$siteElements = array ();

			// retrieve the SiteElement with the given id
			$element = self::getSiteMapItemsById($siteMap, $id);

			// if parent should be not included show only its children otherwise the parent itself
			if ($element != null && $includeParent) {
				$siteElements[0] = $element;
			} else if ($element != null && $element->getChildren() != null) {
					foreach ($element->getChildren() as $element) {
						$siteElements[] = $element;
					}
			}
		} else {
			$siteElements = $siteMap;
		}

		// If depth is greater than -1, cut off subitems
		if ($depth >= 0) {
			foreach ($siteElements as $element) {
				self :: cutOffSiteMap(0, $depth, $element);
			}
		}
		return $siteElements;
	}

	/**
	 * Returns the SiteElement with the given in id or null if no item is found.
	 * @param array $siteMap The array of SiteElements to search in.
	 * @param string $id The id of the SiteElement.
	 * @param SiteElement $foundElement The SiteElement with the given in id or null 
	 * if no item has been found yet.
	 * @return SiteElement The SiteElement with the given in id or null if no item is found.
	 */
	private static function getSiteMapItemsById($siteMap, $id, SiteElement $foundElement = null) {
		foreach ($siteMap as $element) {
			if ($element->getId() == $id) {
				$foundElement = $element;
				break;
			} else if ($element->getChildren() != null) {
					$foundElement = self :: getSiteMapItemsById($element->getChildren(), $id, $foundElement);
			}
		}
		return $foundElement;
	}

	/**
	 * Removes all subitems in the given SitenElement that exceed the given depth.
	 * @param integer $currentDepth The current depth.
	 * @param integer $depth description The desired depth.
	 * @param SiteElement $siteElement The SiteElement whose children should be cut off.
	 */
	private static function cutOffSiteMap($currentDepth, $depth, SiteElement $siteElement) {
		if ($currentDepth++ >= $depth) {
			$siteElement->setChildren(null);
		} else {
			if ($siteElement->getChildren() != null) {
				foreach ($siteElement->getChildren() as $element) {
					self::cutOffSiteMap($currentDepth, $depth, $element);
				}
			}
		}
	}
}
?>