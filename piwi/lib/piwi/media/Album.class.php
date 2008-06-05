<?
class Album {
	var $name;
	var $images;
	var $createdAt; 
	
	function Album($name) {
		$this->images = array();
		$this->name = $name;
	}
	
	function setCreatedAt($t) {
		$this->createdAt = $t;
	}
	
	function getCreatedAt() {
		return $this->createdAt;
	}
	
	function addImage($image) {
		array_push($this->images, $image);
	}
	
	function getName() {
		return $this->name;	
	}
	
	function getImages() {
		return $this->images;
	}
}
?>