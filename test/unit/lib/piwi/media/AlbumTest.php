<?php
require_once ('test/PiwiTestCase.php');

class AlbumTest extends PiwiTestCase {

	private	$name = 'test';
	private $time = null;
	
	function setUp() {
		$this->time = time();
	}
	
	function testProperties() {
		$album = new Album($this->name);
		$album->setCreatedAt($this->time);
		
		$this->assertEqual($this->name, $album->getName(), 'Name does not match.');
		$this->assertEqual($this->time, $album->getCreatedAt(), 'Date does not match.');
	}
	
	function testAddImage() {
		$album = new Album($this->name);
		$album->setCreatedAt($this->time);
		
		$album->addImage('test1');
		$album->addImage('test2');
		$album->addImage('test3');
		
		$images = $album->getImages();
		$this->assertEqual(3, sizeof($images), 'Size does not match.');
		$this->assertEqual('test1', $images[0], 'Image does not match.');
		$this->assertEqual('test2', $images[1], 'Image does not match.');
		$this->assertEqual('test3', $images[2], 'Image does not match.');
	}
}
?>