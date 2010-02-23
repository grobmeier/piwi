<?php
/**
 * Represents a page item.
 */
class SiteElement {
	/** The id of the page. */
	private $id = null;
	
	/** The label of the page, which will be displayed. */
	private $label = null;
	
	/** The url of the content. */
	private $filePath = null;
	
	/** Indicates whether the item is shown in the Navigation. */
	private $hideInNavigation = false;
	
	/** Indicates whether the item is shown in the SiteMap. */
	private $hideInSiteMap = false;
	
	/** The children. */
	private $children = null;
	
	/** The parent SiteElement. */
	private $parent = null;
	
	/** Indicates whether the children are currently visible. */
	private $open = false;
	
	/** Indicates whether the SiteElement is currently selected. */
	private $selected = false;
	
	/** array of roles configured for this SiteElement */
	private $roles = array();
	
	/**
	 * Constructor.
	 * @param string $id The id of the page.
	 * @param string $label The label of the page, which will be displayed.
	 * @param string $filePath The url of the content. 
	 */
	public function __construct($id, $label, $filePath) {
		$this->id = $id;
		$this->label = $label;
		$this->filePath = $filePath;
	}
	
	/**
	 * Returns the id of the page.
	 * @return string The id.
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * Returns the label of the page.
	 * @return string The label of the page.
	 */
	public function getLabel(){
		return $this->label;
	}

	/**
	 * Returns the url of the content.
	 * @return string The url of the content.
	 */
	public function getFilePath(){
		return $this->filePath;
	}
	
	/**
	 * Returns the roles of this site element as array
	 * @return array A array with roles for this site element
	 */
	public function getRoles() {
	    return $this->roles;
	}
			
	/**
	 * Sets roles for this site element. An array is expected.
	 * If a string is give, the string will be splitted on "," 
	 * and the resulting array is used as roles array.
	 * @param mixed $roles either an array with roles or a string
	 * 				with comma separated roles
	 */
	public function setRoles($roles) {
	    if(is_array($roles)) {
	        $this->roles = $roles;
	    } else {
	    	$this->roles = explode(',',$roles); 
	    }
	}
	
	/**
	 * Indicates whether the item is shown in the Navigation.
	 * @return boolean True if Element should be hidden in Navigation.
	 */
	public function isHiddenInNavigation() {
		return $this->hideInNavigation;
	}
	
	/**
	 * Indicates whether the item is shown in the Navigation.
	 * @param boolean $hideInNavigation True if Element should be hidden in Navigation.
	 */
	public function setHiddenInNavigation($hideInNavigation) {
		return $this->hideInNavigation = $hideInNavigation;
	}
	
	/**
	 * Indicates whether the item is shown in the SiteMap.
	 * @return boolean True if Element should be hidden in SiteMap.
	 */
	public function isHiddenInSiteMap() {
		return $this->hideInSiteMap;
	}
	
	/**
	 * Indicates whether the item is shown in the SiteMap.
	 * @param boolean $hideInSiteMap True if Element should be hidden in SiteMap.
	 */
	public function setHiddenInSiteMap($hideInSiteMap) {
		return $this->hideInSiteMap = $hideInSiteMap;
	}
	
	/**
	 * Returns the children of the SiteElement.
	 * @return array The children.
	 */
	public function getChildren(){
		return $this->children;
	}
			
	/** Sets the children
	 * @param array $children The children to add.
	 */
	public function setChildren(array $children = null) {
		$this->children = $children;
	}
	
	/**
	 * Indicates whether the children are currently visible.
	 * @return boolean True if open.
	 */
	public function isOpen() {
		return $this->open;
	}
	
	/**
	 * Sets the SiteElement to open.
	 * @param boolean $open Indicates whether the children are currently visible.
	 */
	public function setOpen($open) {
		return $this->open = $open;
	}
	
	/**
	 * Indicates whether the SiteElement is currently selected.
	 * @return boolean True if selected.
	 */
	public function isSelected() {
		return $this->selected;
	}
	
	/**
	 * Sets the SiteElement to selected.
	 * @param boolean $selected Indicates whether the SiteElement is currently selected.
	 */
	public function setSelected($selected) {
		return $this->selected = $selected;
	}
	
	/**
	 * Returns the parent SiteElement.
	 * @return SiteElement The parent SiteElement.
	 */
	public function getParent() {
		return $this->parent;
	}
	
	/**
	 * Sets the parent SiteElement.
	 * @param SiteElement $selected the parent SiteElement.
	 */
	public function setParent(SiteElement $parent) {
		return $this->parent = $parent;
	}
}
?>