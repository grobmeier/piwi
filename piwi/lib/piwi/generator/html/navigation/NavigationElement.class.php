<?php
/**
 * Creates a simple navigation.
 */
class NavigationElement {
	/** The id of the page. */
	private $id = null;
	
	/** The label of the page, which will be displayed. */
	private $label = null;
	
	/** The children. */
	private $children = null;
	
	/** The parent NavigationElement. */
	private $parent = null;
	
	/** Indicates whether the children are currently visible. */
	private $open = false;
	
	/** Indicates whether the NavigationElement is currently selected.  */
	private $selected = false;
	
	/**
	 * Constructor.
	 * @param string $id The id of the page.
	 * @param string $label The label of the page, which will be displayed.
	 */
	public function __construct($id, $label) {
		$this->id = $id;
		$this->label = $label;
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
	 * @return string The label.
	 */
	public function getLabel(){
		return $this->label;
	}
	
	/**
	 * Returns the children of the NavigationElement.
	 * @return array The children.
	 */
	public function getChildren(){
		return $this->children;
	}
			
	/** Sets the children
	 * @param array $children The children to add.
	 */
	public function setChildren($children) {
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
	 * Sets the NavigationElement to open.
	 * @param boolean $open Indicates whether the children are currently visible.
	 */
	public function setOpen($open) {
		return $this->open = $open;
	}
	
	/**
	 * Indicates whether the NavigationElement is currently selected.
	 * @return boolean True if selected.
	 */
	public function isSelected() {
		return $this->selected;
	}
	
	/**
	 * Sets the NavigationElement to selected.
	 * @param boolean $selected Indicates whether the NavigationElement is currently selected.
	 */
	public function setSelected($selected) {
		return $this->selected = $selected;
	}
	
	/**
	 * Returns the parent NavigationElement.
	 * @return NavigationElement The parent NavigationElement.
	 */
	public function getParent() {
		return $this->parent;
	}
	
	/**
	 * Sets the parent NavigationElement.
	 * @param NavigationElement $selected the parent NavigationElement.
	 */
	public function setParent(NavigationElement $parent) {
		return $this->parent = $parent;
	}
}
?>