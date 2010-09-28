<?php
class MediaCenterTest extends UnitTestCase {

	private $dir = null;
	
	function setUp() {
		$this->dir = dirname(__FILE__) . '/data';
	}
	
	function testGetAlbums() {
		$mediaCenter = new MediaCenter($this->dir);
		
		$albums = $mediaCenter->getAlbums();
		$this->assertEqual(2, sizeof($albums), 'Size does not match.');
			
		foreach ($albums as $album) {
       		$this->assertIsA($album, 'Album', 'Type does not match.');
       		$this->assertEqual(3, sizeof($album->getImages()), 'Size does not match.');
		}
	}
}
?>