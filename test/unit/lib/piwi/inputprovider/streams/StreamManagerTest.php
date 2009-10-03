<?php
class StreamManagerTest extends UnitTestCase {

	function testProperties() {
		$sm = new StreamManager();
		$sm->setStreamConfiguration(__DIR__ . '/streams.xml');
		$a = $sm->getStreamInfo('image1');
		
		$this->assertEqual('custom/files/gallery/Nature/originals/1.jpg', $a->uri, 'URI does not match.');
		$this->assertEqual('1.jpg', $a->name, 'Name does not match.');
	}
	
}
?>