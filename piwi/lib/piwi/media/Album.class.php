<?php
/**
 * Represents an photoalbum (a set of images).
 */
class Album {
	/** The name of the album. */
	private $name = null;
	
	/** The images in the album. */
	private $images = array();
	
	/** The date when the album has been created. */
	private $createdAt = null; 
	
	/**
	 * Constructor.
	 * @param string $name The name of the album.
	 */
	public function __construct($name) {
		$this->name = $name;
	}
	
	/**
	 * Returns the name of the album.
	 * @return string The name of the album.
	 */
	public function getName() {
		return $this->name;	
	}
	
	/**
	 * Adds an image to the album.
	 * @param string $image The image to add.
	 */	
	public function addImage($image) {
		array_push($this->images, $image);
	}
	
	/**
	 * Returns the images in the album.
	 * @return array The images in the album.
	 */
	public function getImages() {
		return $this->images;
	}

	/**
	 * Returns the date when the album has been created.
	 * @return int The date when the album has been created.
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * Sets the date when the album has been created.
	 * @return int $date The date when the album has been created.
	 */
	public function setCreatedAt($date) {
		$this->createdAt = $date;
	}
}
?>